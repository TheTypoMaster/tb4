<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/06/2015
 * Time: 2:57 PM
 */

namespace TopBetta\Models;


use Cartalyst\Sentry\Groups\Eloquent\Group;

class AdminGroupModel extends Group {

    protected $table = 'tb_admin_groups';
}