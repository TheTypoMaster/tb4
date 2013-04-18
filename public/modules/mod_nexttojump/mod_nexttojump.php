<?php
/**
* @version		$Id: mod_whosonline.php 9764 2007-12-30 07:48:11Z ircmaxell $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

$menu			= & JSite::getMenu();
$is_homepage	= ($menu->getActive() == $menu->getDefault());

if ($is_homepage) {
	$next_to_jump_list = array(
		'galloping'		=> modNextToJumpHelper::getHomepageNextToJump('galloping', 5),
		'harness'		=> modNextToJumpHelper::getHomepageNextToJump('harness', 5),
		'greyhounds'	=> modNextToJumpHelper::getHomepageNextToJump('greyhounds', 5),
	);
	
} else {
	$next_to_jump_list = modNextToJumpHelper::getNextToJump(null, 8);
}
require(JModuleHelper::getLayoutPath('mod_nexttojump'));
