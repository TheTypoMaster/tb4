<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
define( 'COM_TOURNAMENT_DIR', 'images'.DS.'tournament'.DS );
define( 'COM_TOURNAMENT_BASE', JPATH_ROOT.DS.COM_TOURNAMENT_DIR );
define( 'COM_TOURNAMENT_BASEURL', JURI::root().str_replace( DS, '/', COM_TOURNAMENT_DIR ));

// Require wagering library
jimport( 'mobileactive.wagering.bet' );

// Require the base controller
$controller_name = JRequest::getVar('controller', null);
if(is_null($controller_name)) {
  require_once JPATH_COMPONENT . DS . 'controller.php';
  $controller = new TournamentController(array('default_task' => 'display'));
} else {
  require_once JPATH_COMPONENT . DS . 'controllers' . DS . $controller_name . '.php';
  $controller_name .= 'Controller';
  $controller = new $controller_name(array('default_task' => 'display'));
}

$component_list = array('betting', 'payment', 'tournamentdollars','topbetta_user');
foreach($component_list as $component) {
	$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
	$controller->addModelPath($path);
}

// Require the helper
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'helper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php';
// Perform the Request task
$controller->execute(JRequest::getVar('task' ));

// Redirect if set by the controller
$controller->redirect();