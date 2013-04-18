<?php

defined('_JEXEC') or die();

/**
 * This is used to provide a shared place for function names and constants related to them.
 *
 * @author declan.kennedy
 * @package mobileactive
 */
class DatabaseQueryHelperFunction
{
	/**
	 * Count query identifier
	 *
	 * @var integer
	 */
	const COUNT = 0;

	/**
	 * Min query identifier
	 *
	 * @var integer
	 */
	const MIN = 1;

	/**
	 * Max function identifier
	 *
	 * @var integer
	 */
	const MAX = 2;

	/**
	 * Sum function identifier
	 *
	 * @var integer
	 */
	const SUM = 3;

	/**
	 * Now function identifier
	 *
	 * @var integer
	 */
	const NOW = 4;

	/**
	 * Unix timestamp function identifier
	 *
	 * @var integer
	 */
	const UNIX_TIMESTAMP = 5;

	/**
	 * From unix timestamp conversion function identifier
	 *
	 * @var integer
	 */
	const FROM_UNIXTIME = 6;

	/**
	 * In function identifier
	 *
	 * @var integer
	 */
	const IN = 7;

	/**
	 * LOWER function ID
	 *
	 * @var integer
	 */
	const LOWER = 8;
	
	/**
	 * LOWER function ID
	 *
	 * @var integer
	 */
	const CURDATE = 9;

	/**
	 * Get the name of the function designated by the constant value
	 *
	 * @return string
	 */
	public static function getFunctionName($type)
	{
		$function = '';
		switch($type) {
			case self::MIN:
				$function = 'MIN';
				break;
			case self::MAX:
				$function = 'MAX';
				break;
			case self::SUM:
				$function = 'SUM';
				break;
			case self::UNIX_TIMESTAMP:
				$function = 'UNIX_TIMESTAMP';
				break;
			case self::FROM_UNIXTIME:
				$function = 'FROM_UNIXTIME';
				break;
			case self::NOW:
				$function = 'NOW';
				break;
			case self::IN:
				$function = 'IN';
				break;
			case self::LOWER:
				$function = 'LOWER';
				break;
			case self::CURDATE:
				$function = 'CURDATE';
				break;
			case self::COUNT:
			default:
				$function = 'COUNT';
				break;
		}

		return $function;
	}
}