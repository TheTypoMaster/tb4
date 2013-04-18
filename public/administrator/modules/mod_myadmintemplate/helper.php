<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_templates'.DS.'helpers'.DS.'template.php' );

class modMyAdminTemplateHelper
{
	function getDefaultAdminTemplate()
	{
		$dbo = JFactory::getDBO();
		$query = 'SELECT template '.
			'FROM #__templates_menu '.
			'WHERE client_id = 1 '.
			'ORDER BY template';
		$dbo->setQuery($query);
		return $dbo->loadResult();
	}

	function getAdminTemplates()
	{
		//Retreiving administrator templates since client_id=1	
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '1', '', 'int'));
		$tBaseDir = $client->path.DS.'templates';

		//get template xml file info
		$rows = array();
		$rows = TemplatesHelper::parseXMLTemplateFiles($tBaseDir);
		return $rows;	
	}

}

