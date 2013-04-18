<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';

$view = JRequest::getCmd('view', '');

if ($view == "loginregister")
{
    require_once JPATH_COMPONENT . DS . 'controllers' . DS . $view . '.php';
    $classname = 'JFBConnectControllerLoginRegister';
}
else
{
    $controller = JRequest::getCmd('controller', '');
    if ($controller != '')
        require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controller . ".php");

    require_once JPATH_COMPONENT . DS . 'controller.php';
    $classname = 'JFBConnectController' . $controller;
}

$controller = new $classname();

$controller->execute(JRequest::getCmd('task'));
$controller->redirect();