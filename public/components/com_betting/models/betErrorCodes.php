<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

// Dodgy Jomola model to get in bet codes for legacy API. I'm not bothering with creating lots of models for this!

class BettingModelBetErrorCodes extends JModel
{
	
	// Get the TB error model based on the code passed in.
	public function getTBErrorMessage($providerErrorCode, $provider) {
		
		$db =& $this->getDBO();
		
		$query  = " SELECT * from tb_data_values AS dv";
		$query .= " INNER JOIN tb_data_types AS dt ON dt.id = dv.data_type_id";
		$query .= " INNER JOIN tb_data_provider_match AS dpm ON dpm.data_value_id = dv.id";
		$query .= " INNER JOIN tb_data_provider AS dp ON dp.id = dpm.provider_id";
		
		$query .= " WHERE dpm.value = $providerErrorCode";
		$query .= " AND dp.provider_name = $provider";
			
		$db->setQuery($query);
		return $db->loadObject();
		
		
	}
	
	
}