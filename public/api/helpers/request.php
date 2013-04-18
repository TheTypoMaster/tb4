<?php
/**
 * @version		$Id: wshelper.php  Sudhi Seshachala $
 * @package		JWS
 * @subpackage	Admin
 * @copyright	Copyright (C) 2005 - 2010 Hooduku/Plexicloud. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
class RequestHelper {
		
	/*
	 * Check for a POST or GET field
	 * 
	 * return: value of field of it exists
	 */
	 
	 // TODO: THIS IS NOT NEEDED - REVERT BACK TO STD JOOMLA
	 // libraries/joomla/environment/request.php
	 
	public function validate($field = "") {
		if (isset($_POST[$field]) && $_POST[$field] != "") {
			$response = $_POST[$field];
		} elseif (isset($_GET[$field]) && $_GET[$field] != "") {
			$response = $_GET[$field];
		} else {
			$response = NULL;
		}

		return $response;

	}

}
?>