<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

class TournamentModelTournamentBuyIn extends SuperModel
{
	protected $_table_name = '#__tournament_buyin';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true
		),
		'buy_in' => array(
			'name' 		=> 'Buy-in',
			'type' 		=> self::TYPE_FLOAT,
			'required' 	=> true
		),
		'entry_fee' => array(
			'name' 		=> 'Entry-fee',
			'type' 		=> self::TYPE_FLOAT
		),
		'status_flag' => array(
			'name' 		=> 'Status-flag',
			'type' 		=> self::TYPE_INTEGER
		)
	);

	/**
	 * Load a single buy-in record by ID.
	 *
	 * @param integer $id
	 * @param integer $status
	 * @return object
	 */
	public function getTournamentBuyIn($id, $status = 1)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('id', $id),
			SuperModel::getFinderCriteria('status_flag', $status)
		), SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load a single buy-in record by tournament.
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getBuyInByTournamentID($tournament_id)
	{
		$tournament_model =& JModel::getInstance('Tournament', 'TournamentModel');
		$tournament = $tournament_model->getTournament($tournament_id);
		
		return $this->find(array(
			SuperModel::getFinderCriteria('buy_in', $tournament->buy_in / 100),
			SuperModel::getFinderCriteria('entry_fee', $tournament->entry_fee / 100)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load all possible buy-in values. Defaults to only active records.
	 *
	 * @param integer $status
	 * @return array
	 */
	public function getTournamentBuyInList($status = 1)
	{
		$table = $this->_getTable()
					->addWhere('status_flag', $status)
					->addOrder('buy_in');

		$query = new DatabaseQuery($table);
		$db =& $this->getDBO();

		$db->setQuery($query->getSelect());
		return $this->_loadModelList($db->loadObjectList());
	}

    /**
	 * Load all possible buy-in values for the api. Defaults to only active records.
	 *
	 * @param integer $status
	 * @return array
	 */
	public function getTournamentBuyInListApi($status = 1)
	{
		$table = $this->_getTable()
					->addWhere('status_flag', $status)
					->addOrder('buy_in');

		$query = new DatabaseQuery($table);
		$db =& $this->getDBO();

		$db->setQuery($query->getSelect());
		return $db->loadObjectList();
 
    }

	/**
	 * Load all possible buy-in values. Defaults to only active records.
	 *
	 * @param integer $status
	 * @return array
	 */
	public function getTournamentBuyInListByPrivateFlag($private_flag = false)
	{
		$criteria_list = array(SuperModel::getFinderCriteria('status_flag', 1));

		if($private_flag != false) {
			$criteria_list[] = SuperModel::getFinderCriteria('buy_in', 5, DatabaseQueryTableWhere::CONTEXT_AND, DatabaseQueryTableWhere::OPERATOR_GREATER_OR_EQUAL);
			$criteria_list[] = SuperModel::getFinderCriteria('buy_in', 100, DatabaseQueryTableWhere::CONTEXT_AND, DatabaseQueryTableWhere::OPERATOR_LESS_OR_EQUAL);
		}

		return $this->find($criteria_list, SuperModel::FINDER_LIST);
	}
}
