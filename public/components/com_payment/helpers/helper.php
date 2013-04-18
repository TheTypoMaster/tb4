<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: helper.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * payment Helper
 * 
 */
class PaymentHelper
{
	/**
	* Method to replace variables
	* 
	* @param array an associate array of replacement values
	* @param string the text which contains replacement variables
	* @return string the text after replacement
	*/
	function variableReplace( array $replacements, $text )
	{
		return str_replace(array_keys($replacements), array_values($replacements), $text);   
	}
}
?>