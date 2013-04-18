<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$db = &JFactory::getDBO();
$sql = 
	"SELECT menutype, ".
	"	title ".
	"FROM #__menu_types ".
	"ORDER BY title";
$db->setQuery($sql);
$menuTypes = $db->loadAssocList();

$menu = array();
for($i = 0; $i < count($menuTypes); $i++)
{
	$menuType = $menuTypes[$i];

	$menu[] = 
		array(
			'id' => 'menus'.$i, 
			'name' => $menuType['title'],
			'link' => 'index.php?option=com_menus&task=view&menutype='.$menuType['menutype'],
			'type' => 'url', 
			'parent' => 0, 
			'params' => 'menu_image=-1', 
			'access' => 0,
			'children' => array()
		);
}

modAPMenuHelper::renderMenu($menu);

?>

