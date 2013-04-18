<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$menu = array(
	array(
		'id' => 'usermanager1', 
		'name' => 'User Manager', 
		'link' => AdminPraiseHelper::getUserLink(null, null, null),
		'type' => 'url', 
		'parent' => 0, 
		'params' => 'menu_image=-1', 
		'access' => 0,
		'children' => array()
	),
);

modAPMenuHelper::renderMenu($menu);

?>

