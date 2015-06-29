<?php namespace TopBetta\Services\Response;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 15:06
 * Project: tb4
 */

use Carbon\Carbon;
use Response;

class ApiResponse {

	protected $responseObject = array();

	/**
	 * @param array $overrides
	 *
	 * @return array
	 */
	protected function addToResponse(array $overrides = array()) {
		$this->responseObject['timestamp'] = Carbon::now()->toDateTimeString();
		$this->responseObject = array_merge($this->responseObject, $overrides);
		return $this->responseObject;
	}

	/**
	 * @param       $content
	 * @param int   $status_code
	 * @param null  $error_code
	 * @param null  $user_message
	 * @param null  $developer_message
	 *
	 * @param array $metaData
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function failed($content, $status_code = 500, $error_code = null, $user_message = null, $developer_message = null, array $metaData = array()){
		$response = $this->addToResponse(array(
			'error_code' => $error_code,
			'user_message' => $user_message,
			'developer_message' => $developer_message,
			'errors' => $content,
			'http_status_code' => $status_code
		));

		$this->addMetaData($metaData);

		return Response::json($this->responseObject, $status_code);
	}

	/**
	 * @param       $content
	 * @param int   $status_code
	 * @param array $metaData
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function success($content, $status_code = 200, array $metaData = array()){
		$this->addToResponse(array(
			'data' => $content,
			'http_status_code' => $status_code
		));

		$this->addMetaData($metaData);

		return Response::json($this->responseObject);
	}

	/**
	 * @param array $metaData
	 *
	 * @internal param $response
	 */
	protected function addMetaData(array $metaData) {
		foreach ($metaData as $key => $value) {
			$this->responseObject[$key] = $value;
		}
	}
} 