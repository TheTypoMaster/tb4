<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.parameter');

class modAPQuickLaunchHelper
{
	function render($params)
	{
		$type = $params->get('type', 'components');
		$iconsShownPerRow = $params->get('iconsShownPerRow', '7');

		switch($type)
		{
			case "components":
				modAPQuickLaunchHelper::renderComponents($iconsShownPerRow);
				break;
			case "modules":
				modAPQuickLaunchHelper::renderModules($params, $iconsShownPerRow);
				break;
		}
	}

	function renderComponents($iconsShownPerRow)
	{
		// Borrowed from html/mod_menu/helper.php
		$db = &JFactory::getDBO();
		$query =
			"SELECT id, ".
			"       parent, ".
			"       name, ".
			"       admin_menu_link, ".
			"       ".$db->NameQuote('option')." ".
			"FROM #__components ".
			"WHERE ".$db->NameQuote('option')." <> 'com_frontpage' ".
			"       AND ".$db->NameQuote('option')." <> 'com_media' ".
			"       AND enabled = 1 ".
			"ORDER BY parent, ".
			"       ordering, ".
			"       name";
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

		$count = 0;
		$n = count($components);
		for($i = 0; $i < $n; $i++)
		{
			$component = $components[$i];

			if($component['parent'] == 0 && array_key_exists($component['id'], $adminMenuLinks) && $adminMenuLinks[$component['id']])
			{
				$text = $component['name'];
				$link = "index.php?".$adminMenuLinks[$component['id']];
				$imgsrc = modAPQuickLaunchHelper::_getImgSrc($component['option'].'.png');

				modAPQuickLaunchHelper::_renderIcon($link, $imgsrc, $text);

				$count++;
				if($count >= $iconsShownPerRow)
				{
					$count = 0;
					print "<div style=\"clear: both\"></div>\n";
				}
			}
		}
	}

	function renderModules($params, $iconsShownPerRow)
	{
		static $modulesType = null;
		if($modulesType == null) $modulesType = $params->get('modulesType', 'both');

		if($modulesType == 'both' || $modulesType == 'site')
		{
			modAPQuickLaunchHelper::_renderModules(0, $iconsShownPerRow);
			print "<div style=\"clear: both\"></div>\n";
		}
		if($modulesType == 'both')
		{
			print "<hr></hr>\n";
		}
		if($modulesType == 'both' || $modulesType == 'admin')
		{
			modAPQuickLaunchHelper::_renderModules(1, $iconsShownPerRow);
		}
	}

	function _renderModules($clientId, $iconsShownPerRow)
	{
		// Borrowed from /administrator/components/com_modules/controller.php

		// path to search for modules
		if ($clientId == '1')
		{
			$path = JPATH_ADMINISTRATOR.DS.'modules';
			$langbase = JPATH_ADMINISTRATOR;
		}
		else
		{
			$path = JPATH_ROOT.DS.'modules';
			$langbase = JPATH_ROOT;
		}

		jimport('joomla.filesystem.folder');
		$dirs = JFolder::folders($path);

		foreach($dirs as $dir)
		{
			if(substr($dir, 0, 4) == 'mod_')
			{
				$files = JFolder::files($path.DS.$dir, '^([_A-Za-z0-9]*)\.xml$');
				$module = array(
					'file' => $files[0],
					'module' => str_replace('.xml', '', $files[0]),
					'name' => str_replace('.xml', '', $files[0]),
					'path' => $path.DS.$dir,
				);

				$data = JApplicationHelper::parseXMLInstallFile($module['path'].DS.$module['file']);

				if($data['type'] == 'module')
				{
					$module['name'] = $data['name'];
				}

				$modules[] = $module;
			}
		}

		// sort array of objects alphabetically by name
		usort($modules, array('modAPQuickLaunchHelper', '_moduleCompare'));

		$count = 0;
		$n = count($modules);
		for ($i = 0; $i < $n; $i++)
		{
			$module = $modules[$i];

			$text = $module['name'];
			$link = 'index.php?option=com_modules&client='.$clientId.'&filter_type='.$module['module'];
			$imgsrc = modAPQuickLaunchHelper::_getImgSrc($module['module'].'.png');

			modAPQuickLaunchHelper::_renderIcon($link, $imgsrc, $text);

			$count++;
			if($count >= $iconsShownPerRow)
			{
				$count = 0;
				print "<div style=\"clear: both\"></div>\n";
			}
		}
	}

	function _renderIcon($link, $imgsrc, $text)
	{
		print "<div class=\"icon\" style=\"float: left\">";
		print "<a href=\"".$link."\">";
		print "<img src=\"".$imgsrc."\" /><br />"; 
		print $text;
		print "</a>";
		print "</div>\n";
	}
	function _getImgSrc($filename)
	{
		$imgPath = 'templates/adminpraise2/images/logos/';

		$imgsrc = $imgPath.$filename;
		if(!file_exists($imgsrc))
		{
			$imgsrc = $imgPath.'missing.png';
		}
		return $imgsrc;
	}

	function _moduleCompare($moduleA, $moduleB)
	{
		if($moduleA['name'] == $moduleB['name'])
		{
			return 0;
		}

		return ($moduleA['name'] < $moduleB['name']) ? -1 : 1;
	}
}

