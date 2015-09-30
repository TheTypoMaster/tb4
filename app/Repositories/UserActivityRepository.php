<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 1:31 PM
 */

namespace TopBetta\Repositories;

use Regulus\ActivityLog\Models\Activity;
use TopBetta\Repositories\Contracts\UserActivityRepositoryInterface;

class UserActivityRepository extends BaseEloquentRepository implements UserActivityRepositoryInterface {


    public function __construct(Activity $activityModel) {
        $this->activityModel = $activityModel;
    }
    /**
     * get list of user activities
     * @param $user_id
     * @return mixed
     */
    public function listUserActivity($user_id) {
        return $this->activityModel->where('user_id', $user_id)->paginate();
    }
}