<?php namespace TopBetta\Models;

class RisaFormLastStarts extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
    
    protected $table = 'tb_data_risa_runner_form_last_starts';
    
    public function risaForm(){
    	return $this->hasOne('Topbetta\RisaForm', 'id', 'runner_form_id');
    }
    
    
    // check if runner_code exists in DB already
    static public function checkForRunnerCode($runnerCode) {
    	return  RisaForm::where('runner_code', '=', $runnerCode)-> pluck('id');
    }
    
    
    static public function getRunnersFormForRunnerId($runnerId){
    	
    	
    }
    
    
  /*
     * rrf.age as df_age,
                        rrf.colour as df_colour,
                        rrf.sex as df_sex,
                        rrf.career_results as df_career,
                        rrf.track_results as df_tracks,
                        rrf.track_distance_results as df_track_distance,
                        rrf.first_up_results as df_first_up,
                        rrf.second_up_results as df_second_up,
                        rrf.good_results as df_good,
                        rrf.dead_results as df_dead,
                        rrf.slow_results as df_slow,
                        rrf.heavy_results as df_heavy,
                        fls.runner_form_id as ls_id,
                        fls.finish_position as ls_finish_position,
                        fls.race_starters as ls_race_starters,
                        fls.abr_venue as ls_abr_venue,
                        fls.race_distance as ls_race_distance,
                        fls.name_race_form as ls_name_race_form,
                        fls.mgt_date as ls_mgt_date,
                        fls.track_condition as ls_track_condition,
                        fls.numeric_rating as ls_numeric_rating,
                        fls.jockey_initials as ls_jockey_initials,
                        fls.jockey_surname as ls_jockey_surname,
                        fls.handicap as ls_handicap,
                        fls.barrier as ls_barrier,
                        fls.starting_win_price as ls_starting_win_price,
                        fls.other_runner_name as ls_other_runner_name,
                        fls.other_runner_barrier as ls_other_runner_barrier,
                        fls.in_running_800 as ls_in_running_800,
                        fls.in_running_400 as ls_in_running_400,
                        fls.other_runner_time as ls_other_runner_time,
                        fls.margin_decimal as ls_margin_decimal
                        
                         LEFT JOIN
			  						`tb_data_risa_runner_form` as rrf
			  ON
			  						rrf.runner_code = s.runner_code
			  LEFT JOIN
			  						`tb_data_risa_runner_form_last_starts` as fls
			  ON
			  						fls.runner_form_id = rrf.id
                         
     */
    
    
}