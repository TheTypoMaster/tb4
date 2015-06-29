<?php

namespace TopBetta\Models;

class RewardTypesModel extends Eloquent {

	protected $table = 'tb_reward_types';
	public $timestamps = true;

	public function rewards()
	{
		return $this->hasMany('TopBetta\Models\RewardsModel');
	}

}