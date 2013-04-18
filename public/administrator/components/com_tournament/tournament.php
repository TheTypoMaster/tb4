<?php
/**
 * @name Tournament component initialisation
 * @desc Initialises the tournament component for admin
 * @package com_tournament
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
define( 'COM_TOURNAMENT_DIR', 'images'.DS.'tournament'.DS );
define( 'COM_TOURNAMENT_BASE', JPATH_ROOT.DS.COM_TOURNAMENT_DIR );
define( 'COM_TOURNAMENT_BASEURL', JURI::root().str_replace( DS, '/', COM_TOURNAMENT_DIR ));

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

// Initialize the controller
$controller = new TournamentController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>