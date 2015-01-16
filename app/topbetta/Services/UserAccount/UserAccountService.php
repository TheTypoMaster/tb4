<?php namespace TopBetta\Services\UserAccount;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:49
 * Project: tb4
 */

use Carbon\Carbon;
use Validator;

use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;
use TopBetta\Services\Validation\Exceptions\ValidationException;


class UserAccountService {

    protected $basicUser;
    protected $fullUser;

    function __construct(UserRepositoryInterface $basicUser,
                         UserTopbettaRepositoryInterface $fullUser)
    {
        $this->basicUser = $basicUser;
        $this->fullUser = $fullUser;
    }


    public function createTopbettaUserAccount($input){

        // set some other required fields
        $basicData = array();
        if(isset($input['username'])) $basicData['username'] = $input['username'];
        if(isset($input['email'])) $basicData['email'] = $input['email'];
        if(isset($input['password'])) $basicData['password'] = $input['password'];
        if(isset($input['parent_user_id'])) $basicData['parent_user_id'] = $input['parent_user_id'];
        $basicData['name'] = $input['first_name'].' '.$input['last_name'];
        $basicData['usertype'] = 'Registered';
        $basicData['gid'] = '18';
        $basicData['registerDate'] = Carbon::now();
        $basicData['lastVisitDate'] = Carbon::now();

        // create the basic user record
        $basic = $this->createBasicAccount($basicData);

        // get the user id of the new account
        $input['user_id'] = $basic['id'];

        // unset fields not required for basic
        unset($input['username'], $input['email'], $input['password']);
        if(isset($input['parent_user_id'])) unset($input['parent_user_id']);

        // create the full account record
        $full = $this->createFullAccount($input);

        return array_merge($basic, $full);

    }


    public function createBasicAccount($data){
        return $this->basicUser->create($data);
    }

    public function createFullAccount($data){
        return $this->fullUser->create($data);
    }

    /**
     * Confirms the child account is a child of the parent account
     *
     * @param $parentUserName
     * @param $childUserName
     * @return bool
     */
    public function confirmBettingAccount($parentUserName, $childUserName){
        // get club user details
        $parentUserDetails = $this->basicUser->getUserDetailsFromUsername($parentUserName);
        $childUserDetails = $this->basicUser->getUserDetailsFromUsername($childUserName);

        // check if betting account userid is child of club
        if($parentUserDetails['id'] == $childUserDetails['parent_user_id']) return true;

        return false;

    }

    /**
     * Creates a new unique child betting account
     * - bets placed in these accounts are linked to the parent account
     * - cost of bet comes from the child account
     * - the child account will be a clone of an existing full account
     *
     * @param $input
     * @return bool
     * @throws ValidationException
     */
    public function createUniqueChildUserAccount($input){

        // validation rules
        $rules = array(
            'source' => 'required',
            'parent_user_name' => 'required|alphadash',
            'personal_betting_user_name' => 'required|alphadash',
            'child_betting_user_name' => 'required|alphadash',
            'token' => 'required'
        );

        // validate input
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) throw new ValidationException("Validation Failed", $validator->messages());

        // check if default unique betting username exists for this club (parent-user-name_personal-username)
        $uniqueBettingUserDetails = $this->user->getUserDetailsFromUsername($input['parent_user_name'].'_'.$input['personal_betting_user_name']);

        if(!$uniqueBettingUserDetails) {

            // get details of betting acount to base club betting account off or throw exception if it does not exist
            $bettingAccountDetails = $this->user->getFullUserDetailsFromUsername($input['personal_betting_user_name']);
            if(!$bettingAccountDetails) throw new ValidationException("Validation Failed", 'Personal betting account does not exist?');

            $parentAccountDetails = $this->user->getFullUserDetailsFromUsername($input['parent_user_name']);
            if(!$parentAccountDetails) throw new ValidationException("Validation Failed", 'Parent account does not exist?');

            // create club betting account
            $data = array('username' => $input['parent_user_name'].'_'.$input['personal_betting_user_name'],
                'email' => 'no-email_'.$bettingAccountDetails['email'],
                'password' => substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand( 0 ,50 ) ,1 ) .substr( md5( time() ), 1),
                'first_name' => $bettingAccountDetails['topbettauser']['first_name'],
                'last_name' => $bettingAccountDetails['topbettauser']['last_name'],
                'source' => $input['source'],
                'title' => $bettingAccountDetails['topbettauser']['title'],
                'dob_day' => $bettingAccountDetails['topbettauser']['dob_day'],
                'dob_month' => $bettingAccountDetails['topbettauser']['dob_month'],
                'dob_year' => $bettingAccountDetails['topbettauser']['dob_year'],
                'postcode' => $bettingAccountDetails['topbettauser']['postcode'],
                'street' => $bettingAccountDetails['topbettauser']['street'],
                'city' => $bettingAccountDetails['topbettauser']['city'],
                'state' => $bettingAccountDetails['topbettauser']['state'],
                'country' => $bettingAccountDetails['topbettauser']['country'],
                'marketing_opt_in_flag' => $bettingAccountDetails['topbettauser']['marketing_opt_in_flag'],
                'parent_user_id' => $parentAccountDetails['id']
            );

            return $this->createTopbettaUserAccount($data);

        } else {
            throw new ValidationException("Validation Failed", 'Personal betting already exists');
        }
    }
}