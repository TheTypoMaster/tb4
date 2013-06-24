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
	 * @param todo
	 * @return Response
	 */
	public function store()
	{
		// TODO: Move to config
		$userName = Config::get('igas.topbetta.api.userName');
		$userPassword = Config::get('igas.topbetta.api.userPassword');
		$secretKey = Config::get('igas.topbetta.api.secretKey');
		$companyID = Config::get('igas.topbetta.api.companyID');
		$command = Config::get('igas.topbetta.api.command');
		$remoteHost = Config::get('igas.topbetta.api.remoteHost');
		
		$paramslist = Request::get('remote');
		
		// validate input variables
				
		// check last state in DB - need model
		
		// grab some config data for the remote
		
		// generate datakey
		$dataKey = TopBetta\IgasDataKey::getDataKey($userName, $userPassword, $companyID, $paramslist, $secretKey);
		
		// Build up JSON request object
		$payloadArray = array('Username' => $userName, 'Password' => $userPassword, 
								'CompanyID' => $companyID, 
								'CompanyPushUrl' => 'http://testing1.mugbookie.com',
								'CurrentTime' => "$serverTime",
								'DataKey' => $dataKey);
			
		$jsonPayload = json_encode($payloadArray);
		
		// check current state
		$requestResponse = TopBetta\CurlRequestHelper::curlRequest($remote, $command, 'POST', $jsonPayload);
		
		// store status change in DB
		
		// Email on status change
		
		return "Post some data here";
	}
	

	
	
	
}