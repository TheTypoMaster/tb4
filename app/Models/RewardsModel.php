<?php

namespace TopBetta\Models;

class RewardsModel extends Eloquent {

	protected $table = 'tb_rewards';
	public $timestamps = true;

	public function promotions()
	{
		return $this->belongsToMany('TopBetta\Models\PromotionsModel', 'tb_promotions_rewards', 'reward_id', 'promotion_id');
	}

	public function types()
	{
		return $this->belongsTo('TopBetta\Models\RewardTypesModel');
	}

}