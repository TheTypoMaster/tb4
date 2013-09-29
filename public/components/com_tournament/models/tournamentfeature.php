<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentFeature extends JModel
{
	
	public function getTournamentFeatureList(){
		$db =& $this->getDBO();
				
		$query = '
			SELECT
				id,
				keyword,
				description
			FROM
				tb_tournament_features ORDER BY keyword';
				
		$db->setQuery($query);
		return $db->loadObjectList();
	}

}