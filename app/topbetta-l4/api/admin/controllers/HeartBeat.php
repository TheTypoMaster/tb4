<?php namespace TopBetta\admin;

use TopBetta;

class HeartBeatController extends \BaseController {

	/**
	 * Default log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_NORMAL = 0;
	
	/**
	 * Debug log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_DEBUG = 1;
	
	/**
	 * Log message type for errors
	 *
	 * @var integer
	 */
	const LOG_TYPE_ERROR = 2;
	
	/**
	 * Default time formatting string for log messages
	 *
	 * @var string
	 */
	const LOG_TIME_FORMAT_DEFAULT = 'r';
	
	/**
	 * Show time string in log messages
	 *
	 * @var string
	 */
	const LOG_TIME_SHOWN = false;
	
	/**
	 * Debugging mode flag
	 *
	 * @var boolean
	 */
	private $debug = true;
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		return "Nothing to see here";
	}

	/**
	 * Trigger the heartbeat
	 * .
	 * @param providerName, companyurl, remotehost
	 * @return Response
	 */
	public function store()
	{
		$heartBeatService = \Input::get('provider');
		$companyPushUrl = \Input::get('pushUrl');
		$remoteHost = \Input::get('remoteHost');
		
		// $heartBeatService = 'igas_schedule';
		
		// get stuff from the config
		$userName = \Config::get('igasauthentication.userName');
		$userPassword = \Config::get('igasauthentication.userPassword');
		$secretKey = \Config::get('igasauthentication.secretKey');
		$companyID = \Config::get('igasauthentication.companyID');
		$command = \Config::get('igasauthentication.command');
		// $remoteHost = \Config::get('igasauthentication.remoteHost');
		
		$serverTime = date("Y-m-d H:i:s");
		
		// build up array 
		$payloadArray = array('Username' => $userName, 'Password' => $userPassword,
				'CompanyID' => $companyID,
				'CompanyPushUrl' => $companyPushUrl,
				'CurrentTime' => "$serverTime",
				);
		
		// generate datakey
		$dataKey = TopBetta\libraries\wagering\IgasDataKey::getDataKey($userName, $userPassword, $companyID, $payloadArray, $secretKey);
		
		// add data key to array
		$payloadArray['DataKey'] = $dataKey;
		
		// encode array as JSON
		$jsonPayload = json_encode($payloadArray);

		// check last remote host state
		$lastStatusObject = Topbetta\HeartbeatStatus::where('heartbeat_endpoint', '=', "$heartBeatService")->get();
		
		if(is_array($lastStatusObject[0])){
			// return error
			return "$serverTime: ERROR: Service not found in DB";
		}
		
		// grab the status and id
		$lastStatusID = $lastStatusObject[0]->id;
		$lastStatus = $lastStatusObject[0]->last_status;
				
		// check current remote host state
		$currentStatus = json_decode(TopBetta\CurlRequestHelper::curlRequest($remoteHost, $command, 'POST', $jsonPayload));
		
		if(isset($currentStatus->ErrorNo)){
			if ($currentStatus->ErrorNo == "0") {
				$currentStatus = "up";
			}	
		}else{
			$currentStatus = "down";
		}

		
		if($lastStatusObject[0]->last_status == $currentStatus){
			return "$serverTime: No change. last:$lastStatus, current:$currentStatus";
		}
		
		// store status change in DB
		$lastStatusObject[0]->last_status = $currentStatus;
		$lastStatusObject[0]->save();
		
		// Email on status change
		$emailSubject = "iGAS Schedule($heartBeatService): Status changed: ".$currentStatus.".";
		$emailDetails = array( 'email' => 'oliver@topbetta.com', 'to_name' => 'Oliver', 'from' => 'hearbeat@topbetta.com', 'from_name' => 'TopBetta iGAS Schedule Heartbeat', 'subject' => "$emailSubject" );
		$newEmail = \Mail::send('hello', $emailDetails, function($m) use ($emailDetails)
		{
			$m->from($emailDetails['from'], $emailDetails['from_name']);
			$m->to($emailDetails['email'], $emailDetails['to_name'])->subject($emailDetails['subject']);
		});
		
		// return error
		return "$serverTime: Status changed. last:$lastStatus, current:$currentStatus";
	}
}