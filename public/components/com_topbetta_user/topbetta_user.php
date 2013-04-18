<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = & JFactory::getDocument();
$document->addStyleSheet('components/com_topbetta_user/assets/general.css');

// Require the base controller
require_once (JPATH_COMPONENT . DS . 'controller.php');

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Create the controller
$controller = new topbettaUserController();

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
