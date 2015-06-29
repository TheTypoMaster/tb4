<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerModel extends Model
{
    protected $table = 'tb_owners';

    protected $fillable = array(
        'external_owner_id', 'name'
    );

    public function runners()
    {
        return $this->hasMany('TopBetta\Models\RunnerModel', 'owner_id');
    }
}
