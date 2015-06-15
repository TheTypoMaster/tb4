<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 3:46 PM
 */

namespace TopBetta\Models;

use Eloquent;

class ProcessParamsModel extends Eloquent {

    protected $table = "tb_process_params";

    protected $fillable = array(
        "process_params",
        "created_at",
        "updated_at"
    );


    public function getProcessParamsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setProcessParamsAttribute($value)
    {
        $this->attributes['process_params'] = json_encode($value, JSON_FORCE_OBJECT);
    }
}