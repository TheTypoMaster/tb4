<?php namespace TopBetta;

/* Name: CurlRequestHelper
 * 
 * 
*/

class CurlRequestHelper {

	public static function curlRequest($remote, $command = null, $requestType = null, $jsonPayload){

		// validate params
		//echo "RES:$remote, Command: $command, RT:$requestType: JSOn: $jsonPayload";
			
		// init curl
		$ch = curl_init($remote."/".$command);
		
		// set curl options
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonPayload))
		);

		$error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		// make the request
		$response = curl_exec($ch);
		curl_close($ch);
		
		// decode response to array
		// $response = json_decode($res);
		
		// check response and return
		if ($response == "") {
			return false;
		} else {
			return $response;
		}
	}
}