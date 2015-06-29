<?php

namespace TopBetta\Models;

class CampaignTypesModel extends Eloquent {

	protected $table = 'tb_campaign_types';
	public $timestamps = true;

	public function campaigns()
	{
		return $this->hasMany('TopBetta\Models\CampaignModel');
	}

}