<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require wagering library
jimport( 'mobileactive.wagering.bet' );

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'helpers' . DS . 'helper.php';

// Initialize the controller
$controller = new BettingController();

$path = JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models';
$controller->addModelPath($path);

$path = JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models';
$controller->addModelPath($path);

$controller->execute(JRequest::getCmd('task', 'display' ));
$controller->redirect();

?>