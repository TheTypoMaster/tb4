<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelToken extends SuperModel
{
	protected $_table_name = '#__wagering_token';

	public $_member_list = array(
		'id' => array(
			'name'		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary'	=> true
		),
		'useragent' => array(
			'name' => 'Useragent',
			'type' => self::TYPE_STRING
		),
		'token' => array(
			'name' => 'Token',
			'type' => self::TYPE_STRING
		),
		'hostid' => array(
			'name' => 'Hostid',
			'type' => self::TYPE_STRING
		)
	);


	/**
	 * Get the token.
	 *
	 * $param string $hostid
	 * @return object array
	 */
	public function getStoredToken($hostid)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				useragent,
				token
			FROM
				' . $db->nameQuote('#__wagering_token') . '
			WHERE
				hostid = ' . $db->quote($hostid);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Update token.
	 *
	 * @param array $params
	 * @return bool
	 */
	public function setStoredToken($params)
	{
		$db =& $this->getDBO();
		
		$query = 'UPDATE ' . $db->nameQuote('#__wagering_token') . ' SET  
			token = ' . $db->quote($params['token']) . ' 
		WHERE
			hostid = ' . $db->quote($params['hostid']);

		$db->setQuery($query);
		return $db->query();
	}

}
