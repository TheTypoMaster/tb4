<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 2:42 PM
 */
namespace TopBetta\Repositories\Contracts;

interface UserActivityRepositoryInterface {
    public function listUserActivity($user_id);
}