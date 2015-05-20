<?php namespace TopBetta\Models;

class RisaLastStarts extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
    
    protected $table = 'tb_data_risa_runner_form_last_starts';
    
    // check if runner_code exists in DB already
    static public function checkForRunnerLastStart($raceCode, $runnerCode) {
    	return  RisaLastStarts::where('runner_code', $runnerCode)
    							->where('race_code', $raceCode)			
    							-> pluck('id');
    }
}