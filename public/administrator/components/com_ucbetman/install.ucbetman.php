<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: install.uc_betman.php 2009-08-07 04:40:27 svn $
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

// Initialize the database
$db =& JFactory::getDBO();
$update_queries = array ();

// Perform all queries - we don't care if it fails
foreach( $update_queries as $query ) {
    $db->setQuery( $query );
    $db->query();
}
?>