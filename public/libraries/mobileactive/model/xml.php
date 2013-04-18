<?php

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class XMLModel extends JModel
{
	/**
	 * The timezone in which feed times arrive
	 *
	 * @var string
	 */
	const TZ_FEED = 'Australia/Brisbane';

	/**
	 * The timezone of the application
	 *
	 * @var string
	 */
	const TZ_LOCAL = 'Australia/Sydney';

	/**
	 * Format to use to generate a mysql datetime compatible time string
	 *
	 * @var string
	 */
	const TIME_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Given a time string this will return a DateTime object which has been shifted to the
	 * current timezone from the feed timezone.
	 *
	 * @param string $time_string
	 * @return DateTime
	 */
	public function getLocalDateTime($time_string = '', $timezone = null) {
		if(is_null($timezone)) {
			$timezone = self::TZ_FEED;
		}

		$date = new DateTime($time_string, new DateTimeZone($timezone));
		$date->setTimezone(new DateTimeZone(self::TZ_LOCAL));

		return $date;
	}

	/**
	 * Format a localised time string using the internal local date method
	 *
	 * @param string $time_string
	 * @param string $timezone
	 * @param string $format
	 * @return string
	 */
	public function formatLocalDateTime($time_string = '', $timezone = null, $format = null) {
		$date = $this->getLocalDateTime($time_string, $timezone);
		if(is_null($format)) {
			$format = self::TIME_FORMAT;
		}

		return $date->format($format);
	}
}