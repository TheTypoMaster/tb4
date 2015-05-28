<?php

namespace TopBetta\Models;

class AffiliatesModel extends Eloquent {

	protected $table = 'tb_affiliates';
	public $timestamps = true;

	public function users()
	{
		return $this->hasMany('TopBetta\Models\UserModel', 'user_affiliate_id', 'affiliate_id');
	}

	public function type()
	{
		return $this->belongsTo('TopBetta\Models\AffiliateTypesModel', 'affiliate_type_id', 'affiliate_type_id');
	}

	public function campaigns()
	{
		return $this->belongsToMany('TopBetta\Models\CampaignModel', 'tb_affiliates_campaigns', 'affiliate_id', 'campaign_id');
	}

	public function acls()
	{
		return $this->belongsToMany('TopBetta\Models\WebsiteAcl', 'tb_affiliates_acls', 'affiliate_id', 'acl_id');
	}

	public function promotoions()
	{
		return $this->belongsToMany('TopBetta\Models\PromotionsModel', 'tb_affiliates_promotions', 'affiliate_id', 'promotion_id');
	}

}