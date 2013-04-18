<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.parameter');

class modAPMenuHelper
{
	function getMenu($menuType)
	{
		$db = &JFactory::getDBO();

		$sql = 
			"SELECT m.id, ".
			"	m.name, ".
			"	m.link, ".
			"	m.type, ".
			"	m.parent, ".
			"	m.params, ".
			"	m.access ".
			"FROM #__menu AS m ".
			"WHERE m.menutype = ".$db->quote($menuType)." ".
			"	AND m.published = 1 ".
			"ORDER BY m.parent, ".
			"	m.ordering";
		$db->setQuery($sql);

		$menuRows = $db->loadAssocList();

		$menu = modAPMenuHelper::_buildMenu($menuRows);

		/*
		$menu = array(
			array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
			array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
			array('id' => 1, 'name' => 'Site', 'url' => 'index.php', 'children' => array()),
		);
		*/

		return $menu;
	}

	function &_buildMenu($menuRows)
	{
		$menuItems = array();
		$menu = array();

		foreach($menuRows as $menuRow)
		{
			// This makes a copy
			$menuItem = $menuRow;
			$menuItem['children'] = array();

			$parentId = $menuItem['parent'];
			if($parentId == 0)
			{
				$menu[] = &$menuItem;
			}
			else if(array_key_exists($parentId, $menuItems))
			{
				$menuItems[$parentId]['children'][] = &$menuItem;
			}

			$menuItems[$menuItem['id']] = &$menuItem;
			unset($menuItem);
		}

		return $menu;
	}

	function renderMenu($menu, $top = false)
	{
		if($top)
		{
			print "<div id=\"module-menu\">\n";
			print "<ul id=\"menu\">\n";
		}

		$childCount = count($menu);
		for($i = 0; $i < $childCount; $i++)
		{
			modAPMenuHelper::renderMenuItem($menu[$i]);
		}

		if($top)
		{
			print "</ul>\n";
			print "</div>\n";
		}
	}

	function renderMenuItem($menuItem)
	{
		static $user = null;
		if($user == null) $user = &JFactory::getUser();

		// access: 0 - Public, 1 - Registered, 2 - Special
		// gid: 23 - Manager, 24 - Administrator, 25 - Super Administrator
		if($menuItem['access'] == 1 && $user->gid < 24 || $menuItem['access'] == 2 && $user->gid < 25)
		{
			return;
		}
		
		if($menuItem['type'] == 'separator')
		{
			print "<li class=\"separator\"><span></span></li>\n";
		}
		else if(strpos($menuItem['link'], 'modules/mod_apmenu/dynamic/') === 0)
		{
			require_once($menuItem['link']);
		}
		else
		{
			$menuItemParams = new JParameter($menuItem['params']);
			$menuImage = $menuItemParams->get('menu_image');
			$anchorExtra = "";

			// Use onclick for any javascript
			if(strpos($menuItem['link'], "javascript:") === 0)
			{
				$href = "#";
				$anchorExtra = "onclick=\"".$menuItem['link']."\"";
			}
			else
			{
				$href = $menuItem['link'];
			}

			print "<li class=\"node\">\n";
			print "<a id=\"".str_replace(' ','',$menuItem['name'])."\" ".$anchorExtra." href=\"".$href."\">";
			if($menuImage == -1)
			{
				print $menuItem['name'];
			}
			else
			{
				print "<img src=\"".$menuImage."\" />";
			}
			print "</a>\n";

			$childCount = count($menuItem['children']);
			if($childCount > 0)
			{
				print "<ul>\n";
				for($i = 0; $i < $childCount; $i++)
				{
					modAPMenuHelper::renderMenuItem($menuItem['children'][$i]);
				}
				print "</ul>\n";
			}

			print "</li>\n";
		}
	}
}

