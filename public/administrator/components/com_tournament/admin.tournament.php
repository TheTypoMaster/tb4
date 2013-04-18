<?php
/**
 * @name Tournament Admin component initialisation
 * @desc Initialises the tournament component for admin
 * @package com_tournament
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$controller_name = JRequest::getVar('controller', 'tournament');

if($controller_name == 'tournament') {
  require_once JPATH_COMPONENT . DS . 'controller.php';
} else {
  require_once JPATH_COMPONENT . DS . 'controllers' . DS . $controller_name . '.php';
}
require_once JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php';

// add the ListViewHelper
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'listview.php';

// add the form helper
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'form.php';

// add the helper
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'helper.php';

// set the class name for the controller
$class_name = $controller_name . 'controller';

// instantiate controller class
$controller = new $class_name(array('default_task' => 'listView'));
//var_dump($controller);
//exit;
// set the model path to share for front and back end
$tournament_model_path = JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models';
$controller->addModelPath($tournament_model_path);

// set the betting model path to load betting related models
$betting_model_path = JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models';
$controller->addModelPath($betting_model_path);

// set the model path to load payment models
$payment_model_path = JPATH_SITE . DS . 'components' . DS . 'com_payment' . DS . 'models';
$controller->addModelPath($payment_model_path);

// set the model path to load payment models
$tournament_model_path = JPATH_SITE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models';
$controller->addModelPath($tournament_model_path);

// Perform the Request task
$controller->execute( JRequest::getVar('task') );

// Redirect if set by the controller
$controller->redirect();
?>
