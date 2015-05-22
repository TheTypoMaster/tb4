<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta;

class TestJSONController extends \BaseController {

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
	
	
	public function __construct()
	{
	 	//$this->beforeFilter('apiauth');
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		// return RaceMeetings::all();
		return "Test API Index";
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		
		// Log this
		TopBetta\Helpers\LogHelper::l("BackAPI: TEST - Reciving POST");
		
		// get the JSON POST
		$racingJSON = \Input::json();
		//$racingJSON = unserialize(file_get_contents('/tmp/backAPIracing-20130614072624'));
		$jsonSerialized = serialize($racingJSON);

		$o = print_r($racingJSON,true);
		
		
		if($this->debug){
			$timeStamp = date("YmdHis");
			\File::append('/tmp/backAPItest-'.$timeStamp, $jsonSerialized);
			\File::append('/tmp/backAPItestarray-'.$timeStamp, $o);
			
			
		}
		
		// make sure JSON was received
		$keyCount = count($racingJSON);
		if(!$keyCount){
			TopBetta\Helpers\LogHelper::l("BackAPI: Racing - No Data In POST",2);
			return \Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

		
		return \Response::json(array(
				'error' => false,
				'message' => 'OK: Processed Successfully'),
				200
		);
		//return RaceMeetings::all();
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	
	
	
}