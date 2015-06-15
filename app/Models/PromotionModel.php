<?php

namespace TopBetta\Models;

use Eloquent;

class PromotionModel extends Eloquent {

	protected $table = 'tbdb_promotions';

	protected $primaryKey = 'pro_id';

	protected $guarded = array();

	public $timestamps = false;

	public static $rules = array();

	public function user()
	{
		return $this->belongsTo('TopBetta\Models\UserModel', 'pro_entered_by');
	}

	public function getProValueAttribute($value)
	{
		return $value / 100;
	}

	public function setProValueAttribute($value)
	{
		$this->attributes['pro_value'] = $value * 100;
	}

}
