<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = & JFactory::getDocument();

// Require the base controller
require_once (JPATH_COMPONENT . DS . 'controller.php');
// Require JMAIL
require_once( JPATH_ROOT.DS.'libraries' . DS . 'joomla' . DS . 'mail' . DS . 'mail.php' );

// Create the controller
$controller = new UserReferralController();
// set topbetta user model path to load topbetta user model
$user_model_path = JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models';
$controller->addModelPath($user_model_path);

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
