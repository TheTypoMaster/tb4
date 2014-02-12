<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentLabels extends JModel
{
	public function getTournamentLabels(){
		$db =& $this->getDBO();
		
		$query  = " SELECT tl.id, tl.label as label, tl.description as description, tl.parent_label_id as parent_label_id, ttl.label as parent_label_label";
		$query .= " FROM tb_tournament_labels as tl";
		$query .= " LEFT JOIN tb_tournament_labels as ttl on ttl.id = tl.parent_label_id";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getTournamentLabelById($id){
		$db =& $this->getDBO();
	
		$query  = " SELECT id, label, description, parent_label_id";
		$query .= " FROM tb_tournament_labels WHERE id = '$id'";
			
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	public function getTournamentParentLabels($parentLabelId){
		$db =& $this->getDBO();
	
		$query  = " SELECT id, label, description, parent_label_id";
		$query .= " FROM tb_tournament_labels WHERE parent_label_id = '$parentLabelId' ";
			
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
	
	public function addTournamentLabel($label, $description, $parent_label_id){
		$db =& $this->getDBO();
	
		$query = "INSERT INTO tb_tournament_labels (label,	description, parent_label_id)
			 		VALUES ('$label','$description','$parent_label_id')";
	
		$db->setQuery($query);
		$db->loadObjectList();
		return $db->insertid();
	}
	
	public function updateTournamentLabel($id, $label, $description, $parent_label_id){
		$db =& $this->getDBO();
	
		$query = "UPDATE tb_tournament_labels SET label = '$label', description = '$description', parent_label_id = '$parent_label_id' WHERE id = '$id'" ;
		
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
	
	public function deleteTournamentLabelsByLabelId($labelId){
		$db =& $this->getDBO();
	
		$query  = " DELETE from tb_tournament_label_tournament";
		$query .= " WHERE tournament_label_id = '$labelId' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function deleteLabelsByLabelId($labelId){
		$db =& $this->getDBO();
	
		$query  = " DELETE from tb_tournament_labels";
		$query .= " WHERE id = '$labelId' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function removeParentLabelRefs($labelId){
		$db =& $this->getDBO();
		
		$query = "UPDATE tb_tournament_labels SET parent_label_id = '0' WHERE parent_label_id = '$labelId'" ;
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	/*
	 * Tournament Group Stuff
	*/
	
	public function deleteLabelGroupsByLabelId($labelID){
		$db =& $this->getDBO();
	
		$query  = " DELETE from tb_tournament_group_label";
		$query .= " WHERE tournament_label_id = '$labelID' ";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function addLabelGroupToLabel($labelID, $groupID){
		$db =& $this->getDBO();
	
		$query = "INSERT INTO tb_tournament_group_label (tournament_label_id, tournament_group_id, created_at) VALUES ($labelID, $groupID, NOW())";
	
		$db->setQuery($query);
		$db->loadObjectList();
		return $db->insertid();
	}
	
	public function getLabelGroups(){
		$db =& $this->getDBO();

		$query  = " SELECT tg.id, tg.group_name as group_name, tg.description as description, tg.parent_group_id as parent_group_id, ttg.group_name as parent_group_group";
		$query .= " FROM tb_tournament_groups as tg";
		$query .= " LEFT JOIN tb_tournament_groups as ttg on ttg.id = tg.parent_group_id";
			
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getLabelGroupsByLabelId($labelID){
		$db =& $this->getDBO();
	
		$query  = " SELECT tg.id";
		$query .= " FROM tb_tournament_groups AS tg";
		$query .= " INNER JOIN tb_tournament_group_label AS tgl ON tgl.tournament_group_id = tg.id";
		$query .= " WHERE tgl.tournament_label_id = '$labelID'";
	
		$db->setQuery($query);
		return $db->loadResultArray();
	}
	
}
