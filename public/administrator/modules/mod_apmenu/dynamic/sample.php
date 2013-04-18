<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$menu = array(
	array(
		'id' => 'sample1', 
		'name' => 'JoomlaPraise', 
		'link' => 'http://www.joomlapraise.com/', 
		'type' => 'url', 
		'parent' => 0, 
		'params' => 'menu_image=-1', 
		'access' => 0,
		'children' => array()
	),
	array(
		'id' => 'sample2', 
		'name' => 'SourceCoast', 
		'link' => 'http://www.sourcecoast.com/', 
		'type' => 'url', 
		'parent' => 0, 
		'params' => 'menu_image=-1', 
		'access' => 0,
		'children' => array()
	),
	array(
		'id' => 'sample3', 
		'name' => 'CMS Market', 
		'link' => 'http://www.cmsmarket.com/', 
		'type' => 'url', 
		'parent' => 0, 
		'params' => 'menu_image=-1', 
		'access' => 0,
		'children' => array()
	),
);

modAPMenuHelper::renderMenu($menu);

?>

