<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class RunnerModel extends Model
{
    protected $table = 'tb_runners';

    protected $fillable = array(
        'external_runner_id', 'owner_id', 'trainer_id', 'name', 'colour', 'sex', 'age', 'foal_date', 'sire', 'dam'
    );

    public function selections()
    {
        return $this->hasMany('TopBetta\Models\SelectionModel', 'runner_id');
    }

    public function trainer()
    {
        return $this->belongsTo('TopBetta\Models\TrainerModel', 'trainer_id');
    }

    public function owner()
    {
        return $this->belongsTo('TopBetta\Models\OwnerModel');
    }
}
