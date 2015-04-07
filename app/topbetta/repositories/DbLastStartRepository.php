<?php namespace TopBetta\Repositories; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 5/04/15
 * File creation time: 23:56
 * Project: tb4
 */

use TopBetta\Models\LastStartsModel;
use TopBetta\Repositories\Contracts\LastStartRepositoryInterface;

class DbLastStartRepository extends BaseEloquentRepository implements LastStartRepositoryInterface{

	protected $model;

	function __construct(LastStartsModel $model)
	{
		$this->model = $model;
	}

	public function getLastStartIdByRaceAndRunnerCode($raceCode, $runnerCode) {
		$laststartid = $this->model->where('runner_code', $runnerCode)
						->where('race_code', $raceCode)
						->pluck('id');
		if(!$laststartid) return null;
		return $laststartid;

	}


}