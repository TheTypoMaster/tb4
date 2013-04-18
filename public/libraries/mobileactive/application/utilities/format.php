<?php

defined('_JEXEC') or die();

class Format
{
	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	public static function counterText($time, $params = array())
	{
		if($time < time()) {
			return 'PAST START TIME';
		}

		$remaining = $time - time();

		$days     = intval($remaining / 3600 / 24);
		$hours    = intval(($remaining / 3600) % 24);
		$minutes  = intval(($remaining / 60) % 60);
		$seconds  = intval($remaining % 60);
		
		$separator = isset($params['separator']) ? $params['separator'] : ' ';

		$text = $seconds . $separator . (isset($params['second']) ? $params['second'] : 'sec');
		if($minutes > 0) {
			$text = $minutes . $separator . (isset($params['second']) ? $params['minute'] : 'min');
		}

		if($hours > 0) {
			$min_sec_text = '';

			if( $days == 0 )
			{
				$min_sec_text = $text;
			}

			$text = $hours . $separator . (isset($params['hour']) ? $params['hour'] : 'hr') . $separator . $min_sec_text;
		}

		if( $days > 0) {
			$text = $days . $separator . (isset($params['day']) ? $params['day'] : 'd') . $separator . $text;
		}
		return $text;
	}

	/**
	 * Formats an integer to be displayed as currency, optionally adding a dollar sign
	 *
	 * @param integer $amount
	 * @param boolean $add_dollar_sign
	 * @return string
	 */
	public static function currency($amount, $add_dollar_sign = false)
	{
		$text = ($add_dollar_sign) ? '$' : '';
		return $text . number_format(floor($amount) / 100, 2);

	}
	
	/**
	 * Formats a float to be displayed as odds
	 *
	 * @param float $odds
	 * @return string
	 */
	public static function odds($odds)
	{
		return number_format($odds, 2);
	}
	
	/**
	 * Formats a float to be displayed as percentage
	 *
	 * @param float $number
	 * @return string
	 */
	public static function percentage($number)
	{
		return number_format($number, 2) . '%';
	}
	
	/**
	 * Formats an integer to an ordinal number
	 *
	 * @param int $number
	 * @return string
	 */
	public static function ordinalNumber($number)
	{
		if (!is_int($number) && !ctype_digit($number)) {
			return $number;
		}
		if (!in_array(($number % 100),array(11,12,13))) {
			switch ($number % 10) {
				case 1:
					return $number.'st';
				case 2:
					return $number.'nd';
				case 3:
					return $number.'rd';
			}
		}
		return $number.'th';
	}
	
	/**
	 * Format string to the specified length
	 * 
	 * @param string $str
	 * @param int $length
	 * @param string $postfix
	 * @return string
	 */
	public static function cutString($str, $length, $postfix = '')
	{
		if (strlen($str) > $length) {
			$str = substr($str, 0, $length - strlen($postfix));
			$str .= $postfix;
		}
		
		return $str;
	}
}