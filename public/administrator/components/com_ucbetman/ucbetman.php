<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: uc_betman.php 2009-08-07 04:40:27 svn $
 * @author uc-joomla.net
 * @package Joomla
 * @subpackage uc_betman
 * @license Copyright (c) 2009 - All Rights Reserved
 *
 * sports tournament betting component
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
define( 'COM_UC_BETMAN_DIR', 'images'.DS.'ucbetman'.DS );
define( 'COM_UC_BETMAN_BASE', JPATH_ROOT.DS.COM_UCBETMAN_DIR );
define( 'COM_UC_BETMAN_BASEURL', JURI::root().str_replace( DS, '/', COM_UCBETMAN_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Initialize the controller
$controller = new ucbetmanController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>