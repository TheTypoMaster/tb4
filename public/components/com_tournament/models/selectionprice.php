<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelSelectionPrice extends SuperModel
{
	protected $_table_name = '#__selection_price';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'selection_id' => array(
			'name' => 'Selection ID',
			'type' => self::TYPE_INTEGER
		),
		'bet_product_id' => array(
			'name' => 'Bet Product ID',
			'type' => self::TYPE_INTEGER
		),
		'w_product_id' => array(
			'name' => 'Win Bet Product ID',
			'type' => self::TYPE_INTEGER
		),
		'p_product_id' => array(
			'name' => 'Place Bet Product ID',
			'type' => self::TYPE_INTEGER
		),
		'win_odds' => array(
			'name' => 'Win Odds',
			'type' => self::TYPE_FLOAT
		),
		'place_odds' => array(
			'name' => 'Place Odds',
			'type' => self::TYPE_FLOAT
		),
		'override_odds' => array(
			'name' => 'Override Odds',
			'type' => self::TYPE_FLOAT
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

	public function getSelectionPrice($id)
	{
		return $this->load($id);
	}

	public function getSelectionPriceBySelectionID($selection_id)
	{
//		return $this->find(array(
//			SuperModel::getFinderCriteria('selection_id', $selection_id)
//		), 	SuperModel::FINDER_LIST);

		//FIXME: use super model when it's really working
		$db =& $this->getDBO();
		$query = '
			SELECT
				*
			FROM
				' . $db->nameQuote('#__selection_price') . ' 
			WHERE
				selection_id = ' . $db->quote($selection_id);
			;
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function getSelectionPriceBySelectionIDAndBetProductID($selection_id, $product_id)
	{
		return $this->find(array(
		SuperModel::getFinderCriteria('selection_id', $selection_id),
		SuperModel::getFinderCriteria('bet_product_id', $product_id)
		), 	SuperModel::FINDER_SINGLE);
	}

	public function getUpdatedSelectionPriceListByMarketID($market_id)
	{
		$selection = new DatabaseQueryTable('#__selection');
		$selection->addWhere('market_id', $market_id);

		$table = $this	->_getTable()
						->addJoin($selection, 'selection_id', 'id');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $db->loadObjectList();
	}
}
