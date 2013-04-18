<?php
// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Setup the path related constants.
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', realpath(dirname(__FILE__) . DS . '..' . DS . 'document-root'));
define('JPATH_ROOT', JPATH_BASE);
define('JPATH_CONFIGURATION', JPATH_BASE);
define('JPATH_LIBRARIES', JPATH_BASE . DS . 'libraries');
define('JPATH_METHODS', JPATH_ROOT . DS . 'methods');

// Load the library importer.
require_once (JPATH_LIBRARIES . DS . 'joomla' . DS . 'import.php');
require_once (JPATH_CONFIGURATION . DS . 'configuration.php');
include_once( JPATH_ROOT.DS.'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php' );

// main application dependencies
jimport('joomla.application.application');
jimport('joomla.utilities.utility');
jimport('joomla.language.language');
jimport('joomla.utilities.string');
jimport('joomla.factory');

// configuration
jimport('joomla.registry.registry');

// component dependencies
jimport('joomla.application.component.model');
jimport('joomla.application.component.helper');
jimport('joomla.html.parameter');

// load framework config and set JConfig values
$framework =& JFactory::getConfig();
$framework->loadObject(new JConfig);

// set error reporting to fatals to keep the logs clean
if($framework->getValue('app_environment') != 'development') {
	ini_set('error_reporting', E_ERROR | E_USER_ERROR);
}

// cli object
abstract class TopBettaCLI {
	/**
	 * Default log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_NORMAL = 0;

	/**
	 * Debug log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_DEBUG = 1;

	/**
	 * Log message type for errors
	 *
	 * @var integer
	 */
	const LOG_TYPE_ERROR = 2;

	/**
	 * Default time formatting string for log messages
	 *
	 * @var string
	 */
	const LOG_TIME_FORMAT_DEFAULT = 'r';

	/**
	 * Root URL for XML feed data
	 *
	 * @var string
	 */
	const XML_ROOT_DEFAULT = 'http://unitab.com/data/';

	/**
	 * The timezone in which feed times arrive
	 *
	 * @var string
	 */
	const TZ_FEED = 'Australia/Brisbane';

	/**
	 * The timezone of the application
	 *
	 * @var string
	 */
	const TZ_LOCAL = 'Australia/Sydney';

	/**
	 * Debugging mode flag
	 *
	 * @var boolean
	 */
	private $debug = false;

	/**
	 * XML handle
	 *
	 * @var resource
	 */
	private $xml_doc = null;

	/**
	 * A signature used to prevent reloading of an XML file if it's already in use
	 *
	 * @var string
	 */
	private $xml_signature = null;

	/**
	 * Stores any arguments passed to a script
	 *
	 * @var array
	 */
	protected $arg_list = array();

	/**
	 * Start time of the script
	 *
	 * @var float
	 */
	private $start_time;

	/**
	 * The class name for use as the script name
	 *
	 * @var string
	 */
	private $job_name;

	/**
	 * Max script execution time of script if set
	 *
	 * @var int
	 */
	protected $max_execution_time = null;

	/**
	 * Max script execution time of script if set
	 *
	 * @var int
	 */
	private $expiry_time = null;

	/**
	 * Starts a timer, sets up log messages and imports CLI arguments
	 *
	 * @param array $arguments
	 * @param array $required_arguments
	 */
	final public function __construct($arguments = null, $required_arguments = array()) {
		$this->start_time 	= microtime(true);
		$this->job_name		= get_class($this);

		if(!is_null($this->max_execution_time)){
			$this->_setExecutionExpiryTime($this->max_execution_time);
		}

		$this->arg_list = $this->_getArguments($arguments, $required_arguments);
		$this->l("Starting {$this->job_name} Job");

		if(method_exists($this, 'initialise')) {
			$this->initialise();
		}
	}

	/**
	 * Write completion messages to stdout
	 */
	final public function __destruct() {
		$this->l("Finishing {$this->job_name} Job");

		$time_taken = microtime(true) - $this->start_time;
		$this->l("Time Taken: {$time_taken} sec");

		$memory = round((memory_get_peak_usage(true) / 1024 / 1024), 2);
		$this->l("Peak memory allocation: {$memory}MB");
	}

