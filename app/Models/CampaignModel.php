<?php

namespace TopBetta\Models;

class CampaignModel extends Eloquent {

	protected $table = 'tb_campaigns';
	public $timestamps = true;

	public function users()
	{
		return $this->belongsToMany('TopBetta\Models\UserModel', 'tb_campaign_users', 'user_id', 'campaign_id');
	}

	public function affiliates()
	{
		return $this->belongsToMany('TopBetta\Models\AffiliatesModel', 'tb_affiliates_campaigns', 'campaign_id', 'affilate_id');
	}

	public function promotions()
	{
		return $this->belongsToMany('PromotionsModel', 'tb_campaigns_promotions', 'campaign_id', 'promotion_id');
	}

	public function types()
	{
		return $this->belongsTo('TopBetta\Models\CampaignTypesModel');
	}

}