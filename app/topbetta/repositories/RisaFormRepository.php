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

class RisaFormRepository {

	/**
	 * @var \TopBetta\RisaForm
	 */
	private $risaForm;

	function __construct(RisaForm $risaForm) {
		$this->risaForm = $risaForm;
	}

	public function getFormForRunnerAndRaceId($runner, $raceId) {

		$data = array();

		$runnersForm = $this->risaForm->with('lastStarts')->where('runner_code', $runner['runner_code'])->get();

		$code = $runner['runner_code'];

		// make sure we got some form for this runner
		if(isset($runnersForm[0])){
			$data['detailed_form'] = array ('id'=> (int)$runnersForm[0]->id, 'age' => $runnersForm[0]->age, 'colour' => $runnersForm[0]->colour, 'sex' => $runnersForm[0]->sex, 'career' => $runnersForm[0]->career_results,
				'distance' => $runnersForm[0]->distance_results, 'track' => $runnersForm[0]->track_results, 'track_distance' => $runnersForm[0]->track_distance_results, 'first_up' => $runnersForm[0]->first_up_results, 'second_up' => $runnersForm[0]->second_up_results,
				'good' => $runnersForm[0]->good_results, 'dead' => $runnersForm[0]->dead_results, 'slow' => $runnersForm[0]->slow_results, 'heavy' => $runnersForm[0]->heavy_results);

            $lastStarts = array();
			foreach ($runnersForm[0]->last_starts as $last_starts){
				$lastStarts[] =  array('id' => (int)$last_starts->id, 'finish_position' => (int)$last_starts->finish_position, 'race_starters' => (int)$last_starts->race_starters, 'abr_venue' => $last_starts->abr_venue, 'race_distance' => $last_starts->race_distance,
					'name_race_form' => $last_starts->name_race_form, 'mgt_date' => date('dM y',strtotime($last_starts->mgt_date)), 'track_condition' => $last_starts->track_condition, 'numeric_rating' => $last_starts->numeric_rating, 'jockey_initials' => $last_starts->jockey_initials,
					'jockey_surname' => $last_starts->jockey_surname, 'handicap' => $last_starts->handicap, 'barrier' => (int)$last_starts->barrier, 'starting_win_price' => $last_starts->starting_win_price, 'other_runner_name' => $last_starts->other_runner_name,
					'other_runner_barrier' => (int)$last_starts->other_runner_barrier, 'in_running_800' => $last_starts->in_running_800, 'in_running_400' => $last_starts->in_running_400, 'other_runner_time' => trim($last_starts->other_runner_time, '0:'), 'margin_decimal' => $last_starts->margin_decimal);

                $data['detailed_form']['last_starts'][] = array_reverse($lastStarts);
            }


			return $data;
		}
	}

} 