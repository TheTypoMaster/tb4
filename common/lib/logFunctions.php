<?php

/**
 * Normal message label
 *
 * @var string
 */
define('LOG_TYPE_NORMAL',   '');

/**
 * Error message label
 *
 * @var string
 */
define('LOG_TYPE_ERROR',    'Error: ');

/**
 * Debugging message label
 *
 * @var string
 */
define('LOG_TYPE_DEBUG',    'Debug: ');

/**
 * Default time format for log messages
 *
 * @var string
 */
define('LOG_TIME_FORMAT_DEFAULT', 'r');

/**
 * Debugging switched off
 *
 * @var integer
 */
define('DEBUG_TYPE_OFF',    0);

/**
 * Debug SQL queries
 *
 * @var integer
 */
define('DEBUG_TYPE_QUERY',  1);

/**
 * Debug data arrays
 *
 * @var integer
 */
define('DEBUG_TYPE_DATA',   2);

/**
 * Debug SOAP request and response XML
 *
 * @var integer
 */
define('DEBUG_TYPE_SOAP',   3);

/**
 * Output a log message
 *
 * @return void
 */
function l($message, $type = null, $time_format = null, $add_new_line = true) {
  if(is_null($type)) {
    $type = LOG_TYPE_NORMAL;
  }

  $time = formatLogTime($time_format);
  $suffix = ($add_new_line) ? "\n" : '';

  echo sprintf('%s %s%s %s', $time, $type, $message, $suffix);
}

/**
 * Get a string timestamp to prefix log messages
 *
 * @param string $format
 * @return string
 */
function formatLogTime($format = null) {
  if(is_null($format)) {
    $format = LOG_TIME_FORMAT_DEFAULT;
  }

  return '[' . date($format) . ']';
}