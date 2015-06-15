<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:04
 * Project: tb4
 */

use TopBetta\Models\AccountTransactionModel;
use TopBetta\Models\UserModel;
use TopBetta\Models\UserAroModel;
use TopBetta\Models\UserAroMapModel;
use TopBetta\Services\Validation\UserBasicValidator;

use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;

class DbUserRepository extends BaseEloquentRepository implements UserRepositoryInterface {

    protected $model;
    protected $validator;
    protected $aro;
    protected $aromap;

    public function __construct(UserModel $basicUser,
                                UserBasicValidator $validator,
                                UserAroModel $aro,
                                UserAroMapModel $aromap)
    {
        $this->model = $basicUser;
        $this->aro = $aro;
        $this->aromap = $aromap;
        $this->validator = $validator;
    }

    public function createBasicUser($input){
        return $this->create($input);
    }

    public function getUserDetailsFromUsername($username){
        $userDetails = $this->model->where('username', $username)->first();

        if ($userDetails) return $userDetails->toArray();

        return null;
    }

    public function getFullUserDetailsFromUsername($username){
        $userDetails = $this->model->with('topbettauser')->where('username', $username)->first();

        if ($userDetails) return $userDetails->toArray();

        return null;
    }

    public function checkMD5PasswordForUser($username, $password){
        //$result = $this->model->where('username', $username)->where('password', md5($password))->first();

        $result = $this->model->where('username', $username)->orWhere('email', $username)->first();
        if($result) {

            if(str_contains($result->password, ":")) {
                //Joomla credential check
                $userPassword = explode(":", $result->password);
                if (md5($password . $userPassword[1]) === $userPassword[0]) {
                    return $result->toArray();
                }
            } else if ($result->password === md5($password)) {
                return $result->toArray();
            }

        }

        return null;
    }

    public function createAroRecordsForJoomla($userDetails){

        $data = array('section_value' => 'users', 'value' => $userDetails['user_id'], 'name' => $userDetails['name']);

        $aroRecord = $this->aro->create($data);

        $aroMapRecord = $this->aromap->create(array('group_id' => 18, 'aro_id' => $aroRecord->id));
	}
        
    public function getUsersWithLastActivityBetween($start, $end, $page = null, $count = null)
    {
        return $this    -> model
                        -> where('lastvisitDate', '>=', $start)
                        -> where('lastvisitDate', '<=', $end)
                        -> forPage($page, $count)
                        -> get();

    }

    public function getDormantUsersWithNoDormantChargeAfter($dormantTransactionType, $days, $chargeDate)
    {
        return $this    ->model
                        ->whereNotInRelationship('accountTransactions', function($q) use (  $dormantTransactionType, $days, $chargeDate ) {
                            $q  ->where('created_date', '>=', Carbon::now()->subDays($days)->toDateTimeString())
                                ->where('account_transaction_type_id', '!=', $dormantTransactionType);

                            $q->orWhere(function($q) use ( $dormantTransactionType, $chargeDate ) {
                                $q  ->where('account_transaction_type_id', $dormantTransactionType)
                                    ->where('created_date', '>=', $chargeDate);
                            });

                        })
                        ->get();
    }




    public function getUserWithActivationHash($activationHash)
    {
        return $this->model->where('activation', '=', $activationHash)->first();
    }

    public function updateByActivationHash($activationHash, $data)
    {
        $user = $this->model->where('activation', '=', $activationHash)->firstOrFail();

        $user->update($data);

        return $user->toArray();
	}
	
    public function getWithTopBettaUser($userId)
    {
        return $this    -> model
                        -> where('id', $userId)
                        -> with('topbettauser')
                        -> first();
    }

    public function getUserByUsername($username)
    {
        return $this->model->where('username', $username)->first();
    }


}