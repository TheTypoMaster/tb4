<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelTournamentBetSelection extends SuperModel
{
	protected $_table_name = '#__tournament_bet_selection';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'tournament_bet_id' => array(
			'name' => 'Tournament Bet ID',
			'type' => self::TYPE_INTEGER
		),
		'selection_id' => array(
			'name' => 'Selection ID',
			'type' => self::TYPE_INTEGER
		),
		'position' => array(
			'name' => 'Position',
			'type' => self::TYPE_INTEGER
		)
	);

	public function getTournamentBetSelection($id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('id', $id)
		), 	SuperModel::FINDER_SINGLE);
	}

	public function getTournamentBetSelectionListByBetID($bet_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('tournament_bet_id', $bet_id)
		), 	SuperModel::FINDER_LIST);
	}
}