<?php namespace TopBetta\Services\Html; 


use Form;

/**
 * Coded by Oliver Shanahan
 * File creation date: 18/05/15
 * File creation time: 14:29
 * Project: tb5
 */
 
class FormBuilder extends \Collective\Html\FormBuilder{

	/**
	 * Add datetime
	 * @param $name
	 * @param $value
	 * @param $options
	 * @return string
	 */
	public function datetime($name, $value, $options){

		$class = array_get($options, 'class');

		return "<div class='input-group datepicker'>
                    <input type='text' class='form-control $class' name='$name' id='$name' readonly value='$value'/>
                    <span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>";
	}

//	public function selectMonth($name, $selected, $options){
//		$months = array();
//
//		foreach (range(1, 12) as $month)
//		{
//			$months[$month] = strftime('%B', mktime(0, 0, 0, $month, 1));
//		}
//		return Form::select($name, $months, $selected, $options);
//	}
//
//	public function selectYear($name, $selected, $options){
//		$years = array();
//
//		foreach (range(date('Y')-4, date('Y')) as $year)
//		{
//			$years[$year] = $year;
//		}
//
//		return Form::select($name, $years, $selected, $options);
//	}
}