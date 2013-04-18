<?php

defined('_JEXEC') or die();

jimport('joomla.base.object');

/**
 * Value Object parent class
 *
 * @author declan.kennedy
 * @package mobileactive
 */
abstract class ValueObject extends JObject
{
	/**
	 * Prefix for get methods
	 *
	 * @var string
	 */
	const METHOD_GET = 'get';

	/**
	 * Prefix for set methods
	 *
	 * @var string
	 */
	const METHOD_SET = 'set';

	/**
	 * Prefix for add methods
	 *
	 * @var string
	 */
	const METHOD_ADD = 'add';

	/**
	 * Magic call method
	 *
	 * @param string 	$method
	 * @param array 	$args
	 * @throws Exception
	 * @return mixed
	 */
	public function __call($method, $args) {
		$prefix = $this->_getCallMethodPrefix($method);
		$member = $this->_getCallMethodMember($method);

		if($prefix == self::METHOD_ADD) {
			$member .= '_list';
		}

		$class = get_class($this);
		if(!$this->_isMember($member)) {
			throw new Exception("Member {$class}::{$member} does not exist");
		}

		switch($prefix) {
			case self::METHOD_GET:
				$result = $this->_callGetMethod($member);
				break;
			case self::METHOD_SET:
				$result = $this->_callSetMethod($member, $args);
				break;
			case self::METHOD_ADD:
				if(!is_array($this->$member)) {
					throw new Exception("Member {$class}::{$member} is not an array");
				}
				$result = $this->_callAddMethod($member, $args);
				break;
			default:
				throw new Exception("Method {$class}::{$method} does not exist");
		}

		return $result;
	}

	/**
	 * Check if a string matches an object member name
	 *
	 * @param string $member
	 * @return bool
	 */
	private function _isMember($member) {
		$vars = get_object_vars($this);
		return (array_key_exists($member, $vars));
	}

	/**
	 * Get helper for __call
	 *
	 * @param string $member
	 * @return mixed
	 */
	private function _callGetMethod($member) {
		return $this->$member;
	}

	/**
	 * Set helper for __call
	 *
	 * @param string 	$member
	 * @param array 	$args
	 * @return bool
	 */
	private function _callSetMethod($member, $args) {
		return ($this->$member = $args[0]);
	}

	/**
	 * Add helper for __call
	 *
	 * @param string 	$member
	 * @param array 	$args
	 * @return integer
	 */
	private function _callAddMethod($member, $args) {
		return array_push($this->$member, $args[0]);
	}

	/**
	 * Converts them studly caps to underscores y'all
	 *
	 * @param string $string
	 * @return string
	 */
	private function _convertStudlyCapsToUnderscores($string) {
		$string = preg_replace('/[A-Z]+/', '_$0', $string);
		return strtolower($string);
	}

	/**
	 * Strip the end of a called method to determine which type of method was called
	 *
	 * @param string $method
	 * @return string
	 */
	private function _getCallMethodPrefix($method) {
		return preg_replace('/[A-Z]{1}.*$/', '', $method);
	}

	/**
	 * Get the suffix for a called method
	 *
	 * @param $method
	 */
	private function _getCallMethodSuffix($method) {
		return preg_replace('/^[a-z]+/', '', $method);
	}

	/**
	 * Get the class member to which a method would apply
	 *
	 * @param string $method
	 * @return string
	 */
	private function _getCallMethodMember($method) {
		$suffix = $this->_getCallMethodSuffix($method);
		return $this->_convertStudlyCapsToUnderscores($suffix);
	}

	/**
	 * Children can use this method to cleanly allow for optional method chaining within returns
	 *
	 * @param object 	$item
	 * @param boolean 	$chain
	 * @return mixed
	 */
	protected function _chain($item, $chain = true)
	{
		return ($chain) ? $this : $item;
	}
}