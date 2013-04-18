<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$menu = array(
	array(
		'id' => 'user1', 
		'name' => 'Users', 
		'link' => AdminPraiseHelper::getUserLink(null, null, null),
		'type' => 'url', 
		'parent' => 0, 
		'params' => 'menu_image=-1', 
		'access' => 0,
		'children' => array(
			array(
				'id' => 'users1', 
				'name' => 'All Users', 
				'link' => AdminPraiseHelper::getUserLink(null, null, null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users2', 
				'name' => 'Separator', 
				'link' => '',
				'type' => 'separator', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users3', 
				'name' => 'Public', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Public%20Frontend', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users4', 
				'name' => 'Registered', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Registered', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users5', 
				'name' => 'Author', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Author', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users6', 
				'name' => 'Editor', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Editor', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users7', 
				'name' => 'Publisher', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Publisher', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users8', 
				'name' => 'Backend', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Public%20Backend', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users9', 
				'name' => 'Manager', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Manager', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users10', 
				'name' => 'Administrator', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Administrator', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users11', 
				'name' => 'Super Administrator', 
				'link' => AdminPraiseHelper::getUserLink(null, 'Super%20Administrator', null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users12', 
				'name' => 'Separator', 
				'link' => '',
				'type' => 'separator', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
			array(
				'id' => 'users13', 
				'name' => 'New User', 
				'link' => AdminPraiseHelper::getUserLink('add', null, null),
				'type' => 'url', 
				'parent' => 0, 
				'params' => 'menu_image=-1', 
				'access' => 0,
				'children' => array()
			),
		)
	),
);

modAPMenuHelper::renderMenu($menu);

?>

