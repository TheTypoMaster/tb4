<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateTypesModel extends Model {

	protected $table = 'tb_affiliate_types';
	public $timestamps = true;

	public function affiliates()
	{
		return $this->hasMany('TopBetta\Models\AffiliatesModel', 'affiliate_type_id', 'affiliate_type_id');
	}

}