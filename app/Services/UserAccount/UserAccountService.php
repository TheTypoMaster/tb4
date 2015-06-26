<?php namespace TopBetta\Services\UserAccount;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:49
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Services\Email\ThirdPartyEmailServiceInterface;
use TopBetta\Services\Exceptions\InvalidFormatException;
use Validator;
use Hash;
use Mail;

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
     * @var ThirdPartyEmailServiceInterface
     */
    private $emailService;


    /**
     * @param BetSourceRepositoryInterface $betsource
     * @param UserRepositoryInterface $basicUser
     * @param UserTopBettaRepositoryInterface $fullUser
     * @param ThirdPartyEmailServiceInterface $emailService
     */
    function __construct(BetSourceRepositoryInterface $betsource,
                         UserRepositoryInterface $basicUser,
                         UserTopbettaRepositoryInterface $fullUser,
                         ThirdPartyEmailServiceInterface $emailService)
    {
        $this->basicUser = $basicUser;
        $this->fullUser = $fullUser;
        $this->betsource = $betsource;
        $this->emailService = $emailService;
    }


    /**
     * Create a FULL topbetta user
     * - adds records on both the users and topbetta users table
     * - adds aro records
     * @param $input
     * @return array
     */
    public function createTopbettaUserAccount($input)
    {
        // create the basic user record
        $basic = $this->createBasicAccount($input);

        // get the user id of the new account
        $input['user_id'] = $basic['id'];

        // unset fields not required for full account
        unset($input['username'], $input['email'], $input['password'], $input['auto_activate']);
        if(isset($input['parent_user_id'])) unset($input['parent_user_id']);

        // create aro records
        $this->basicUser->createAroRecordsForJoomla(array('user_id' => $basic['id'], 'name' => $basic['name']));

        // create the full account record
        $full = $this->createFullAccount($input);

        //create email contact
        $this->emailService->addUserToContacts($this->basicUser->find($basic['id']));

        return array_merge($basic, $full);
    }


    /**
     * Create a basic user account
     *
     * @param $input
     * @return mixed
     */
    public function createBasicAccount($input)
    {
        // set some other required fields
        $basicData = array();
        if(isset($input['username'])) $basicData['username'] = $input['username'];
        if(isset($input['email'])) $basicData['email'] = $input['email'];
        if(isset($input['password'])) $basicData['password'] = md5($input['password']);
        if(isset($input['parent_user_id'])) $basicData['parent_user_id'] = $input['parent_user_id'];
        $basicData['name'] = $input['first_name'].' '.$input['last_name'];
        $basicData['usertype'] = 'Registered';
        $basicData['gid'] = '18';
        $basicData['isTopBetta'] = '1';
        $basicData['registerDate'] = Carbon::now();
        $basicData['lastVisitDate'] = Carbon::now();

        if( array_get($input, 'auto_activate', false) ) {
            $basicData['activated_flag'] = true;
        } else {
            //generate activation code. Check to make sure it is unique first. Shouldn't need to check too much as long as
            //activation codes are cleared.
            while ($this->basicUser->getUserWithActivationHash($activationHash = str_random(40))) ;

            $basicData['activation'] = $activationHash;
        }

        return $this->basicUser->create($basicData);
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
     * Get child user accounts
     *
     * @param $parentUserId
     * @return mixed
     */
    public function getChildBettingAccounts($parentUserId){
        return $this->basicUser->getChildUserAccounts($parentUserId);
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
            'parent_username' => 'required|alphadash',
            'personal_username' => 'required|alphadash',
            'child_username' => 'alphadash',
            'token' => 'required'
        );

        // validate input
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) throw new ValidationException("Validation Failed", $validator->messages());

        // confirm source of request
        if(!$this->checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        $autoGenerate = false;

        if(isset($input['child_username'])){
            $uniqueUsername = $input['child_username'];
        }else{
            $uniqueUsername = $input['parent_username'].'_'.$input['personal_username'];
            $autoGenerate = true;
        }

        $username = $this->_generateUniqueUserNameFromBase($uniqueUsername, $autoGenerate);

        // get details of betting acount to base club betting account off or throw exception if it does not exist
        $bettingAccountDetails = $this->basicUser->getFullUserDetailsFromUsername($input['personal_username']);
        if(!$bettingAccountDetails) throw new ValidationException("Validation Failed", 'Personal betting account does not exist?');

        $parentAccountDetails = $this->basicUser->getFullUserDetailsFromUsername($input['parent_username']);
        if(!$parentAccountDetails) throw new ValidationException("Validation Failed", 'Parent account does not exist?');

        // create club betting account
        $data = array();
        $data['username'] = $username;
        $data['email'] = $username.'+'.$bettingAccountDetails['email'];
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


    public function sendWelcomeEmail($userId, $emailSource = null)
    {
        //user the default source if one is not specified
        if( ! $emailSource ) {
            $emailSource = \Config::get("accountactivation.default_source");
        }

        //get the user
        $user = $this->basicUser->find($userId);

        //get the activation email config
        $config = \Config::get("accountactivation.email.".$emailSource);

        //send the welcome email
        Mail::send(
            array_get($config, "email"),

            array("user" => $user, "activationUrl" => \Config::get("accountactivation.activation_url")),

            function ($message) use ($user, $config) {
               $message
                   ->to($user->email)
                   ->subject(array_get($config, "subject"));

               //if from config is setup use it, otherwise default is fine
                if( array_get($config, "from", false) ) {
                   $message->from(
                       array_get($config, "from.address"),
                       array_get($config, "from.name")
                   );
               }
            }
        );
    }

    public function activateUser($activationHash)
    {
        //activate user. Clear activation so can't unblock twice.
        return $this->basicUser->updateByActivationHash($activationHash, array(
            "activated_flag"    => 1,
            "activation"        => ""
        ));
	}
	
    public function getTopBettaUser($userId)
    {
        return $this->basicUser->getWithTopBettaUser($userId);

    }

    /**
     * Adds the amount to the users balance to turn over
     * @param $userId
     * @param $amount
     * @return bool
     */
    public function addBalanceToTurnOver($userId, $amount)
    {
        if( $amount ) {
            return $this->fullUser->updateBalanceToTurnOver($userId, $amount);
        }

        return true;
    }

    public function addFreeCreditWinsToTurnOver($userId, $amount)
    {
        if( $amount ) {
            return $this->fullUser->updateFreeCreditWinsToTurnOver($userId, $amount);
        }

        return true;
    }

    public function decreaseBalanceToTurnOver($userId, $amount, $decreaseFreeCreditTurnover = false)
    {
        $user = $this->fullUser->findByUserId($userId);

        $remainingAmount = $amount;
        if( $decreaseFreeCreditTurnover && $user->free_credit_wins_to_turnover > 0 ) {
            $remainingAmount = $amount - $user->free_credit_wins_to_turnover;

            $this->addFreeCreditWinsToTurnOver($userId, -$amount);
        }

        if ( $remainingAmount > 0 && $user->balance_to_turnover > 0) {
            $this->addBalanceToTurnOver($userId, -$remainingAmount);
        }
    }

    public function getBalanceToTurnOver($userId)
    {
        $user = $this->fullUser->getUserDetailsFromUserId($userId);

        if($user) {
            return $user['balance_to_turnover'];
        }

        return 0;
    }

    public function findUserByNameAndDob($firstName, $lastName, $dob)
    {
        //format the date
        try {
            $dob = Carbon::createFromFormat('d-M-y', trim($dob));
        } catch (\Exception $e) {
            try {
                $dob = Carbon::createFromFormat('d/m/Y', trim($dob));
            } catch (\Exception $e) {
                throw new InvalidFormatException(array($firstName, $lastName, $dob), "Invalid DOB format");
            }
        }

        //get the user
        if ( $dob ) {
            return $this->fullUser->getUserByNameAndDob($firstName, $lastName, $dob->day, $dob->month, $dob->year);
        }

        return $this->fullUser->getUserByNameAndDob($firstName, $lastName);
    }

    private function _generateUniqueUserNameFromBase($username, $autoGenerate, $count = 0)
    {
        $checkForName = $this->basicUser->getUserDetailsFromUsername($username);
        if(!$checkForName || !$autoGenerate) {
            return $username;
        }else{
             $count++;
             $newUserName = $this->_generateUniqueUserNameFromBase($username.$count, $autoGenerate, $count);
        }
        return $newUserName;
    }

}