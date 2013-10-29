<?php namespace TopBetta;

class RisaForm extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
    
    protected $table = 'tb_data_risa_runner_form';
    
    // check if runner_code exists in DB already
    static public function checkForRunnerCode($runnerCode) {
    	return  RisaForm::where('runner_code', '=', $runnerCode)-> pluck('id');
    }
    

}