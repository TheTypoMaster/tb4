<?php namespace TopBetta\Repositories; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 3/04/15
 * File creation time: 19:37
 * Project: tb4
 */

use TopBetta\Models\DataValueModel;
use TopBetta\Repositories\Contracts\DataValueRepositoryInterface;
 
class DbDataValueRepository  extends BaseEloquentRepository implements DataValueRepositoryInterface {

	protected $model;

	function __construct(DataValueModel $model)
	{
		$this->model = $model;
	}

	/**
	 * Get the system default value.
	 *
	 * @param $type
	 * @param $value
	 * @return mixed
	 */

	public function getDefaultValueForType($type, $value)
	{
		$defaultvalue = $this->model->join('tb_data_types', 'tb_data_types.id', '=', 'tb_data_values.data_type_id')
			->leftjoin('tb_data_provider_match', 'tb_data_provider_match.data_value_id', '=', 'tb_data_values.id')
			->leftjoin('tb_data_provider', 'tb_data_provider.id', '=', 'tb_data_provider_match.provider_id')
			->where('tb_data_provider_match.value', $value)
			->where('tb_data_provider_match.value', $type)
			->value('tb_data_types.data_type');

		if(!$defaultvalue) return null;

		return $defaultvalue;
	}

	/*
	 *   	$valueName = "select dv.value
	    	from tb_data_values as dv
	    	left join tb_data_types as dt on dt.id = dv.data_type_id
	    	left join tb_data_provider_match as dpm on dpm.data_value_id = dv.id
	    	left join tb_data_provider as dp on dp.id = dpm.provider_id
	    	where dpm.value = '$value'
	    	and dt.data_type = '$type'
	       	limit 1";

	 */

}