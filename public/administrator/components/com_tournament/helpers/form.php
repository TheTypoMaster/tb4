<?php
defined('_JEXEC') or die();

class FormHelper
{
	public static function getDefaultList($field_list, $model, $submit_list = array())
	{
		$default_list = array();
		foreach($field_list as $field => $default) {
			$default_list[$field] = self::_selectDefault($field, $default, $model, $submit_list);
		}

		return $default_list;
	}

	private static function _selectDefault($field, $default, $model, $submit_list)
	{
		if($model->$field != SuperModel::UNDEFINED) {
			$default = $model->$field;
		}

		if(array_key_exists($field, $submit_list)) {
			$default = $submit_list[$field];
		}

		return $default;
	}

	public static function getSelectedList($option_list, $value = null)
	{
		$selected_list = array_fill_keys(array_keys($option_list), '');

		if(array_key_exists($value, $selected_list)) {
			$selected_list[$value] = ' selected="checked"';
		}

		return $selected_list;
	}
}