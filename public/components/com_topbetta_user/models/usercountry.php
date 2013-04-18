<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TopbettaUserModelUserCountry extends SuperModel
{
	protected $_table_name = '#__user_country';
	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'code' => array(
			'name' => 'Country Code',
			'type' => self::TYPE_STRING,
		),
		'name' => array(
			'name' => 'Country Name',
			'type' => self::TYPE_STRING,
		),
		'code' => array(
			'name' => 'Code',
			'type' => self::TYPE_STRING,
		),
		'mobile_validation' => array(
			'name' => 'Mobile Validation',
			'type' => self::TYPE_STRING,
		),
		'phone_validation' => array(
			'name' => 'Phone Validation',
			'type' => self::TYPE_STRING,
		),
		'postcode_validation' => array(
			'name' => 'Postcode Validation',
			'type' => self::TYPE_STRING,
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		)

	);
	/**
	 * Load a single record from the tbdb_user_country table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getUserCountry($id) {
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
	/**
	 * Load a single record from the tbdb_user_country table by (ISO 3166-1 alpha-2) countrycode.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getUserCountryByCode($country_code) {
		return $this->find(array(SuperModel::getFinderCriteria('code', $country_code)), SuperModel::FINDER_SINGLE);
	}
	/**
	 * Load a list of countries sorted by (ISO 3166-1 alpha-2) countrycode.
	 *
	 * @return object
	 */
	public function getUserCountryList() {
	
		$table = $this->_getTable();
		$table->addOrder('name', DatabaseQueryTableOrder::ASCENDING);
		
		$query = new DatabaseQuery($table);

		$db =& $this->getDBO();
		$db->setQuery($query->getSelect());

		return $db->loadAssocList('code');	
	}
	
}