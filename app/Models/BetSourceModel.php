<?php namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class BetSourceModel extends Model {
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
