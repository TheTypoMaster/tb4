<?php

namespace TopBetta\Models;

class PromotionTypesModel extends Eloquent {

	protected $table = 'tb_promotion_types';
	public $timestamps = true;

	public function promotions()
	{
		return $this->hasMany('TopBetta\Models\PromotionsModel');
	}

}