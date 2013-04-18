<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// DEVNOTE: Pull in the class that will be used to actually display our toolbar.
require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task) 
{
	default:
		TOOLBAR_JFBConnect::_DEFAULT();
		break;
}