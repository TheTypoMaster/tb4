<?php

namespace TopBetta\Models;

class ConfigurationModel extends \Eloquent {

    protected $table = 'tb_configuration';

    protected $guarded = array();

	public static $rules = array();
}
