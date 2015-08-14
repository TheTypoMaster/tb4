<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class SportMarketTypeDetailsModel extends Model
{
    protected $table = 'tb_sport_market_type_details';

    public function sport()
    {
        return $this->belongsTo('TopBetta\Models\SportModel', 'sport_id');
    }

    public function marketType()
    {
        return $this->belongsTo('TopBetta\Models\MarketTypeModel', 'market_type_id');
    }
}
