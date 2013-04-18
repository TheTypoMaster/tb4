<?php

defined('JPATH_BASE') or die();

/**
 * Database Connection Factory class
 *
 * @deprecated There's only 1 DB in use now and the mobileactive.config.reader class is more useful
 */

class DatabaseConnectionFactory
{
	private static $_instanceList = array();
	private static $_configuration = array();


	//A private constructor; prevents direct creation of object
	private function __construct() {}


	/**
	 * Get an instance of database connection
	 *
	 * Returns a db connection
	 *
	 * @access public
	 * @param	string	$name 		The db connection name
	 * @return object return db connection object or false if error occurs
	 */
	public static function getInstance( $name=null )
	{
		$instanceName = ($name ? $name : 'default');

		//if the connection not exists in the list yet, create it
		if(!isset(self::$_instanceList[$instanceName]) )
		{
			//if $name not provided, use the default one
			if(!$name)
			{
				self::$_instanceList[$instanceName] = JFactory::getDBO();
			}
			else
			{
				//populate configuration when it's not populated
				if(!self::$_configuration)
				{
					self::populateConfiguration();
				}

				//add db connection to _instanceList
				if(isset(self::$_configuration[$name]))
				{
					self::$_instanceList[$instanceName] = JDatabase::getInstance(self::$_configuration[$name]);
				}
				else
				{
					//if the db name not found, raise error!
					JError::raiseError(0, 'db connection ' . $name . ' not found in the configuration');
					exit;
				}
			}
		}

		//return the connection
		return self::$_instanceList[$instanceName];
	}

	/**
	 * List instances
	 *
	 * Returns the db connections stored in $_instanceList (debugging purpose)
	 *
	 * @access public
	 * @return array return an array of db connections
	 */
	public static function listInstances()
	{
		return self::$_instanceList;
	}

	/**
	 * List databse connection configurations
	 *
	 * Returns configuration details (debugging purpose)
	 *
	 * @access public
	 * @return array return an array of db connections
	 */
	public static function listConfigurations()
	{
		return self::$_configuration;
	}

	/**
	 * Populate $_configuration with database configuration details
	 *
	 * @access public
	 * @return boolean true
	 */
	private static function populateConfiguration()
	{
		$registry = JFactory::getConfig();

		$_configuration = $registry->getValue('config.dbconnection');

		if( $_configuration )
		{
			self::$_configuration = $_configuration;
		}
		else
		{
			$host = $registry->getValue('host');
			$user = $registry->getValue('user');
			$password = $registry->getValue('password');
			$database = $registry->getValue('db');

			self::$_configuration[$host] = array(
				'host' => $host,
				'user' => $user,
				'password' => $password,
				'database' => $database,
			);
		}

		return true;
	}

}

?>