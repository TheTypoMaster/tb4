<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Debug plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemConfig extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemConfig(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the translation
		$this->loadLanguage( );
	}

	/**
	* Converting the site URL to fit to the HTTP request
	*
	* @return void
	*/
	function onAfterInitialise()
	{
		global $_PROFILER, $mainframe, $database;

		$registry =& JFactory::getConfig();
		
		$dbconnection = $this->getconfig();
		
		$registry->setValue('config.dbconnection', $dbconnection);
		
	}
	
	/**
	* Converting the site URL to fit to the HTTP request
	*
	* @return object
	*/
	private function getconfig()
	{
		$xmlFileName = '/mnt/web/server.xml';
		$xmlHandler= @fopen($xmlFileName,"r");
		
		$content= fread($xmlHandler,filesize($xmlFileName));
		fclose($xmlHandler);
	
		$xml = new SimpleXMLElement($content);
		$dbconnection = array();
		
		foreach( $xml->children() as $name => $node )
		{
			$nodeDatabase = $node->database;
			if( $nodeDatabase )
			{
				$attributes = array();
				for( $i = 0; $i< count($nodeDatabase); $i++)
				{
					foreach( $nodeDatabase[$i]->attributes() as $k => $v )
					{
						$attributes[$k] = (string)$v;
					}
					$dbconnection[$attributes['name']] = array(
						'host' => (string)$nodeDatabase[$i]->host,
						'user' => (string)$nodeDatabase[$i]->user,
						'password' => (string)$nodeDatabase[$i]->password,
						'database' => (string)$nodeDatabase[$i]->name,
					);
				}
	
			}
		}
		
		return $dbconnection;
	}
	
}