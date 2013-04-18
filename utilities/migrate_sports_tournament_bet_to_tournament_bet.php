<?php
require_once '../common/shell-bootstrap.php';

define('__DATA_MIGRATION_IN_PROGRESS__', true);

class MigrateSportsTournamentBetToTournamentBet extends TopBettaCLI
{
	const SQL_FILE = '/tmp/update_tournament_bet_id_mapping.sql';
	private $handle = null;
	
	public function initialise()
	{
		$this->addComponentModels('tournament');

		jimport('mobileactive.database.query');
		$this->db = $this->getDBO();
	}

	public function execute()
	{
		$db =& $this->db;
		$this->_createSqlFile();
		
		$sql = 'SELECT * FROM `tbdb_tournament_sport_bet` ORDER BY id ASC';
		$this->d($sql);
		$db->setQuery($sql);
		
		$bet_list = $db->loadObjectList();
		
		foreach($bet_list as $bet){
			
		$this->l('Migrating bet: '. $bet->id . '(' . $bet->tournament_ticket_id . ')');
			
			$old_id = $bet->id;
			unset($bet->id);	
		
			$migrated = JModel::getInstance('TournamentBet', 'TournamentModel')
			 	->setMembers((array) $bet, array(
			 	'odds'				=> 'fixed_odds'
			 ));
			 
			$migrated->bet_type_id		= 1;
			$migrated->bet_product_id	= 2;
			
			$this->_save($migrated);
				
			$bet_selection = array(
				'tournament_bet_id'	=> $migrated->id,
				'selection_id'		=> $bet->tournament_offer_id,
			);
			
			$selection_migrated = JModel::getInstance('TournamentBetSelection', 'TournamentModel')
			->setMembers($bet_selection);
				
			if($this->_save($selection_migrated)){
				$this->l('Migration complete');
				$this->_writeSql($old_id, $migrated->id);
			}
		}
		
		$this->_closeSqlFile();
	}
	
	private function _createSqlFile(){
		$this->handle = fopen(self::SQL_FILE, 'w');
	}
	
	private function _closeSqlFile(){
		fclose($this->handle);
	}
	
	private function _writeSql($old_id, $new_id){
		$sql = "UPDATE `acceptance_transaction` ";
		$sql .= "SET transaction_id= $new_id, ";
		$sql .= "acceptance_transaction_type_id = 2 ";
		$sql .= "WHERE transaction_id = $old_id AND "; 
		$sql .= "acceptance_transaction_type_id = 3;\n";
		
		fwrite($this->handle, $sql);
	}
}

$job = new MigrateSportsTournamentBetToTournamentBet();
$job->debug(true);
$job->execute();