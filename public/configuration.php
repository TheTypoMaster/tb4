<?php

class JConfig {
	public $offline = '0';
	public $editor = 'none';
	public $list_limit = '20';
	public $helpurl = 'http://help.joomla.org';
	public $debug = '0';
	public $debug_lang = '0';
	public $sef = '0';
	public $sef_rewrite = '0';
	public $sef_suffix = '0';
	public $feed_limit = '10';
	public $feed_email = 'author';
	public $secret = 'UfUlBYLtSuejXntn';
	public $gzip = '0';
	public $error_reporting = '0';
	public $xmlrpc_server = '1';
	public $log_path = './logs';
	public $tmp_path = './tmp';
	public $live_site = '';
	public $force_ssl = '0';
	public $offset = '10';
	public $caching = '0';
	public $cachetime = '15';
	public $cache_handler = 'file';
	public $memcache_settings = array("persistent" => "0", "compression" => "0", "servers" => array());
	public $ftp_enable = '0';
	public $ftp_host = '127.0.0.1';
	public $ftp_port = '21';
	public $ftp_user = 'admin';
	public $ftp_pass = 'dec0ll1g';
	public $ftp_root = '';
	public $dbtype = 'mysql';
	public $host = 'localhost';
	public $user = 'root';
	public $db = 'topbetta_igas';
	public $password = 't0pb3tt@mysqlp@ss';
	public $dbprefix = 'tbdb_';
	public $mailer = 'mail';
	public $mailfrom = 'help@topbetta.com';
	public $fromname = 'TopBetta Admin';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpsecure = 'none';
	public $smtpport = '25';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';
	public $MetaAuthor = '1';
	public $MetaTitle = '1';
	public $lifetime = '60';
	public $session_handler = 'database';
	public $sitename = 'TopBetta';
	public $MetaDesc = 'TopBetta is a leading Australian online race betting site. We also offer Australia’s only online racing & sports tournament betting. Sign up today!';
	public $MetaKeys = 'Online Betting,Bet,Gambling,Horse Racing,Spring Carnival,Melbourne Cup,Greyhound,Harness,Bookmakers,AFL,NRL';
	public $offline_message = 'This site is down for maintenance. Please check back again soon. foobar';
	public $api_key = '3f7fd44b1d5ec5916a5bbc674da888e3';
	public $dbconnection = array();
	public $api_test_mode = false;
	public $time_zone = 'AEST';
	public $time_zone_long = 'AEST (UT+10:00)';

	/*
	public function __construct() {
		jimport('mobileactive.config.reader');

		$reader = ConfigReader::getInstance();
		$db 	= $reader->getDatabase('topbetta_application');
		$env 	= $reader->getEnvironment();

		$this->app_environment		= $env;
		$this->error_reporting 		= ($env == 'development') ? E_ALL : 0;
		$this->remote_processing	= ($env == 'development') ? false : true;
		//$this->memcache_settings['servers'] = $reader->getMemCacheServerList();
		
		$this->host		= $db->getValue('host');
		$this->user 	= $db->getValue('user');
		$this->db 		= $db->getValue('database');
		$this->password	= $db->getValue('password');
		
		$date					= new DateTime();
		$this->offset			= (int)($date->getOffset() / 3600);
		$this->time_zone		= ($this->offset == 10) ? 'AEST' : 'AEDT';
		$this->time_zone_long	= ($this->offset == 10) ? 'AEST (UT+10:00)' : 'AEDT (UTC+11:00)';
	}
	 * 
	 */
}
//error_reporting(E_ALL);
