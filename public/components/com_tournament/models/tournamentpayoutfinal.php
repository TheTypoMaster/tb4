<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');
/**
 * Tournament Payout Final Model
 */
class TournamentModelTournamentPayoutFinal extends SuperModel
{

	const TYPE_PAYOUT_CASH = 'cash';
	const TYPE_PAYOUT_TOURNAMENTDOLLAR = 'tournamentdollar';
	const TYPE_PAYOUT_TOURNAMENTTICKET = 'tournamentticket';

	const TOURNAMENT_PAYOUT_TYPE_TABLE = '#__tournament_payout_type';

	protected $_table_name = '#__tournament_payout_final';

	protected $_member_list = array(
		'id' => array(
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'tournament_id' => array(
			'type' => self::TYPE_INTEGER
		),
		'user_id' => array(
			'type' => self::TYPE_INTEGER
		),
		'position'=> array(
			'type' => self::TYPE_INTEGER
		),
		'tournament_payout_type_id' => array(
			'type' => self::TYPE_INTEGER
		),
		'win_amount' => array(
			'type' => self::TYPE_INTEGER,
			'validate' => '_zeroCheck'
		));

	public function saveCashPayout(){
		$this->_savePayout(self::TYPE_PAYOUT_CASH);
	}

	public function saveTournamentDollarPayout(){
		$this->_savePayout(self::TYPE_PAYOUT_TOURNAMENTDOLLAR);
	}

	public function saveTournamentTicketPayout(){
		$this->_savePayout(self::TYPE_PAYOUT_TOURNAMENTTICKET);
	}

	private function _savePayout($type){
		$this->tournament_payout_type_id = $this->_getPayoutTypeIdByKeyword($type);
		$this->save();
	}

	protected function _zeroCheck(){
		if($this->win_amount == 0){
			return 'Win amount is zero';
		}
		return true;
	}

	private function _getPayoutTypeIdByKeyword($keyword){
		static $payout_table = null;
		$db =& $this->getDBO();

		if(is_null($payout_table)){
			$table = new DatabaseQueryTable(self::TOURNAMENT_PAYOUT_TYPE_TABLE);
			$table->addColumn('id');
			$table->addColumn('keyword','keyword');

			$subquery = new DatabaseQuery($table);
			$sub_query_sql = $subquery->getSelect();
			$db->setQuery($sub_query_sql);
			$payout_table = $db->loadAssocList('keyword');
		}

		return (int) $payout_table[$keyword]['id'];
	}
}