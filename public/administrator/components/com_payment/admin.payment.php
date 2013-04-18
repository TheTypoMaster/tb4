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

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';
if( $c = JRequest::getVar('c') )
{
	require_once(JPATH_COMPONENT.DS.'controllers'.DS.$c.'.php');
}

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
require_once(JPATH_ROOT.DS.'components'.DS.'com_topbetta_user'.DS.'helpers'.DS.'helper.php');
// Initialize the controller
$c = 'PaymentController' . $c;
$controller = new $c();

$controller->execute( JRequest::getCmd('task', 'display' ));
$controller->redirect();


$document = &JFactory::getDocument();
$document->addScript('/media/system/js/tabs.js' );

?>