	/**
	 * Log a message to stdout
	 *
	 * @param string 	$message
	 * @param integer 	$type
	 * @param string 	$time_format
	 * @param boolean 	$add_new_line
	 */
	public function l($message, $type = null, $time_format = null, $add_new_line = true) {
		if(is_null($type)) {
			$type = self::LOG_TYPE_NORMAL;
		}

		if($type == self::LOG_TYPE_DEBUG && $this->debug == FALSE){
			return 0;
		}

		$time = $this->_formatLogTime($time_format);
		$processPID = getmypid();
		
		$prefix = array(
			self::LOG_TYPE_NORMAL => '[PID:'.$processPID . '] ',
			self::LOG_TYPE_DEBUG => 'Debug: ',
			self::LOG_TYPE_ERROR => 'Error: '
		);

		$suffix = ($add_new_line) ? "\n" : '';

		echo sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix);
	}

	/**
	 * Dump mixed output as a string to stdout
	 *
	 * @param mixed $dump
	 * @return void
	 */
	public function d($dump) {
		if(is_array($dump) || is_object($dump)) {
			$dump = print_r($dump, true);
		}

		$this->l($dump, self::LOG_TYPE_DEBUG);
	}

	/**
	 * Output an error to stdout
	 *
	 * @param string $message
	 */
	public function e($message) {
		$this->l($message, self::LOG_TYPE_ERROR);
	}

	/**
	 * Format the timestamp for a log message
	 *
	 * @param string $format
	 */
	private function _formatLogTime($format = null) {
		if(is_null($format)) {
			$format = self::LOG_TIME_FORMAT_DEFAULT;
		}

		return '[' . date($format) . ']';
	}

	/**
	 * Set the internal debug flag
	 *
	 * @param boolean $status
	 */
	public function debug($status = null) {
		if(!is_null($status)) {
			if($status !== true && $status !== false) {
				$status = false;
			}

			$this->debug = $status;
		}

		return $this->debug;
	}

	/**
	 * Pause script execution - mainly for debugging
	 *
	 */
	public function pause(){
		$handle = fopen ("php://stdin","r");
		$line = trim(fgets(STDIN));
	}

	/**
	 * Get the XML feed root from server.xml
	 *
	 * @return string
	 */
	private function _getXMLRoot() {
		static $xml_root = null, $default_xml_root = null;

		if(is_null($xml_root)) {
			$section = $this->_getConfigSection('datafeed');
			foreach($section as $node) {
				$name = (string)$node->attributes()->name;
				if($name == 'default') {
					$default_xml_root = (string)$node->children()->host;
				}

				if($name == 'topbetta') {
					$xml_root = (string)$node->children()->host;
				}
			}

			if(is_null($xml_root) && !is_null($default_xml_root)) {
				$xml_root = $default_xml_root;
			}
		}

		return $xml_root;
	}

	/**
	 * Populate the internal XML handle
	 *
	 * @param string 	$file
	 * @param bool		$force_reload
	 */
	public function loadXML($file, $force_reload = false) {
		$xml_root = $this->_getXMLRoot();

		if(is_null($xml_root)) {
			$xml_root = self::XML_ROOT_DEFAULT;
		}

		$file_path = $xml_root . $file;
		if(!$this->_checkXMLSignature($file_path) || $force_reload) {
			$this->d('Signature mismatch, loading a new XML file');
			$simple_xml = simplexml_load_file($file_path);

			if(!$simple_xml) {
				$this->l("Could not load XML from path {$file_path}", self::LOG_TYPE_ERROR);

				$this->xml_doc 			= null;
				$this->xml_signature 	= $this->_setXMLSignature(null);
			} else {
				$this->xml_doc 			= $simple_xml;
				$this->xml_signature 	= $this->_setXMLSignature($file_path);
			}
		}

		return $this->xml_doc;
	}

	/**
	 * Hash the current XML file URL and set the xml_signature member
	 *
	 * @param string $file_path
	 * @return void
	 */
	private function _setXMLSignature($file_path) {
		$this->xml_signature = md5($file_path);
	}

	/**
	 * Compare the supplied XML file URL with the current signature
	 *
	 * @param string $file_path
	 * @return bool
	 */
	private function _checkXMLSignature($file_path) {
		return (md5($file_path) == $this->xml_signature);
	}

	/**
	 * Query the internal XML document using XPath
	 *
	 * @param string $query
	 */
	public function xpath($query) {
		if(!$this->xml_doc) {
			$this->l("No XML document loaded", self::LOG_TYPE_ERROR);
		} else {
			$result = $this->xml_doc->xpath($query);
			if(is_array($result) && count($result) == 1) {
				$result = $result[0];
			}

			return $result;
		}
	}

	/**
	 * Use file_exists to determine if a feed URL exists. This will only work
	 * when allow_url_fopen is enabled.
	 *
	 * @param string $file
	 * @return bool
	 */
	public function feedExists($file) {
		$xml_root = $this->_getXMLRoot();

		if(is_null($xml_root)) {
			$this->l("No datafeed URL found in server.xml", self::LOG_TYPE_ERROR);
			return;
		}

		$file_path = $xml_root . $file;
		return file_exists($file_path);
	}

	/**
	 * Add the model path for a component to the JModel class
	 *
	 * @param string 	$name
	 * @param boolean 	$admin
	 */
	public function addComponentModels($name, $admin = false) {
		$component = (substr($name, 0, 4) == 'com_') ? $name : 'com_' . $name;

		$path = JPATH_BASE . DS;
		if($admin) {
			$path .= 'administrator' . DS;
		}

		$path .= 'components' . DS . $component;

		if(file_exists($path) && is_dir($path)) {
			JModel::addIncludePath($path . DS . 'models');
		} else {
			$this->l("ERROR - Could not find component {$name}");
		}
	}

	/**
	 * Takes CLI arguments in the form -[name][value] or --[name]=[value] and extracts them
	 *
	 * @param array $arg_list If null this will be taken from $argv
	 * @return array
	 */
	protected function _getArguments($arg_list = null, $required_list = array()) {
		if(is_null($arg_list)) {
			$arg_list = $_SERVER['argv'];
		}

		array_shift($arg_list);
		$sorted = array();

		foreach($arg_list as $arg) {
			list($name, $value) = $this->_getArgumentValue($arg);
			$sorted[$name] = $value;
		}

		if(!empty($required_list)) {
			foreach($required_list as $required => $severity) {
				if(isset($sorted[$required])) {
					continue;
				}

				trigger_error("Required argument missing: {$required}", $severity);
			}
		}

		return $sorted;
	}

	/**
	 * Extract a single name/value pair for an argument
	 *
	 * @param string $arg
	 * @return array
	 */
	private function _getArgumentValue($arg) {
		if('--' == substr($arg, 0, 2)) {
			$pos = strpos($arg, '=');

			$name = substr($arg, 2, ($pos - 2));
			$value = substr($arg, ($pos + 1));
		} else {
			$name = $arg{1};
			$value = substr($arg, 2);
		}

		return array($name, $value);
	}

	/**
	 * Get a CLI argument which has been stored in self::$arg_list
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function arg($name) {
		return (isset($this->arg_list[$name])) ? $this->arg_list[$name] : null;
	}

	/**
	 * Given a time string this will return a DateTime object which has been shifted to the
	 * current timezone from the feed timezone.
	 *
	 * @param string $time_string
	 * @return DateTime
	 */
	public function getLocalDateTime($time_string = '') {
		return $this->_getShiftedDateTime(self::TZ_FEED, self::TZ_LOCAL, $time_string);
	}

	/**
	 * The inverse of getLocalDateTime
	 *
	 * @param unknown_type $time_string
	 * @return DateTime
	 */
	public function getFeedDateTime($time_string = '') {
		return $this->_getShiftedDateTime(self::TZ_LOCAL, self::TZ_FEED, $time_string);
	}

	/**
	 * Helper function to return shifted DateTime objects
	 *
	 * @param string $timezone_local
	 * @param string $timezone_shift
	 * @param string $time_string
	 * @return DateTime
	 */
	private function _getShiftedDateTime($timezone_local, $timezone_shift, $time_string = '') {
		$date = new DateTime($time_string, new DateTimeZone($timezone_local));
		$date->setTimezone(new DateTimeZone($timezone_shift));

		return $date;
	}

	/**
	 * Get a section from the server.xml config file
	 *
	 * @param string $section_name
	 * @return SimpleXMLElement
	 */
	protected function _getConfigSection($section_name) {
		static $section_list = array();

		if(!isset($section_list[$section_name])) {
			$xml = $this->_getServerXML();
			$section = $xml->xpath("/setting/section[@name='{$section_name}']");

			if(empty($section)) {
				trigger_error("The section '{$section_name}' was not found in the server config.", E_USER_ERROR);
			}

			if(count($section) > 1) {
				trigger_error("Server config contains multiple nodes for section '{$section_name}'. Using first node.", E_USER_WARNING);
			}

			$section_list[$section_name] = $section[0];
		}

		return $section_list[$section_name];
	}

	/**
	 * Load the server XML file into a SimpleXMLElement
	 *
	 * @return SimpleXMLElement
	 */
	private function _getServerXML() {
		static $xml = null;

		if(is_null($xml)) {
			$path = '/';
			if('\\' == DS) {
				$path = 'C:';
			}

			$path .= DS . 'mnt' . DS . 'web' . DS . 'server.xml';
			$xml = simplexml_load_file($path);
		}

		return $xml;
	}

	/**
	 * Get an instance of the database object
	 *
	 * @return JDatabase
	 */
	protected function getDBO($option = null) {
		static $db = array();

		$sig = md5(serialize($option));

		if(!isset($db[$sig])) {
			if(is_null($option)) {
				$config =& JFactory::getConfig();
				$option = array(
					'driver' 	=> $config->getValue('dbtype'),
					'host'		=> $config->getValue('host'),
					'user'		=> $config->getValue('user'),
					'password' 	=> $config->getValue('password'),
					'database'	=> $config->getValue('db'),
					'prefix' 	=> $config->getValue('dbprefix')
				);
			}

			if(!class_exists('JDatabase')) {
				jimport('joomla.application.component.model');
			}

			$db[$sig] =& JDatabase::getInstance($option);
		}

		return $db[$sig];
	}
	
	/**
	 * Save a model, validate and display any errors to console
	 * 
	 * @param SuperModel $model
	 * @return mixed
	 */
	protected function _save($model){
	
		$error_list = $model->validate();
		
		if(!empty($error_list)){
			foreach ($error_list as $member => $error){
				foreach($error as $description){
					$this->l('Save Error: ' . $member . ' (' . $description . ')');
				}
			}
			return false;
		}
		else{
			return $model->save();
		}
	}

	/**
	 * Set script execution expiry time
	 *
	 * @param int $seconds
	 */
	private function _setExecutionExpiryTime($seconds)
	{
		$date = new DateTime();
		$date->modify('+'.$this->max_execution_time.' seconds');
		$this->expiry_time = $date;
		$this->l('Job set to expire at '.$this->expiry_time->format('Y-m-d H:i:s'));
	}

	/**
	 * Has the execution time expired
	 *
	 * @return boolean
	 */
	public function hasExecutionTimeExpired(){
		$expired = false;
		if(!is_null($this->expiry_time)){
			$date = new DateTime();
			$expired = ($date > $this->expiry_time);
			if($expired){
				$this->l('Job execution time has expired.');
			}
		}
		return $expired;
	}
	
	final protected function _checkForRunningInstance($cron_script)
	{
		$count = 0;
		exec("ps w -C php", $process_list);
	
		foreach ($process_list as $process){
			if (strpos($process, $cron_script) == true){
				$count ++;
			}
		}
		
		if ($count > 2){
			$this->e('More than 2 instances of script running');
			exit;
		}
		
		if ($this->hasExecutionTimeExpired()) {
			$this->e('Script was running for longer than max execution time without starting.');
			exit;
		}
		
		if ($count > 1){
			return true;
		}
		return false;
	}

	abstract public function execute();
}