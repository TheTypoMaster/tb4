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
use TopBetta\Services\Validation\Exceptions\ValidationException;


/**
 * Class TokenAuthenticationService
 * @package TopBetta\Services\Authentication
 */
class TokenAuthenticationService {

    protected $betorigin;
    protected $usertoken;
    protected $user;

    /**
     * Inject class dependencies
     *
     * @param BetOriginRepositoryInterface $betorigin
     * @param UserTokenRepositoryInterface $usertoken
     * @param UserRepositoryInterface $user
     */
    public function __construct(BetOriginRepositoryInterface $betorigin,
                                UserTokenRepositoryInterface $usertoken,
                                UserRepositoryInterface $user){

        $this->betorigin = $betorigin;
        $this->usertoken = $usertoken;
        $this->user = $user;
    }

    /**
     * Public method that receives the request and performs source identification, validation and token reponse
     *
     * @param $input
     * @return array
     * @throws ValidationException
     */
    public function processTokenRequest($input){

        $rules = array(
            'source' => 'required',
            //'club_name' => 'required',
            'betting_user_name' => 'required',
            'token' => 'required'
        );

        // validate input
        $validated = $this->_validateParams($input, $rules);

        // confirm source of request
        if(!$this->_checkSource($input)) throw new ValidationException("Validation Failed", 'Invalid Payload - source');

        // get betting account user details
        $bettingUserDetails = $this->user->getUserDetailsFromUsername($input['betting_user_name']);
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
        if(!$bettingUserDetails) throw new ValidationException("Validation Failed", 'Invalid Payload - betting account');

        // if club_name is supplied then this is a child betting acount login and we need to make sure this username is a child of the source?
        if(isset($input['club_name'])){
            if(!$this->_confirmBettingAccount($input, $bettingUserDetails)) throw new ValidationException("Validation Failed", 'Invalid Payload - non child');
        }

        // get token record for the username
        $tokenRecord = $this->usertoken->getTokenRecordByUserId($bettingUserDetails['id']);

        if(!$tokenRecord) throw new ValidationException("Validation Failed", 'Invalid Payload - no token for betting');

        // check the expiry
        if($tokenRecord['expiry'] < Carbon::now('Australia/Sydney')) throw new ValidationException("Validation Failed", 'Invalid Payload - token expired:'.$tokenRecord['expiry']. ', Current Time: '.Carbon::now('Australia/Sydney'));

        // compare the token
        if($tokenRecord['token'] != $input['token']) throw new ValidationException("Validation Failed", 'Invalid Payload - token invalid');

        $user = \TopBetta\Models\UserModel::find(6996);

        // Log user In
        Auth::login($user);

        return Auth::user();
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

        $sharedSecret = $sourceDetails['shared_secret'];

        $clubname = '';

        if(isset($input['club_user_name'])) $clubname = $input['club_user_name'];

        $hashString = $input['source'] . $clubname . $input['betting_user_name'] . $sourceDetails['shared_secret'];

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

}