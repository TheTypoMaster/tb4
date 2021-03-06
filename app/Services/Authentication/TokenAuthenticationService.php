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
use Regulus\ActivityLog\Models\Activity;

use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTokenRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;

use TopBetta\Services\Authentication\Exceptions\InvalidTokenException;
use TopBetta\Services\Authentication\Exceptions\TokenGenerationException;
use TopBetta\Services\UserAccount\Exceptions\UserNotFoundException;
use TopBetta\Services\UserAccount\UserAccountService;

use TopBetta\Services\Validation\Exceptions\ValidationException;


/**
 * Class TokenAuthenticationService
 * @package TopBetta\Services\Authentication
 */
class TokenAuthenticationService {

    protected $betsource;
    protected $usertoken;
    protected $user;
    protected $usertopbetta;
    protected $userservice;

    /**
     *  Inject class dependencies
     *
     * @param BetSourceRepositoryInterface $betsource
     * @param UserTokenRepositoryInterface $usertoken
     * @param UserRepositoryInterface $user
     * @param UserAccountService $userservice
     */
    public function __construct(BetSourceRepositoryInterface $betsource,
                                UserTokenRepositoryInterface $usertoken,
                                UserRepositoryInterface $user,
                                UserTopBettaRepositoryInterface $usertopbetta,
                                UserAccountService $userservice){

        $this->betsource = $betsource;
        $this->usertoken = $usertoken;
        $this->user = $user;
        $this->usertopbetta = $usertopbetta;
        $this->userservice = $userservice;

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
            'username' => 'required',
            'token' => 'required'
        );

        // validate input
        $validated = $this->_validateParams($input, $rules);

        // confirm source of request
        if(!$this->checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['username']);
        if(!$bettingUserDetails) throw new ValidationException("Validation Failed", 'Invalid Payload - betting account');

        // if this is a club betting account login
        if(isset($input['parent_username'])){
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

    public function processTokenRequestNoValidation($username)
    {
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($username);

        if(!$bettingUserDetails) {
            throw new UserNotFoundException;
        }

        // generate token
        $newToken = $this->_genreateToken();

        if(!$newToken) {
            throw new TokenGenerationException;
        }

        // token expiry - TODO move to a database field
        $expiry = Carbon::now('Australia/Sydney')->addMinute();

        // store token
        if(!$this->_storeToken($bettingUserDetails['id'], $newToken, $expiry)) {
            throw new TokenGenerationException;
        }

        return $newToken;
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
            'username' => 'required',
            'token' => 'required'
        );
        $validated = $this->_validateParams($input, $rules);

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['username']);
        if(!$bettingUserDetails) throw new ValidationException("Validation Failed", 'Betting user account does not exist');

        // if club_name is supplied then this is a child betting acount login and we need to make sure this username is a child of the source?
        if(isset($input['parent_username'])){
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

        $this->usertoken->updateWithId($tokenRecord['id'], array(
            'expiry' => Carbon::now(),
        ));

        // get both user model details
        $userBasic = Auth::user()->toArray();
        $userFull = $this->usertopbetta->getUserDetailsFromUserId($userBasic['id']);

        if (Auth::check()) {
            // record the logout to the activity table
            Activity::log([
                'contentId'   => Auth::user()->id,
                'contentType' => 'User',
                'action'      => 'Token Log In',
                'description' => 'User logged into TopBetta',
                'details'     => 'Username: '.Auth::user()->username,
                //'updated'     => $id ? true : false,
            ]);
        }

        // return user and topbetta_user model
        return array_merge($userBasic, $userFull);

        // return user model
        //return Auth::user();
    }

    public function tokenLoginExternal($input)
    {

        // get token, username and source and validate
        $rules = array(
            'tournament_username' => 'required',
            'token' => 'required'
        );

        $validated = $this->_validateParams($input, $rules);

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['tournament_username']);
        if(!$bettingUserDetails) {
            throw new UserNotFoundException;
        }

        // get token record for the username
        $tokenRecord = $this->usertoken->getTokenRecordByUserId($bettingUserDetails['id']);

        // if there is no current token in the database
        if(!$tokenRecord) {
            throw new InvalidTokenException("No token found for user");
        }

        // compare the token
        if($tokenRecord['token'] != $input['token']) {
            throw new InvalidTokenException("Token does not match");
        }

        // make sure the token has not expired
        if($tokenRecord['expiry'] < Carbon::now('Australia/Sydney')) {
            throw new InvalidTokenException("Token has expired");
        }

        // get an instance of the user
        $user = $this->user->find($bettingUserDetails['id']);

        // log betting user In
        Auth::login($user);

        $this->usertoken->updateWithId($tokenRecord['id'], array(
            'expiry' => Carbon::now(),
        ));

        // get both user model details
        $userBasic = Auth::user()->toArray();
        $userFull = $this->usertopbetta->getUserDetailsFromUserId($userBasic['id']);

        if (Auth::check()) {
            // record the logout to the activity table
            Activity::log([
                'contentId'   => Auth::user()->id,
                'contentType' => 'User',
                'action'      => 'Token Log In',
                'description' => 'User logged into TopBetta',
                'details'     => 'Username: '.Auth::user()->username,
                //'updated'     => $id ? true : false,
            ]);
        }

        // return user and topbetta_user model
        return array_merge($userBasic, $userFull ? : array());
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

    /**
     * Confirms the betting account is a child of the parent account
     *
     * @param $input
     * @param $bettingUserDetails
     * @return bool
     */
    private function _confirmBettingAccount($input, $bettingUserDetails){
        // get club user details
        $clubUserDetails = $this->user->getUserDetailsFromUsername($input['parent_username']);

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

}