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
		return $this->belongsTo('TopBetta\models\UserModel', 'pro_entered_by');
	}

}
