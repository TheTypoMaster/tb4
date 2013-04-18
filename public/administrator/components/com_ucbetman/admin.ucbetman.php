<?php
/**
* @package sportman 01
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the HELLOWORD component. 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: 
// specific controller?
// Require specific controller if requested
//if no controller then default controller = 'sportman'
$controller = JRequest::getVar('controller','ucbetman' ); 

//set the controller page  
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');

// Create the controller sportmanController 
$classname  = $controller.'controller';

//create a new class of classname and set the default task:display
$controller = new $classname( array('default_task' => 'display') );

// Perform the Request task
$controller->execute( JRequest::getVar('task' ));

// Redirect if set by the controller
$controller->redirect(); 
?>
