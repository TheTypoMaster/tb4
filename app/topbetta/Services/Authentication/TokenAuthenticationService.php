<?php namespace TopBetta\Services\Authentication;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 11:57
 * Project: tb4
 */

use App;
use Hash;
use Auth;
use Carbon\Carbon;

use TopBetta\Repositories\Contracts\BetOriginRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTokenRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\UserAccount\UserAccountService;
use TopBetta\Services\Accounting\AccountTransactionService;


/**
 * Class TokenAuthenticationService
 * @package TopBetta\Services\Authentication
 */
class TokenAuthenticationService {

    protected $betorigin;
    protected $usertoken;
    protected $user;
    protected $userservice;
    protected $accountservice;
    protected $accounttransactions;

    /**
     *  Inject class dependencies
     *
     * @param BetOriginRepositoryInterface $betorigin
     * @param UserTokenRepositoryInterface $usertoken
     * @param UserRepositoryInterface $user
     * @param UserAccountService $userservice
     * @param AccountTransactionService $accountservice
     * @param AccountTransactionRepositoryInterface $accounttransactions
     */
    public function __construct(BetOriginRepositoryInterface $betorigin,
                                UserTokenRepositoryInterface $usertoken,
                                UserRepositoryInterface $user,
                                UserAccountService $userservice,
                                AccountTransactionService $accountservice,
                                AccountTransactionRepositoryInterface $accounttransactions){

        $this->betorigin = $betorigin;
        $this->usertoken = $usertoken;
        $this->user = $user;
        $this->userservice = $userservice;
        $this->accountservice = $accountservice;
        $this->accounttransactions = $accounttransactions;
    }

    /**
     * Public method that receives the request for a token, performs source identification, validation and generates
     * token
     *
     * @param $input
     * @return array
     * @throws ValidationException
     */
    public function processTokenRequest($input){

        $rules = array(
            'source' => 'required',
            //'club_user_name' => 'required',
            'club_betting_user_name' => 'required',
            'token' => 'required'
        );

        // validate input
        $validated = $this->_validateParams($input, $rules);

        // confirm source of request
        if(!$this->_checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['club_betting_user_name']);
        if(!$bettingUserDetails) throw new ValidationException("Validation Failed", 'Invalid Payload - betting account');

        // if this is a club betting account login
        if(isset($input['club_user_name'])){
            // confirm betting account is a child of the club account
            if(!$this->_confirmBettingAccount($input, $bettingUserDetails)) throw new ValidationException("Validation Failed", 'Invalid Payload - non child');
        }

        // generate token
        $newToken = $this->_genreateToken();

        if(!$newToken) throw new ValidationException("Validation Failed", 'Invalid Payload - token genration');

        // token expiry - TODO move to a database field
        $expiry = Carbon::now('Australia/Sydney')->addMinute();

        // store token
        if(!$this->_storeToken($bettingUserDetails['id'], $newToken, $expiry)) throw new ValidationException("Validation Failed", 'Invalid Payload - token not stored');

        return array('token' => $newToken);
    }

    /**
     * Public method that attemps to log a user in with a token
     *
     * @param $input
     * @return mixed
     * @throws ValidationException
     */
    public function tokenLogin($input){

        // get token, username and source and validate
        $rules = array(
            'source' => 'required',
            'betting_user_name' => 'required',
            'token' => 'required'
        );
        $validated = $this->_validateParams($input, $rules);

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['betting_user_name']);
        if(!$bettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user account does not exist');

        // if club_name is supplied then this is a child betting acount login and we need to make sure this username is a child of the source?
        if(isset($input['club_user_name'])){
            if(!$this->_confirmBettingAccount($input, $bettingUserDetails)) throw new ValidationException("Validation Failed", 'Bettung user account is not linked to club account');
        }

        // get token record for the username
        $tokenRecord = $this->usertoken->getTokenRecordByUserId($bettingUserDetails['id']);

        // if there is no current token in the database
        if(!$tokenRecord) throw new ValidationException("Validation Failed", 'Invalid Payload - no token for betting');

        // compare the token
        if($tokenRecord['token'] != $input['token']) throw new ValidationException("Validation Failed", 'Token is incorrect');

        // make sure the token has not expired
        if($tokenRecord['expiry'] < Carbon::now('Australia/Sydney')) throw new ValidationException("Validation Failed", 'Token has expired');

        // get an instance of the user
        $user = $this->user->find($bettingUserDetails['id']);

        // log betting user In
        Auth::login($user);

        // return user model
        return Auth::user();
    }


