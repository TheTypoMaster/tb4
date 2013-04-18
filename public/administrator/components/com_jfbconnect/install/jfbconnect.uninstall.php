<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC' ) or die('Restricted access' );

$dbo = JFactory::getDBO();
$dbo->setQuery(
	"UPDATE #__plugins ".
	"SET published = 0 ".
	"WHERE (element = 'jfbconnectauth' AND folder = 'authentication') ".
	"	OR (element = 'jfbcsystem' AND folder = 'system') ".
	"	OR (element = 'jfbconnectuser' AND folder = 'user')"
);
$dbo->query();

?>
<p>The main plugins associated with JFBConnect have been disabled.  If you will no longer be using JFBConnect, you can uninstall them in the Install Manager.</p>
