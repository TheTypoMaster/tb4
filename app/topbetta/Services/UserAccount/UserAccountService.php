<?php namespace TopBetta\Services\UserAccount;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:49
 * Project: tb4
 */

use Carbon\Carbon;
use Validator;
use Hash;

use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;
use TopBetta\Services\Validation\Exceptions\ValidationException;


/**
 * Class UserAccountService
 * @package TopBetta\Services\UserAccount
 */
class UserAccountService {

    /**
     * @var UserRepositoryInterface
     */
    protected $basicUser;
    /**
     * @var UserTopBettaRepositoryInterface
     */
    protected $fullUser;
    /**
     * @var BetSourceRepositoryInterface
     */
    protected $betsource;


    /**
     * @param BetSourceRepositoryInterface $betsource
     * @param UserRepositoryInterface $basicUser
     * @param UserTopBettaRepositoryInterface $fullUser
     */
    function __construct(BetSourceRepositoryInterface $betsource,
                         UserRepositoryInterface $basicUser,
                         UserTopbettaRepositoryInterface $fullUser)
    {
        $this->basicUser = $basicUser;
        $this->fullUser = $fullUser;
        $this->betsource = $betsource;
    }


    /**
     * Create a FULL topbetta user
     * - adds records on both the users and topbetta users table
     * @param $input
     * @return array
     */
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


    /**
     * Create a basic user account
     *
     * @param $data
     * @return mixed
     */
    public function createBasicAccount($data){
        return $this->basicUser->create($data);
    }

    /**
     * Create a TopBetta User account
     *
     * @param $data
     * @return mixed
     */
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
            'child_betting_user_name' => 'alphadash',
            'token' => 'required'
        );

        // validate input
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) throw new ValidationException("Validation Failed", $validator->messages());

        // confirm source of request
        if(!$this->checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        $autoGenerate = false;

        if(isset($input['child_betting_user_name'])){
            $uniqueUsername = $input['child_betting_user_name'];
        }else{
            $uniqueUsername = $input['parent_user_name'].'_'.$input['personal_betting_user_name'];
            $autoGenerate = true;
        }

        $username = $this->_generateUniqueUserNameFromBase($uniqueUsername, $autoGenerate);

        //if(!$uniqueBettingUserDetails) {

            // get details of betting acount to base club betting account off or throw exception if it does not exist
            $bettingAccountDetails = $this->basicUser->getFullUserDetailsFromUsername($input['personal_betting_user_name']);
            if(!$bettingAccountDetails) throw new ValidationException("Validation Failed", 'Personal betting account does not exist?');

            $parentAccountDetails = $this->basicUser->getFullUserDetailsFromUsername($input['parent_user_name']);
            if(!$parentAccountDetails) throw new ValidationException("Validation Failed", 'Parent account does not exist?');

            $data = array();

            if(isset($input['child_betting_user_name'])){
                $data['username'] = $input['child_betting_user_name'];
            }else{
                $data['username'] = $input['parent_user_name'].'_'.$input['personal_betting_user_name'];
            }

            // create club betting account
            $data['email'] = $input['parent_user_name'].'+'.$bettingAccountDetails['email'];
            $data['password'] = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand( 0 ,50 ) ,1 ) .substr( md5( time() ), 1);
            $data['first_name'] = $bettingAccountDetails['topbettauser']['first_name'];
            $data['last_name'] = $bettingAccountDetails['topbettauser']['last_name'];
            $data['source'] = $input['source'];
            $data['title'] = $bettingAccountDetails['topbettauser']['title'];
            $data['dob_day'] = $bettingAccountDetails['topbettauser']['dob_day'];
            $data['dob_month'] = $bettingAccountDetails['topbettauser']['dob_month'];
            $data['dob_year'] = $bettingAccountDetails['topbettauser']['dob_year'];
            $data['postcode'] = $bettingAccountDetails['topbettauser']['postcode'];
            $data['street'] = $bettingAccountDetails['topbettauser']['street'];
            $data['city'] = $bettingAccountDetails['topbettauser']['city'];
            $data['state'] = $bettingAccountDetails['topbettauser']['state'];
            $data['country'] = $bettingAccountDetails['topbettauser']['country'];
            $data['marketing_opt_in_flag'] = $bettingAccountDetails['topbettauser']['marketing_opt_in_flag'];
            $data['parent_user_id'] = $parentAccountDetails['id'];

            return $this->createTopbettaUserAccount($data);

        //} else {
        //    throw new ValidationException("Validation Failed", 'Club betting account already exists');
        //}
    }

    /**
     * Checks the source of the request is valid and known
     *
     * @param $input
     * @return bool
     */
    public function checkSource($input){

        $sourceDetails = $this->betsource->getSourceByKeyword($input['source']);

        if(!$sourceDetails) return false;

        $hashString = '';
        foreach($input as $key => $field){
            if($key != 'token') $hashString .= $field;
        }

        $hashString .= $sourceDetails['shared_secret'];

        //$hashString = $input['source'] . $clubname . $bettingUserName . $clubBettingUserName . $bettingAmount . $sourceDetails['shared_secret'];

        //dd($hashString);
        if (Hash::check($hashString, $input['token'])) return true;

        return false;
    }

    private function _generateUniqueUserNameFromBase($username, $autoGenerate, $count = 0)
    {
        $checkForName = $this->basicUser->getUserDetailsFromUsername($username);
        if(!$checkForName || !$autoGenerate) {
            return $checkForName;
        }else{
             $count++;
             $newUserName = $this->_generateUniqueUserNameFromBase($username, $autoGenerate, $count);
        }
        return $newUserName;
    }

}