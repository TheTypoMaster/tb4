<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class ResultPriceModel extends Model
{
    protected $table = 'tb_result_prices';

    protected $guarded = array();

    public function betType()
    {
        return $this->belongsTo('TopBetta\Models\BetTypeModel', 'bet_type_id');
    }
}
