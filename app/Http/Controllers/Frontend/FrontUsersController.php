<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use TopBetta;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use Regulus\ActivityLog\Activity;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use View;
use Auth;
use Redirect;

class FrontUsersController extends Controller {

    /**
     * @var UserDashboardNotificationService
     */
    private $userDashboardNotificationService;

    public function __construct(UserDashboardNotificationService $userDashboardNotificationService) {
		//we are only protecting certain routes in this controller
		$this -> beforeFilter('auth', array('only' => array('index')));

        $this->userDashboardNotificationService = $userDashboardNotificationService;
    }

	public function login() {

		$input = Input::json() -> all();

		$rules = array('username' => 'required', 'password' => 'required');

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			$l = new \TopBetta\LegacyApiHelper;
			$login = $l -> query('doUserLogin', $input);

			if ($login['status'] == 200) {

				// we do a standard laravel auth with the joomla user id in the DB
				\Auth::loginUsingId($login['userInfo']['id']);

				if (Auth::check()) {

					$tbUser = \TopBetta\TopBettaUser::where('user_id', '=', Auth::user()->id) -> first();

					if (!$login['userInfo']['full_account']) {
						$parts = explode(" ", Auth::user()->name);
						$lastname = array_pop($parts);
						$firstname = implode(" ", $parts);
					} else if ( $tbUser ) {
						//redundant but don't want to break anything
						$lastname = $tbUser->last_name;
						$firstname = $tbUser->first_name;
					} else  {

						$lastname = $login['userInfo']['last_name'];
						$firstname = $login['userInfo']['first_name'];

					}

					$mobile = NULL;
					$verified = false;

					if ($tbUser){
						$mobile = $tbUser -> msisdn;
						$verified = ($tbUser -> identity_verified_flag) ? true : false;
					}

					// record the login to the activity table
					Activity::log([
						'contentId'   => Auth::user()->id,
						'contentType' => 'User',
						'action'      => 'Legacy Log In',
						'description' => 'User logged in to TopBetta',
						'details'     => 'Username: '.Auth::user()->username,
						//'updated'     => $id ? true : false,
					]);

					return array("success" => true, "result" => array("id" => $login['userInfo']['id'], "username" => $login['userInfo']['username'], "first_name" => ucwords($firstname), "last_name" => ucwords($lastname), "email" => \Auth::user()->email, "mobile" => $mobile, "full_account" => $login['userInfo']['full_account'], "verified" => $verified, "register_date" => \TimeHelper::isoDate(\Auth::user()->registerDate)));

				} else {

					return array("success" => false, "error" => Lang::get('users.login_problem'));

				}

			} else {

				return array("success" => false, "error" => $login['error_msg']);

			}

		}

	}

	public function logout() {

		if (Auth::check()) {
			// record the logout to the activity table
			Activity::log([
				'contentId'   => Auth::user()->id,
				'contentType' => 'User',
				'action'      => 'Legacy Log Out',
				'description' => 'User logged out of TopBetta',
				'details'     => 'Username: '.Auth::user()->username,
				//'updated'     => $id ? true : false,
			]);
		}

		//logout of laravel only
		Auth::logout();

		if (Auth::check()) {

			return array("success" => false, "error" => Lang::get('users.logout_problem'));

		} else {

			//kill our laravel session which joomla is relying on
			\Session::regenerate();



			return array("success" => true, "result" => Lang::get('users.logout_success'));

		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		$action = \Input::get('action');

		switch ($action) {
			case 'exclude' :

				//forward to legacy API to handle
				$l = new \TopBetta\LegacyApiHelper;

				$exclude = $l -> query('doSelfExclude', $input = array());

				if ($exclude['status'] == 200) {

					//log this user out of laravel - joomla logout is done via legacy api
					$this -> logout();
					return array('success' => true, 'result' => $exclude['msg']);

				} else {

					return array('success' => false, 'error' => $exclude['error_msg']);

				}
				break;

			default :
				break;
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	
	public function getBrowser()
	{
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";
	
		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
	
		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}
	
		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
	
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}
	
		// check if we have a number
		if ($version==null || $version=="") {$version="?";}
	
		return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'    => $pattern
		);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {

		$input = Input::json() -> all();
		
		// $ua=$this->getBrowser();
		//$yourbrowser= "Your browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
		//$timeStamp = date("YmdHis");
		//\File::append('/tmp/UserSignups-'.$timeStamp, json_encode($input) .". ".$yourbrowser);

		//\Log::debug(json_encode($input) .". ".$yourbrowser);
		
		$rules = array('username' => 'regex:(.*[a-zA-Z].*)', 'first_name' => 'required|alpha_num|min:3', 'last_name' => 'required|alpha_num|min:3', 'source' => 'required|alpha_dash', 'type' => 'required|in:basic,upgrade,full');

		//shared between upgrade & full accounts
		$extRules = array('title' => 'required|in:Mr,Mrs,Ms,Miss,Dr,Prof', 
			'dob_day' => 'required|max:2', 
			'dob_month' => 'required|max:2', 
			'dob_year' => 'required|max:4', 
			//'phone' => 'required|min:9', 
			'postcode' => 'required|max:6', 
			'street' => 'required|max:100', 
			'city' => 'required|max:50', 
			'state' => 'required|max:50', 
			'country' => 'required|alpha|max:3', 
			'promo_code' => 'alpha_dash|max:100', 
			'heard_about' => 'alpha_dash|max:200',
			'heard_about_info' => 'alpha_dash|max:200');

		if (isset($input['type']) && $input['type'] == 'basic') {

			$rules['email'] = 'required|email|unique:tbdb_users';
			//$rules['mobile'] = 'required|min:9';
			$rules['password'] = array('required', 'min:5', 'regex:([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])');
			// terms wraps up privacy/terms & marketing as 1 options now
			$rules['terms'] = 'accepted';	
			$input['optbox'] = 1;

		}

		if (isset($input['type']) && $input['type'] == 'upgrade') {

			$rules = array_merge($rules, $extRules);

		}

		if (isset($input['type']) && $input['type'] == 'full') {

			$extRules['username'] = 'unique:tbdb_users|regex:(.*[a-zA-Z].*)';
			// terms wraps up privacy/terms & marketing as 1 options now
			$rules['terms'] = 'accepted';	
			$input['optbox'] = 1;
						
			$rules = array_merge($rules, $extRules);

		}

		$validator = \Validator::make($input, $rules);

		if ($validator -> fails()) {

			return array("success" => false, "error" => $validator -> messages() -> all());

		} else {

			// create user via legacy API
			$l = new \TopBetta\LegacyApiHelper;

			if ($input['type'] == 'basic') {

				$user = $l -> query('doUserRegisterBasic', $input);

			} elseif ($input['type'] == 'upgrade') {

				// check if user account is upgraded already
				$alreadyUpgraded = Auth::user()->isTopBetta;

				if($alreadyUpgraded){
					return array("success" => false, "result" => 'Your account is already upgraded!');
				}

				$user = $l -> query('doUserUpgradeTopBetta', $input);

			} elseif ($input['type'] == 'full') {

				$user = $l -> query('doUserRegisterTopBetta', $input);

			}

			if ($user['status'] == 200) {

                $this->userDashboardNotificationService->notify(array('id' => array_get($user, 'id', null)));

				if ($input['type'] != 'upgrade') {

					return array("success" => true, "result" => \Lang::get('users.account_created', array('username' => $user['username'])));

				} else {

					return array("success" => true, "result" => \Lang::get('users.account_upgraded'));

				}

			} else {

				if ($input['type'] == 'basic') {

					return array("success" => false, "error" => $user['error_msg'] . str_replace("<br>", " ", $user['errors']));

				} else {

					return array("success" => false, "error" => $user['error_msg']);

				}

			}

		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {
		//
	}

    public function tempLogin(){
        return View::make('users.login');
    }

    public function handleLogin(){

        $data = Input::only(['username', 'password']);

        if(Auth::attempt(['username' => $data['username'], 'password' => $data['password']])){
            return Redirect::to('/laraveladmin');
        }

        return Redirect::route('login')->withInput();
    }

    public function handleProfile(){
        return View::make('users.profile');
    }

    public function handleLogout(){
        if(Auth::check()){
            Auth::logout();
        }
        return Redirect::route('login');
    }
}
