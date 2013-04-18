<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
define('COM_PAYMENT_DIR', 'images' . DS . 'payment' . DS);
define('COM_PAYMENT_BASE', JPATH_ROOT . DS . COM_PAYMENT_DIR);
define('COM_PAYMENT_BASEURL', JURI::root() . str_replace(DS, '/', COM_PAYMENT_DIR));

// Require the base controller
require_once JPATH_COMPONENT . DS . 'controller.php';

// Require wagering library
jimport( 'mobileactive.wagering.bet' );

if($c = JRequest::getVar('c')) {
	require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $c . '.php');
}

// Require the base controller
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'helper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php';
require_once JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'helpers' . DS . 'helper.php';

// Initialize the controller
$c = 'PaymentController' . $c;
$controller = new $c();

$component_list = array('tournament', 'payment', 'betting', 'topbetta_user');
foreach($component_list as $component) {
	$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
	$controller->addModelPath($path);
}

// Perform the Request task
$controller->execute(JRequest::getCmd('task', 'display'));
$user =& JFactory::getUser();

//redirect login page if the user doesn't login
if($user->guest) {
	//if the user is not logged in, redirect to create account page
	$creatAccountUrl = JURI::base() . 'index.php?option=com_topbetta_user&task=register';
	$controller->setRedirect( $creatAccountUrl );
}

$controller->redirect();
?>