<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentLabels extends JModel
{
	public function getTournamentLabels(){
		$db =& $this->getDBO();
		
		$query  = " SELECT id, label, description";
		$query .= " FROM tb_tournament_labels";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getTournamentLabelsByTournamentId($tournamentId){
		$db =& $this->getDBO();
		
		$query  = " SELECT tl.id";
		$query .= " FROM tb_tournament_labels AS tl";
		$query .= " INNER JOIN tb_tournament_label_tournament AS tlt ON tlt.tournament_label_id = tl.id";
		$query .= " WHERE tlt.tournament_id = '$tournamentId'";
		
		$db->setQuery($query);
		return $db->loadResultArray();
	}
	
	public function addTournamentLabelToTournament($tournamentId, $labelId){
		$db =& $this->getDBO();
		
		$query = 'INSERT INTO tb_tournament_label_tournament (tournament_id,	tournament_label_id, created_at)
			 		VALUES (' . $db->quote($tournamentId) . ',' . $db->quote($labelId) . ', NOW())';
				
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function deleteTournamentLabelsByTournamentId($tournamentId){
		$db =& $this->getDBO();
	
		$query  = " DELETE from tb_tournament_label_tournament";
		$query .= " WHERE tournament_id = '$tournamentId' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
