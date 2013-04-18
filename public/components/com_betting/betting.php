<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
define( 'COM_BETTING_DIR', 'images'.DS.'betting'.DS );
define( 'COM_BETTING_BASE', JPATH_ROOT.DS.COM_BETTING_DIR );
define( 'COM_BETTING_BASEURL', JURI::root().str_replace( DS, '/', COM_BETTING_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require wagering library
jimport( 'mobileactive.wagering.bet' );
jimport('mobileactive.wagering.api');

// Initialize the controller;
$controller = new BettingController();

$component_list = array('tournament', 'topbetta_user');
foreach($component_list as $component) {
	$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
	$controller->addModelPath($path);
}

// Require the helper
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'helper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'helpers' . DS . 'helper.php';

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();