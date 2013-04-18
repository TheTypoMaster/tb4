<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementAdminAccessLevel extends JElement
{
	var $_name = 'Admin Access Level';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

		$query = 
			'SELECT 0 AS id, \'Hidden\' AS name '.
			'UNION '.
			'SELECT id, name '.
			'FROM #__core_acl_aro_groups '.
			'WHERE name IN (\'Manager\', \'Administrator\', \'Super Administrator\')';
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return JHTML::_('select.radiolist', $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'name', $value, $control_name.$name);
	}
}

?>
