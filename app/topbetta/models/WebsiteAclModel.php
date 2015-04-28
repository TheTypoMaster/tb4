<?php

namespace TopBetta\Models;

class WebsiteAclModel extends Eloquent {

	protected $table = 'tb_website_acls';
	public $timestamps = true;

	public function affiliates()
	{
		return $this->belongsToMany('TopBetta\Models\AffiliatesModel', 'tb_affiliates_acls', 'acl_id', 'affiliate_id');
	}

}