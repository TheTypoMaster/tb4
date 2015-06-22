<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerModel extends Model
{
    protected $table = 'tb_trainers';

    protected $fillable = array(
        'external_trainer_id', 'name', 'location', 'state', 'postcode', 'initials'
    );

    public function runners()
    {
        return $this->hasMany('TopBetta\Models\RunnerModel', 'trainer_id');
    }
}
