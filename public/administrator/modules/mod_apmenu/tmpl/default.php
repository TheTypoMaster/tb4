<?php

defined('_JEXEC') or die('Restricted access');

$menu = modAPMenuHelper::getMenu($params->get('menutype'));
modAPMenuHelper::renderMenu($menu, true);

?>
