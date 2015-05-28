<?php

namespace TopBetta\Models;

class PromotionsModel extends Eloquent {

	protected $table = 'tb_promotions';
	public $timestamps = true;

	public function users()
	{
		return $this->belongsToMany('TopBetta\Models\UserModel', 'tb_promotios_users', 'promotion_id', 'user_id');
	}

	public function rewards()
	{
		return $this->belongsToMany('TopBetta\Models\RewardsModel', 'tb_promotions_rewards', 'promotion_id', 'reward_id');
	}

	public function type()
	{
		return $this->belongsTo('TopBetta\Models\PromotionTypesModel');
	}

	public function affiliates()
	{
		return $this->belongsToMany('TopBetta\Models\AffiliatesModel', 'tb_affiliates_promotions', 'promotion_id', 'affilaite_id');
	}

	public function campaigns()
	{
		return $this->belongsToMany('TopBetta\Models\CampaignModel', 'tb_campaigns_promotions', 'promotion_id', 'campaign_id');
	}

}