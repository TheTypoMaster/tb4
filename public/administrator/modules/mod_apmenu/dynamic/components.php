<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Borrowed from html/mod_menu/helper.php
$db = &JFactory::getDBO();
$query = 
	"SELECT id, ".
	"	parent, ".
	"	name, ".
	"	admin_menu_link, ".
	"	".$db->NameQuote('option')." ".
	"FROM #__components ".
	"WHERE ".$db->NameQuote('option')." <> 'com_frontpage' ".
	"	AND ".$db->NameQuote('option')." <> 'com_media' ".
	"	AND enabled = 1 ".
	"ORDER BY parent, ".
	"	ordering, ".
	"	name";
$db->setQuery($query);
$components = $db->loadAssocList();

// If we don't have a link, get our first child's link
$adminMenuLinks = array();
foreach($components as $component)
{
	if($component['parent'] && array_key_exists($component['parent'], $adminMenuLinks) && !$adminMenuLinks[$component['parent']])
	{
		$adminMenuLinks[$component['parent']] = trim($component['admin_menu_link']);
	}
	else
	{
		$adminMenuLinks[$component['id']] = trim($component['admin_menu_link']);
	}
}

$menuItems = array();
$menu = array();
for($i = 0; $i < count($components); $i++)
{
	$component = $components[$i];

	if(array_key_exists($component['id'], $adminMenuLinks) && $adminMenuLinks[$component['id']])
	{
		$link = "index.php?".$adminMenuLinks[$component['id']];

		$menuItem = 
			array(
				'id' => 'components'.$i, 
				'name' => $component['name'],
				'link' => $link,
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			);

		$parentId = $component['parent'];
		if($parentId == 0)
		{
			$menu[] = &$menuItem;
		}
		else if(array_key_exists($parentId, $menuItems))
		{
			$menuItems[$parentId]['children'][] = &$menuItem;
		}

		$menuItems[$component['id']] = &$menuItem;
		unset($menuItem);
	}
}

modAPMenuHelper::renderMenu($menu);

?>