    /**
     * Public method that receives the request and performs source identification, validation and token reponse
     *
     * @param $input
     * @return array
     * @throws ValidationException
     */
    public function manageBettingAccount($input){

        $rules = array(
            'source' => 'required',
            'club_user_name' => 'required',
            'betting_user_name' => 'required|alphadash',
//            'club_betting_user_name' => 'required',
            'betting_amount' => 'required|numeric',
            'token' => 'required'
        );

        // validate input
        $validated = $this->_validateParams($input, $rules);

        // confirm source of request
        if(!$this->_checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        // get the club user account details
        $clubUserDetails = $this->user->getUserDetailsFromUsername($input['club_user_name']);
        if(!$clubUserDetails) throw new ValidationException("Validation Failed", 'Club user not found');

        // If club betting account is not passed in and it does not exist then we try and create one.
        if(!isset($input['club_betting_user_name'])) {
            // create a club betting account or get existing one
            $clubBettingUserDetails = $this->_createUniqueUserAccount($input['club_user_name'], $input['betting_user_name'], $clubUserDetails['id'], $input['source']);
            if(!$clubBettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user not created');

        } else {
            $clubBettingUserDetails = $this->user->getUserDetailsFromUsername($input['club_betting_user_name']);
            if(!$clubBettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user not found');
        }

       // dd($clubBettingUserDetails);
        // if this is a club betting account login
        if(isset($input['club_user_name'])){
            // confirm betting account is a child of the club account
            if(!$this->_confirmBettingAccount($input, $clubBettingUserDetails)) throw new ValidationException("Validation Failed", 'Invalid Payload - club betting account is not a child of the club account');
        }

        /*
         *  transfer funds from club account to betting account
         */

        // get club balance
        $clubAccountBalance = $this->accounttransactions->getAccountBalanceByUserId($clubUserDetails['id']);

        // make sure there is enough to fund the transfer from the club to tge betting account
        if($clubAccountBalance < $input['betting_amount']) throw new ValidationException("Validation Failed", 'Insuffcient club betting funds');

        // remove the funds from the club account
        $removeFunds = $this->accountservice->decreaseAccountBalance($clubUserDetails['id'], $input['betting_amount'], 'clubfundaccount');
        if (!$removeFunds) throw new ValidationException("Validation Failed", 'Failed to decrease club account');

        // increase club betting account
        $addFunds = $this->accountservice->increaseAccountBalance($clubBettingUserDetails['id'], $input['betting_amount'], 'bettingfundaccount');
        if (!$addFunds) throw new ValidationException("Validation Failed", 'Failed to increase club betting account');

        return 'Funds transfered to betting account';

    }

    /**
     * Validates the data
     *
     * @param $input
     * @param array $rules
     * @return mixed
     */
    private function _validateParams($input, array $rules){

        return App::make('\TopBetta\Services\Validation\Validator')->validate($input, $rules);
        //return $this->validator->validate($input, $rules);

    }


    /**
     * Confirms the data is from a trusted source
     *
     * @param $input
     * @return bool
     */
    private function _checkSource($input){

        $sourceDetails = $this->betorigin->getOriginByKeyword($input['source']);

        if(!$sourceDetails) return false;

        $clubname = $bettingAmount = $clubBettingUserName = $bettingUserName  = '';

        if(isset($input['club_user_name'])) $clubname = $input['club_user_name'];
        if(isset($input['betting_amount'])) $bettingAmount = $input['betting_amount'];
        if(isset($input['betting_user_name'])) $bettingUserName = $input['betting_user_name'];
        if(isset($input['club_betting_user_name'])) $clubBettingUserName = $input['club_betting_user_name'];

        $hashString = $input['source'] . $clubname . $bettingUserName . $clubBettingUserName . $bettingAmount . $sourceDetails['shared_secret'];

        //dd($hashString);
        if (Hash::check($hashString, $input['token'])) return true;

        return false;

    }

    /**
     * Confirms the betting account is a child of the parent account
     *
     * @param $input
     * @param $bettingUserDetails
     * @return bool
     */
    private function _confirmBettingAccount($input, $bettingUserDetails){
        // get club user details
        $clubUserDetails = $this->user->getUserDetailsFromUsername($input['club_user_name']);

        // check if betting account userid is child of club
        if($clubUserDetails['id'] == $bettingUserDetails['parent_user_id']) return true;

        return false;

    }

    /**
     * Generates a random token to be used for auto login
     *
     * @return mixed
     */
    private function _genreateToken(){

        $randomString = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand( 0 ,50 ) ,1 ) .substr( md5( time() ), 1);
        return Hash::make($randomString);

    }

    /**
     * Stores a newly genreated token for a user id with a defined expiry time
     *
     * @param $bettingUserId
     * @param $newtoken
     * @param $expiry
     * @return mixed
     */
    private function _storeToken($bettingUserId, $newtoken, $expiry){

        $data = array('user_id' => $bettingUserId, 'token' => $newtoken, 'expiry' => $expiry);
        return $this->usertoken->updateOrCreate($data, 'user_id');

    }

    /**
     * Creates a new club betting account for a punter
     *
     * @param $clubName
     * @param $bettingName
     * @param $clubUserId
     * @return bool
     * @throws ValidationException
     */
    private function _createUniqueUserAccount($clubName, $bettingName, $clubUserId, $source){

        // check if default club betting username exists for this club (club-user-name_betting-username)
        $clubBettingUserDetails = $this->user->getUserDetailsFromUsername($clubName .'_'.$bettingName);

        if(!$clubBettingUserDetails) {

            // get details of betting acount to base club betting account off or throw exception if it does not exist
            $bettingAccountDetails = $this->user->getFullUserDetailsFromUsername($bettingName);

            if(!$bettingAccountDetails) throw new ValidationException("Validation Failed", 'Personal betting account does not exist?');

            // create club betting account
            $data = array('username' => $clubName .'_'.$bettingName,
                            'email' => 'no-email_'.$bettingAccountDetails['email'],
                            'password' => substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,mt_rand( 0 ,50 ) ,1 ) .substr( md5( time() ), 1),
                            'first_name' => $bettingAccountDetails['topbettauser']['first_name'],
                            'last_name' => $bettingAccountDetails['topbettauser']['last_name'],
                            'source' => $source,
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
                            'parent_user_id' => $clubUserId
                    );

            return $this->userservice->createTopbettaUserAccount($data);

        } else {
            throw new ValidationException("Validation Failed", 'Personal betting already exists');
        }

        //return $clubBettingUserDetails;
    }

}