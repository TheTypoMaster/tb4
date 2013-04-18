<?php
/**
 * @version		$Id: output.php  Michael Costa $
 * @package		
 * @subpackage	Admin
 * @copyright	Copyright (C) 2002 Go Big Media. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
class OutputHelper {

	public function json($status = 500, $data = array()) {
		// set the HTTP header content type & status code	
		header("content-type: application/json");
		//header('Access-Control-Allow-Origin: http://m.topbetta.com');
		//header('Access-Control-Allow-Origin: http://mugbookie.com');
		//header('Access-Control-Allow-Origin: http://www.topbetta.com');
		header('Access-Control-Allow-Origin: *');		
		//header(':', true, $status);
		
		$data['status'] = $status;
		$response = json_encode($data);

		return $response;

	}
	
	public function api_link($method, $data) {
		$api_url = "https://www.topbetta.com/api/?";
		
		$return = $api_url . 'method=' . $method . '&' . $data;
		
		return $return;
	}
	
	public function _debug($var = '', $exit = TRUE) {
		if ($var) {
			echo "<pre>";
			var_dump($var);
			echo "</pre>";
			($exit) ? exit : NULL;
		}
	}

}
?>
