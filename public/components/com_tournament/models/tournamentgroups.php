<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentGroups extends JModel
{
	public function getTournamentGroups(){
		$db =& $this->getDBO();
		
		$query  = " SELECT *";
		$query .= " FROM tb_tournament_groups";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getTournamentGroupById($groupId){
		$db =& $this->getDBO();
	
		$query  = " SELECT *";
		$query .= " FROM tb_tournament_groups";
		$query .= " WHERE id = '$groupId'";
	
		$db->setQuery($query);
		return $db->loadResult();
	}
		
	public function getTournamentGroupByLabelId($labelId){
		$db =& $this->getDBO();
		
		$query  = " SELECT tg.group as tournamentGroup, tg.description as tournamentDescription, tg.parentGroupId as tournamentParentGroupId";
		$query .= " FROM tb_tournament_groups AS tg";
		$query .= " INNER JOIN tbdb_tournament_group_label AS tgl ON tgl.tournament_group_id = tg.id";
		$query .= " INNER JOIN tbdb_tournament_labels AS tl ON tl.id = tgl.tournament_label_id";
		$query .= " WHERE tl.id = '$labelId'";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
