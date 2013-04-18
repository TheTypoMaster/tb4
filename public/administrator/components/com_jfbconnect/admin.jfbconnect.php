<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

//JHtml::_('behavior.framework', false); // false = no Mootools More required

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_jfbconnect/assets/default.css");
$document->addScript("components/com_jfbconnect/assets/jfbconnect-admin.js");

require_once (JPATH_COMPONENT . DS . 'controller.php');

$view = JRequest::getCmd('controller', '');
if ($view == "")
	$view = JRequest::getCmd('view', '');

//Live Update

 //SC16

if ($view != '' && $view != "jfbconnect") // Don't do this for the main landing page. Fix this system
{
    require_once (JPATH_COMPONENT . DS . 'controllers' . DS . strtolower($view) . '.php');
    $controllerName = $view;
}
else
    $controllerName = "";


$classname = 'JFBConnectController' . ucfirst($controllerName);
$controller = new $classname();

$controller->execute(JRequest::getVar('task'));
include_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'assets' . DS . 'footer' . DS . 'footer.php');
$controller->redirect();
