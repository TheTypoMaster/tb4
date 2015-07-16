<?php namespace TopBetta\Models;

use Eloquent;

class BetSourceModel extends Eloquent {
	protected $table = 'tb_bet_source';
	protected $guarded = array();

    public function getApiEndpointAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setApiEndpointAttribute($value)
    {
        $this->attributes['api_endpoint'] = json_encode($value);
        return $this;
    }
}
