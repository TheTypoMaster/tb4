<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 14/07/14
 * Time: 10:37 AM
 */

namespace TopBetta\Repositories;

use Illuminate\Support\Facades\Log;
use TopBetta\RisaForm;

class RisaFormRepository extends BaseEloquentRepository {

	/**
	 * @var \TopBetta\RisaForm
	 */



	protected $model;

	function __construct(RisaForm $model) {
		$this->model = $model;
	}

	public function getFormForRunnerAndRaceId($runner, $raceId) {

		$data = array();

		$runnersForm = $this->model->with('lastStarts')->where('runner_code', $runner['runner_code'])->first();

		$code = $runner['runner_code'];

		// make sure we got some form for this runner
		if(isset($runnersForm)){
			$data['detailed_form'] = array ('id'=> (int)$runnersForm->id,
                'age' => $runnersForm->age,
                'colour' => $runnersForm->colour,
                'sex' => $runnersForm->sex,
                'career' => $runnersForm->career_results,
                'distance' => $runnersForm->distance_results,
                'track' => $runnersForm->track_results,
                'track_distance' => $runnersForm->track_distance_results,
                'first_up' => $runnersForm->first_up_results,
                'second_up' => $runnersForm->second_up_results,
				'good' => $runnersForm->good_results,
                'firm' => $runnersForm->firm_results,
                'soft' => $runnersForm->soft_results,
                'synthetic' => $runnersForm->synthetic_results,
                'wet' => $runnersForm->wet_results,
                'nonwet' => $runnersForm->nonwet_results,
                'night' => $runnersForm->night_results,
                'jumps' => $runnersForm->jumps_results,
                'season' => $runnersForm->season_results,
                'heavy' => $runnersForm->heavy_results);

            $lastStarts = array();
			foreach ($runnersForm->last_starts as $last_starts){
				$lastStarts[] =  array('id' => (int)$last_starts->id, 'finish_position' => (int)$last_starts->finish_position, 'race_starters' => (int)$last_starts->race_starters, 'abr_venue' => $last_starts->abr_venue, 'race_distance' => $last_starts->race_distance,
					'name_race_form' => $last_starts->name_race_form, 'mgt_date' => date('dM y',strtotime($last_starts->mgt_date)), 'track_condition' => $last_starts->track_condition, 'numeric_rating' => $last_starts->numeric_rating, 'jockey_initials' => $last_starts->jockey_initials,
					'jockey_surname' => $last_starts->jockey_surname, 'handicap' => $last_starts->handicap, 'barrier' => (int)$last_starts->barrier, 'starting_win_price' => $last_starts->starting_win_price, 'other_runner_name' => $last_starts->other_runner_name,
					'other_runner_barrier' => (int)$last_starts->other_runner_barrier, 'in_running_800' => $last_starts->in_running_800, 'in_running_400' => $last_starts->in_running_400, 'other_runner_time' => trim($last_starts->other_runner_time, '0:'), 'margin_decimal' => $last_starts->margin_decimal);

                $data['detailed_form']['last_starts'] = array_reverse($lastStarts);
            }
			return $data;
		}
	}

	public function getFormIdByRunnerCode($runnerCode){
		$formId = $this->model->where('runner_code', $runnerCode)
							->pluck('id');
		if(!$formId) return null;
		return $formId;

	}

} 