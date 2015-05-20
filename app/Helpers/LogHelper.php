<?php namespace TopBetta;

/* Name: LogHelper
 * Purpose: Add a simple debug and log format wrapper to the laravel log class
 * 
 */

class LogHelper {
	
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
	const DEBUG_ON = true;
	
	
	/**
	 * Log a message to laravel logs
	 *
	 * @param string 	$message
	 * @param integer 	$type
	 * @param string 	$time_format
	 * @param boolean 	$add_new_line
	 */
	static public function l($message, $type = null, $show_time = true, $time_format = null, $add_new_line = true) {
		if(is_null($type)) {
			$type = self::LOG_TYPE_NORMAL;
		}
	
		if($type == self::LOG_TYPE_DEBUG && self::DEBUG_ON == FALSE){ 
			return 0;
		}
	
		if(self::LOG_TIME_SHOWN){
			$time = SELF::_formatLogTime($time_format);
		}else{
			$time = '';
		}
	
	
		//$processPID = getmypid();
	
		$prefix = array(
				self::LOG_TYPE_NORMAL => 'Info: ',
				self::LOG_TYPE_DEBUG =>  'Debug: ',
				self::LOG_TYPE_ERROR =>  'Error: '
		);
	
		$suffix = ($add_new_line) ? "\n" : '';
	
		\Log::info(sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix));
		//echo sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix);
	}
	
	/**
	 * Format the timestamp for a log message
	 *
	 * @param string $format
	 */
	private function _formatLogTime($format = null) {
		if(is_null($format)) {
			$format = self::LOG_TIME_FORMAT_DEFAULT;
		}
	
		return '[' . date($format) . ']';
	}
	
}
