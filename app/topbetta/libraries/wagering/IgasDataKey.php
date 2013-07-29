<?php namespace TopBetta\libraries\wagering;

use TopBetta;

// Class to generate or validate the DATA key for iGAS

class IgasDataKey {
	
	public static function getDataKey($userName, $userPassword, $companyID, $paramslist, $secretKey){
		// Get input object params
	
		$paramListBetData = '';
		$paramList = $userName . $userPassword . $companyID;
		foreach($paramslist as $param){
			$paramListBetData .= $param;
		}
		
		// join params together
		// concatinate with secret key
		$paramsPlusSecret = $paramList . $paramListBetData . $secretKey;
		// generate HASH
		$hashedParams = md5($paramsPlusSecret);
		
		return $hashedParams;
	
		// append generated sequence to function call request
	
	
	}
	public function checkDataKey(){
		
	}
	
	
	
}


?>