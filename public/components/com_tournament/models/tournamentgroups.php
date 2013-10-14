<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentGroups extends JModel
{

	public function getTournamentGroups(){
		$db =& $this->getDBO();

		$query  = " SELECT tg.id, tg.group_name as grouping, tg.description as description, tg.parent_group_id as parent_group_id, ttg.group_name as parent_group_group";
		$query .= " FROM tb_tournament_groups as tg";
		$query .= " LEFT JOIN tb_tournament_groups as ttg on ttg.id = tg.parent_group_id";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentGroupById($id = 0){
		$db =& $this->getDBO();

		$query  = " SELECT id, group_name, description, parent_group_id";
		$query .= " FROM tb_tournament_groups";
		
		if ($id){
			$query .= " WHERE id = '$id'";
		}
			
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	public function getTournamentGroupByGroupId($groupId){
		$db =& $this->getDBO();
	
		$query  = " SELECT tg.group as tournamentGroup, tg.description as tournamentDescription, tg.parentGroupId as tournamentParentGroupId";
		$query .= " FROM tb_tournament_groups AS tg";
		$query .= " INNER JOIN tbdb_tournament_group_group AS tgl ON tgl.tournament_group_id = tg.id";
		$query .= " INNER JOIN tbdb_tournament_groups AS tl ON tl.id = tgl.tournament_group_id";
		$query .= " WHERE tl.id = '$groupId'";
	
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentParentGroups($parentGroupId){
		$db =& $this->getDBO();

		$query  = " SELECT id, group, description, parent_group_id";
		$query .= " FROM tb_tournament_groups WHERE parent_group_id = '$parentGroupId' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentGroupsByTournamentId1($tournamentId){
		$db =& $this->getDBO();

		$query  = " SELECT tl.id";
		$query .= " FROM tb_tournament_groups AS tl";
		$query .= " INNER JOIN tb_tournament_group_tournament AS tlt ON tlt.tournament_group_id = tl.id";
		$query .= " WHERE tlt.tournament_id = '$tournamentId'";

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function addTournamentGroupToTournament($tournamentId, $groupId){
		$db =& $this->getDBO();

		$query = 'INSERT INTO tb_tournament_group_tournament (tournament_id, tournament_group_id, created_at)
		 		VALUES (' . $db->quote($tournamentId) . ',' . $db->quote($groupId) . ', NOW())';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function addTournamentGroup($group, $description, $parent_group_id){
		$db =& $this->getDBO();

		$query = "INSERT INTO tb_tournament_groups (group_name, description, parent_group_id)
		VALUES ('$group','$description','$parent_group_id')";

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function updateTournamentGroup($id, $group, $description, $parent_group_id){
		$db =& $this->getDBO();

		$query = "UPDATE tb_tournament_groups SET group_name = '$group', description = '$description', parent_group_id = '$parent_group_id' WHERE id = '$id'" ;

		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function deleteTournamentGroupsByTournamentId($tournamentId){
		$db =& $this->getDBO();

		$query  = " DELETE from tb_tournament_group_tournament";
		$query .= " WHERE tournament_id = '$tournamentId' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	

}




