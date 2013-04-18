<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelBetSelection extends SuperModel
{
	protected $_table_name = '#__bet_selection';
	
	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'bet_id' => array(
			'name' => 'Bet ID',
			'type' => self::TYPE_INTEGER
		),
		'selection_id' => array(
			'name' => 'Selection ID',
			'type' => self::TYPE_INTEGER
		),
		'position' => array(
			'name' => 'Position',
			'type' => self::TYPE_INTEGER
		),
	);


	/**
	 * Load a single record from the tbdb_bet_selection table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetSelection($id) {
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
	
	/**
	 * Load a single record from the tbdb_bet_selection table by Selection ID.
	 *
	 * @param integer $selection_id
	 * @return object
	 */
	public function getBetSelectionBySelectionID($selection_id) {
		return $this->find(array(SuperModel::getFinderCriteria('selection_id', $selection_id)), SuperModel::FINDER_SINGLE);
	}
	
	/**
	 * Load bet record list from the tbdb_bet_selection table by Bet ID.
	 *
	 * @param integer $bet_id
	 * @return object
	 */
	public function getBetSelectionListByBetID($bet_id) {
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
				*,
				s.external_selection_id,
				s.number,
				s.name
			FROM
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			LEFT JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON
				bs.selection_id = s.id
			WHERE
				bs.bet_id = ' . $db->quote($bet_id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Load bet record list from the tbdb_bet_selection table by Bet ID.
	 *
	 * @param integer $bet_id
	 * @param string $status
	 * @return object
	 */
	public function getBetSelectionListByBetIDAndSelectionStatus($bet_id, $status) {
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
			SELECT
				*,
				s.name,
				s.external_selection_id
			FROM
				' . $db->nameQuote('#__bet_selection') . ' AS bs
			LEFT JOIN
				' . $db->nameQuote('#__selection') . ' AS s
			ON
				bs.selection_id = s.id
			LEFT JOIN
				' . $db->nameQuote('#__selection_status') . ' AS st
			ON
				s.selection_status_id = st.id
			WHERE
				bs.bet_id = ' . $db->quote($bet_id) . '
			AND
				st.keyword = ' .$db->quote($status)
			;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
