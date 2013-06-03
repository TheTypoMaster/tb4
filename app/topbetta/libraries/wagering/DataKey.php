<?php namespace TopBetta\backend;

use TopBetta;

// Class to generate or validate the DATA key for iGAS

class DataKey {
	
	public function getDataKey($params, $secretKey){
		// Get input object params
		
		// join params together
		// concatinate with secret key
		$paramsPlusSecret = $joinedParams . $secretKey;
		// generate HASH
		$hashedParams = md5($paramsPlusSecret);
		
		return $hashedParams;
		
		// append generated sequence to function call request
		
		
	}
	
	public function checkDataKey(){
		
	}
	
	
	
}


?>