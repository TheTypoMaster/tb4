<?php
/**
 * @version		$Id: controller.php 12538 2009-07-22 17:26:51Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('mobileactive.client.geoip');

/**
 * User Component Controller
 *
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.5
 */
class topbettaUserController extends JController
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
    	$authenticate = array(
    		'save',
    		'selfexclude',
    		'betlimits',
    		'betlimits_save',
			'resendverification'
		);

		$user =& JFactory::getUser();
        parent::__construct();

		$task = JRequest::getVar('task', 'display');
		if('accountsettings' == JRequest::getVar('layout')) {
			$authenticate[] = 'display';
		}

    	if ($user->guest && in_array($task, $authenticate)) {
      		$msg = JText::_("You need to login to access this part of the site." . $task);
			$this->setRedirect('/user/register', $msg, 'error');
			$this->redirect();
		}
    }

	/**
	 * Method to display a view
	 *
	 * @access	public
	 * @since	1.5
	 */
	public function display()
	{
		$view	= JRequest::getVar( 'view', 'register');
		$layout	= JRequest::getVar( 'layout', 'default' );
		$view	=& $this->getView( $view, 'html');
		$session =& JFactory::getSession();

		$formData = array();
		if ('accountsettings' == $layout) {
			$loginuser	=& JFactory::getUser();
			$model		=& $this->getModel( 'topbettaUser');
			$model->LoadDynamicOptions();
			$view->assign( 'options', $model->options);
			$model->setId($loginuser->get('id'));
			$user		=& $model->getUser();
			$view->assign('user', $user);
			$view->assign('itemid', JRequest::getString('Itemid', null, 'get'));
			//$view->assign('isTopBetta', $loginuser->get('isTopBetta'));
			$view->assign('isTopBetta', $model->isTopbettaUser($loginuser->id));
			
			$formData['jackpot_reminder'] = $user->email_jackpot_reminder_flag;
            
			
			if($model->isFacebookUser($loginuser->id)){
            
			$view->assign('isFacebookUser', '1' );

			$formData['entriesToFbWall'] = $loginuser->entriesToFbWall;
			$formData['freeWinsToFbWall'] = $loginuser->freeWinsToFbWall;
			$formData['paidWinsToFbWall'] = $loginuser->paidWinsToFbWall;
			$formData['betWinsToFbWall'] = $loginuser->betWinsToFbWall;

			}else{

            $view->assign('isFacebookUser', '0' );
			
			}
			
            $user_country_model =& $this->getModel('UserCountry');
			$country = $user_country_model->getUserCountryByCode($user->country);
			$view->assign('country', $country->name);
		}
		

		
		if ($sessFormData = $session->get('sessFormData', null, 'topbettauser')) {
			if($sessFormErrors = $session->get('sessFormErrors', null, 'topbettauser')) {
				$view->assign( 'formErrors', $sessFormErrors);
				$session->clear('sessFormErrors', 'topbettauser');
			}

			$formData['jackpot_reminder'] = 0;
			foreach($sessFormData as $k => $data) {
				$formData[$k] = stripslashes($data);
			}

			$session->clear('sessFormData', 'topbettauser');
		}

		$view->assign('formData', $formData);
		parent::display();
	}

	/**
	 * Method to save a user
	 *
	 * @access	public
	 * @since	1.5
	 */
	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model =& $this->getModel( 'topbettaUser');
		$model->loadDynamicOptions();
		$session =& JFactory::getSession();

		$user =& JFactory::getUser();
		$userId = JRequest::getVar( 'uid', 0, 'post', 'int' );

		// preform security checks

		if ($userId == 0 || $user->guest || $userId != $user->get('id')) {
			JError::raiseError( 403, JText::_('Access Forbidden') );
			return;
		}

		$mask = JRequest::getBool('mask', null, 'post');

		if ($mask) {
			$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
		} else {
			$password	= JRequest::getString('passwordTxt', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2Txt', null, 'post', JREQUEST_ALLOWRAW);
		}

		$jackpot_reminder 	= JRequest::getString('jackpot_reminder', 0, 'post');
		$entriesToFbWall 	= JRequest::getInt('entriesToFbWall', 0, 'post');
		$freeWinsToFbWall 	= JRequest::getInt('freeWinsToFbWall', 0, 'post');
		$paidWinsToFbWall 	= JRequest::getInt('paidWinsToFbWall', 0, 'post');
		$betWinsToFbWall 	= JRequest::getInt('betWinsToFbWall', 0, 'post');
		$itemId				= JRequest::getInt('itemid', null, 'post');

		$err = array();
		// do a password safety check
		$passwordLength		= strlen($password);
		$passwordLeftCount	= $passwordLength;

		if(!empty($password) || !empty($password2)) {
			$this->_validate_password($password, $password2, $err);
		}

		$redirectTo = '/user/account/settings';
		if (count($err) >  0) {
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}

		if ($password) {
			$data = array(
				'password' => $password,
				'password2' => $password2,
			);
			if (!$user->bind($data)) {
				$this->setRedirect( $redirectTo, 'Failed to bind data!', 'error' );
				return false;
			}
			if (!$user->save(true)) {
				$this->setRedirect( $redirectTo, 'Failed to store your password! Please contact us.', 'error' );

				return false;
			}
		}
 
		if ( $user->get('entriesToFbWall')!=$entriesToFbWall || $user->get('freeWinsToFbWall')!=$freeWinsToFbWall || $user->get('paidWinsToFbWall')!=$paidWinsToFbWall || $user->get('betWinsToFbWall')!=$betWinsToFbWall ) {
			$data = array(
				'entriesToFbWall' => $entriesToFbWall,
				'freeWinsToFbWall' => $freeWinsToFbWall,
				'paidWinsToFbWall' => $paidWinsToFbWall,
				'betWinsToFbWall' => $betWinsToFbWall,
			);
            if (!$user->bind($data)) {
				$this->setRedirect( $redirectTo, 'Failed to bind data for facebook preferences!', 'error' );
				return false;
			}
			if (!$user->save(true)) {
				$this->setRedirect( $redirectTo, 'Failed to store your Facebook preferences! Password changed! Please contact us.', 'error' );

				return false;
			}
		}

		$model->setId($userId);
		$user_data_before_save = $model->getUser();
		

		if (!$model->update('email_jackpot_reminder_flag', $jackpot_reminder)) {
			$this->setRedirect( $redirectTo, 'Failed to update your preference for Jackpot tournaments betting open! Please contact us.', 'error' );

			return false;
		}


		$audit_model =& $this->getModel('UserAudit', 'TopbettaUserModel');
		if (!empty($password)) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'password',
				'old_value'		=> '*',
				'new_value'		=> '*',
			);
			$audit_model->store($audit_params);
		}

		if ($jackpot_reminder != $user_data_before_save->email_jackpot_reminder_flag) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'email_jackpot_reminder_flag',
				'old_value'		=> $user_data_before_save->email_jackpot_reminder_flag,
				'new_value'		=> $jackpot_reminder,
			);
			$audit_model->store($audit_params);
		}

		if ($entriesToFbWall != $user_data_before_save->entriesToFbWall) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'entriesToFbWall',
				'old_value'		=> $user_data_before_save->entriesToFbWall,
				'new_value'		=> $entriesToFbWall,
			);
			$audit_model->store($audit_params);
		}

		if ($freeWinsToFbWall != $user_data_before_save->freeWinsToFbWall) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'freeWinsToFbWall',
				'old_value'		=> $user_data_before_save->freeWinsToFbWall,
				'new_value'		=> $freeWinsToFbWall,
			);
			$audit_model->store($audit_params);
		}
		if ($paidWinsToFbWall != $user_data_before_save->paidWinsToFbWall) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'paidWinsToFbWall',
				'old_value'		=> $user_data_before_save->paidWinsToFbWall,
				'new_value'		=> $paidWinsToFbWall,
			);
			$audit_model->store($audit_params);
		}
		if ($betWinsToFbWall != $user_data_before_save->betWinsToFbWall) {
			$audit_params = array(
				'user_id'		=> $user_data_before_save->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'betWinsToFbWall',
				'old_value'		=> $user_data_before_save->betWinsToFbWall,
				'new_value'		=> $betWinsToFbWall,
			);
			$audit_model->store($audit_params);
		}

		$this->setRedirect( '/', 'Your account settings have been updated.' );
	}


	/**
	* Method to cancel the form
	*
	* @return void
	*/
	public function cancel()
	{
		$this->setRedirect( '/' );
	}

	/**
	* Method to login
	*
	* @return void
	*/
	public function login()
	{
		// Check for request forgeries
		JRequest::checkToken('request') or header(JURI::current());

		global $mainframe;

		$return = '/';
		if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
			$return = base64_decode($return);
			if (!JURI::isInternal($return)) {
				$return = '/';
			}
		}

		$options = array();
		$options['remember']	= JRequest::getBool('remember', false);
		$options['return']		= $return;

		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		//preform the login action
		$error = $mainframe->login($credentials, $options);

		$userPin = $credentials['username'];
		$userPass = $credentials['password'];

		if (!JError::isError($error)) {
			if (JRequest::getBool('getsatisfaction', false)) {
				$return		= '/user/getsatisfaction/nonssl/login';
			}
			//MC - temp using fb flag for basic user popup
			$session =& JFactory::getSession();
			$session->set( 'LoggedInFromFb','1' );
			$mainframe->redirect( $return );
		} else {
			// Facilitate third party login forms
			if (JRequest::getBool('getsatisfaction', false)) {
				$return = '/user/login/getsatisfaction';
			}
			// Redirect to a login form
			$mainframe->redirect($return);
		}
	}

	/**
	* Method to logout
	*
	* @return void
	*/
	public function logout()
	{
		global $mainframe;

		//preform the logout action
		$error = $mainframe->logout();

		if (!JError::isError($error)) {
			$mainframe->redirect( '/' );
		} else {
			parent::display();
		}
	}

	/**
	 * Prepares the quick registration form
	 * @return void
	 */
	function quick_register()
	{
		$user 		= clone(JFactory::getUser());
		$authorize	=& JFactory::getACL();
		
		$first_name	= JRequest::getVar('first_name', null, 'post');
		$last_name	= JRequest::getVar('last_name', null, 'post');
		$email		= JRequest::getVar('email', null, 'post');
		$email2		= JRequest::getVar('email2', null, 'post');
		$password	= JRequest::getVar('password', null, 'post', JREQUEST_ALLOWRAW);
        $password2	= JRequest::getVar('password2', null, 'post', JREQUEST_ALLOWRAW);
		$mobile		= JRequest::getVar('mobile', null, 'post');
		$from_url	= JRequest::getVar('from_url', null, 'post');
		$optbox		= JRequest::getBool('optbox', false, 'post');
		$privacy	= JRequest::getBool('privacy', false, 'post');
		$terms		= JRequest::getBool('terms', false, 'post');		
		$source		= JRequest::getString('source', null, 'post');
		$source		= ($source !== null) ? $source : htmlspecialchars($_SERVER['HTTP_REFERER']);
		$redirect_toun_id		= JRequest::getString('redirect_toun_id', null, 'post');

		$user_model				=& $this->getModel( 'topbettaUser');
		$pre_registration_model	=& $this->getModel( 'userPreRegistration');

		$session =& JFactory::getSession();
		
		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			$this->setRedirect($from_url, JText::_(JError::raiseError( 403, JText::_( 'Access Forbidden' ))), 'error');	
		}

		//validations for username and email
		$err = array();

		$this->_validate_name($first_name, 'first', $err);
		$this->_validate_name($last_name, 'last', $err);
		//$this->_validate_username($username, $user_model, $err);
		$this->_validate_email($email, $email2, $user_model, $err);
		//$this->_validate_mobile($mobile, $err);
		$this->_validate_password($password, $password2, $err);
		
		if (!$privacy) {
			$err['privacy'] = 'Please select privacy policy.';
		}

		if (!$terms) {
			$err['terms'] = 'Please select terms and Conditions.';
		}

		if (!empty($err)) {
			$session->set('sessFormErrors', $err, 'userpreregistration');
			$session->set('sessFormData', $_POST, 'userpreregistration');
			$this->setRedirect($from_url, JText::_(join('; ', $err)), 'error');
			return;
		}	

		//$pre_registration = $pre_registration_model->getUserPreRegistrationByEmail($email);
		$username = $this->_generate_username($first_name,$last_name, $user_model);
		
		// Put data in required fields
		$fullName	= $first_name.' '.$last_name;
		
		$postVariables['username']	= $username; //generated username
		$postVariables['name']		= $fullName; 
		$postVariables['email']		= $email; 
		$postVariables['password']	= $postVariables['password2'] = $password; 

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}

		// Bind the post array to the user object
		if (!$user->bind( $postVariables, 'usertype' )) {
			$this->setRedirect($from_url, JText::_(JError::raiseError( 500, $user->getError())), 'error');
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		if ($useractivation == '1') {
			jimport('joomla.user.helper');
			$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}

                   
		// If there was an error with registration, set the message and display form
		if (!$user->save()) {
		 	$this->setRedirect($from_url, JText::_(JError::raiseError( 500, $user->getError())), 'error');
		}
				    
	    // Send registration confirmation mail
        $this->_sendMail($user);
		

        // Everything went fine, set relevant message depending upon user activation state and display message
		if ($useractivation == 1) {
				$this->setRedirect($from_url, JText::_( 'Your TopBetta account has been created. Please check your email to activate your account.'), 'success');

		} else {
			//redirect if redirect if tournamant id set
			if(!empty($redirect_toun_id)) {
				$this->setRedirect($redirect_toun_id, JText::_( 'Your TopBetta account has been created. You can now login.' ), 'success');
			}
			else {
				$this->setRedirect($from_url, JText::_( 'Your TopBetta account has been created. You can now login.' ), 'success');	
			}
		}
		
		// get the userid
		$user_id = $user->get('id');
		
			// Create User Extension table record for new user.
			$params = array(
			  'user_id'					=> $user_id,
			  'title'					=> $title,
			  'first_name'				=> $first_name,
			  'last_name'				=> $last_name,
			  'msisdn'					=> $mobile,
			  'source'					=> $source,
			  'marketing_opt_in_flag'	=> $optbox ? 1 : 0,
			);
	
			if (!$user_model->store($params)) {
				$this->setRedirect($from_url, JText::_('Cannot save the user.'), 'error');
				return;
			}
	}

	/**
	 * Prepares the registration form
	 * @return void
	 */
	function register()
	{
		$view	= JRequest::getVar( 'view', 'register');
		$layout	= JRequest::getVar( 'layout', 'default' );
		$view	=& $this->getView( $view, 'html');

		$model=& $this->getModel( 'topbettaUser');
		$model->loadDynamicOptions();
		
		$user_country_model =& $this->getModel('UserCountry');
		$country_list = $user_country_model->getUserCountryList();
		$view->assign('country_list', $country_list);

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if (!$usersConfig->get( 'allowUserRegistration' )) {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		$user =& JFactory::getUser();
		if ($user->guest) {
			JRequest::setVar('view', 'register');
		} else {
			$msg = JText::_("You are already registered and logged in.");

			$link = JURI::base();
			global $mainframe;
			$mainframe->redirect($link,$msg);
		}

		//pre-populate the fields with the $_GET, such like 'promo_code' etc
		$formData			= array();
		$prepopulateFields	= array(
			'username', 'title', 'first_name', 'last_name', 'dob_day', 'dob_month', 'dob_year',
			'mobile', 'phone', 'email', 'email2', 'password', 'password2',
			'street', 'city', 'state', 'country', 'postcode',
			'promo_code', 'heard_about', 'heard_about_info', 'optbox', 'source', 'banner_id'
      	);

		foreach ($prepopulateFields as $prepopulateField) {
	        $formData[$prepopulateField] = JRequest::getVar($prepopulateField, null);
		}

		$session =& JFactory::getSession();

		$quick_registration_code = $session->get('quickRegistrationCode', null, 'topbettauser');
      	$session->clear('quickRegistrationCode', 'topbettauser');
		if (empty($quick_registration_code)) {
			$formData['optbox'] = 1;
		}

		$sessFormErrors	= array();
		if ($sessFormData = $session->get('sessFormData', null, 'topbettauser'))
		{
      		if ($sessFormErrors = $session->get('sessFormErrors', null, 'topbettauser'))
      		{
        		$session->clear('sessFormErrors', 'topbettauser');
      		}
      		foreach ($sessFormData as $k => $data) {
      			$formData[$k] = stripslashes($data);
      		}
      		$formData['password'] = $formData['password2'] = '';
      		$session->clear('sessFormData', 'topbettauser');
		}
		$view->assign('formErrors', $sessFormErrors);
		$view->assign('formData', $formData );
		$view->assign('options', $model->options);

		$sport_cookie = JRequest::getVar('FirstVisit', null, 'cookie');

		if (ctype_digit($sport_cookie)) {
      		JLoader::import( 'tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
			$sport					= $tournament_sport_model->getTournamentSport($sport_cookie);
			$sport_cookie			= preg_replace('/[^a-z0-9]/i', '', strtolower($sport->name));
		}
		$view->assign('sport_cookie', $sport_cookie);

		$tracking_code = '';
		if (!isset($_COOKIE['registrationTracking'])) {
			//set registrationTracking expires after 30 days
			setcookie("registrationTracking", '1', time()+2592000, '/');

			$params	=& JComponentHelper::getParams('com_topbetta_user');
			$tracking_code = $params->get('registrationTrackingCode');
		}
		$view->assign('registration_tracking_code', $tracking_code);

		parent::display();
	}

	/**
	 * Save user registration and notify users and admins if required
	 * @return void
	 */
	function register_save()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		$model =& $this->getModel( 'topbettaUser');
		$model->loadDynamicOptions();
		$session =& JFactory::getSession();

		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Get user registration details from post.
		$username	= JRequest::getString('username', null, 'post');
		$title		= JRequest::getString('title', null, 'post');
		$first_name	= JRequest::getString('first_name', null, 'post');
		$last_name	= JRequest::getString('last_name', null, 'post');
		$dob_day	= JRequest::getInt('dob_day', null, 'post');
		$dob_month	= JRequest::getInt('dob_month', null, 'post');
		$dob_year	= JRequest::getInt('dob_year', null, 'post');
		
		$email		= JRequest::getString('email', null, 'post');
		$email2		= JRequest::getString('email2', null, 'post');
		$mask		= JRequest::getBool('mask', false, 'post');

		if ($mask) {
			$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
		} else {
			$password	= JRequest::getString('passwordTxt', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2Txt', null, 'post', JREQUEST_ALLOWRAW);
		}
		
		$mobile			= JRequest::getString('mobile', null, 'post');
		$phone			= JRequest::getString('phone', null, 'post');
		$postcode		= JRequest::getString('postcode', null, 'post');
		$street				= JRequest::getString('street', null, 'post');
		$city				= JRequest::getString('city', null, 'post');
		$state				= JRequest::getString('state', null, 'post');
		$country			= JRequest::getString('country', null, 'post');
		$promo_code			= JRequest::getString('promo_code', null, 'post');
		$heard_about		= JRequest::getString('heard_about', null, 'post');
		$heard_about_info	= JRequest::getString('heard_about_info', null, 'post');
		$optbox				= JRequest::getBool('optbox', false, 'post');
		$privacy			= JRequest::getBool('privacy', false, 'post');
		$terms				= JRequest::getBool('terms', false, 'post');
		$source				= JRequest::getString('source', null, 'post');

		//do validations
		$err = array();

		$this->_validate_username($username, $model, $err);
		$this->_validate_email($email, $email2, $model, $err);
		$this->_validate_password($password, $password2, $err);
		$this->_validate_country($err);

		if('' == $title) {
			$err['title'] = 'Please select a title.';
		} else if( !isset($model->options['title'][$title])) {
			$err['title'] = 'Invalid option.';
		}

		$firstnameLength = strlen($first_name);
		if ('' == $first_name) {
			$err['first_name'] = 'Please enter a first name.';
		} else if ($firstnameLength < 3) {
			$err['first_name'] = 'First name must contain at least 3 characters.';
		} else if ($firstnameLength > 50) {
			$err['first_name'] = 'Maximum length is 50.';
		}

		$lastnameLength = strlen($last_name);
		if ('' == $last_name) {
			$err['last_name'] = 'Please enter a last name.';
		} else if($lastnameLength < 3) {
			$err['last_name'] = 'Last name must contain at least 3 characters.';
		} else if($lastnameLength > 50) {
			$err['last_name'] = 'Maximum length is 50.';
		}

		if('' == $dob_day || '' == $dob_month || '' == $dob_year) {
			$err['dob'] = 'Please select the date you were born.';
		} else if (!checkdate($dob_month, $dob_day, $dob_year)) {
			$err['dob'] = 'Invalid date';
		} else {
			$age = date('Y') - $dob_year;
			if (date('md') < ($dob_month . sprintf('%02s', $dob_day))) {
				$age--;
			}

			if ($age < 18) {
				$err['dob'] = 'Only people over 18 can register.';
			}
		}

		if ('' == $street) {
			$err['street'] = 'Please enter your street address.';
		} else if (strlen($street) > 100) {
			$err['street'] = 'Street address is too long.';
		}

		if ('' == $city) {
			$err['city'] = 'Please enter the suburb/city you live in.';
		} else if (strlen($city) > 50) {
			$err['city'] = 'City name is too long.';
		}
		
		if (empty($state)) {
			$err['state'] = 'Please select the state you live in.';
		} else if(strtolower($country) == 'au' && $state == 'other') {
			$err['state'] = 'Please select the state you live in.';
		} else if(strtolower($country) != 'au' && $state != 'other'){
			$err['state'] = 'Invalid option.';
		} else if(!isset($model->options['state'][$state])) {
			$err['state'] = 'Invalid option.';
		}

		if($promo_code) {
			$this->_validate_promotion($promo_code, $err);
		}

		if ('' != $heard_about && !isset($model->options['heard_about'][$heard_about])) {
			$err['heard_about'] = 'Invalid option';
		}

		if ('' != $heard_about && '' == $heard_about_info && in_array($heard_about, array('Friend', 'Advertisement', 'Internet', 'Promotion', 'Other'))) {
			$err['heard_about_info'] = 'Please provide additional information.';
		}

		if (!$privacy) {
			$err['privacy'] = 'Please select privacy policy.';
		}

		if (!$terms) {
			$err['terms'] = 'Please select terms and Conditions.';
		}

		$redirectTo = 'user/register';

		if (count($err) >  0) {
			
			
			$error_message = 'There were some errors processing this form. See messages below.';
			if(isset($err['country'])){
				$error_message = 'Your current IP address doesn\'t match the country you have selected. Please try again or <a href="/contact-us">contact us</a> if you feel this is an error';
			}
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, $error_message, 'error' );

			return false;
		}

		// Put data in required fields
		$fullName	= $first_name.' '.$last_name;
		//$homeNumber = $acode.$phone;
		$fullDOB	= $dob_year.'-'.$dob_month.'-'.$dob_day;

		// Setup user details array
		$userDetails = array(
	        'DateOfBirth'	=> "$fullDOB",
	        'FirstName'		=> "$first_name",
		);

		$postVariables['username']	= $username; //1
		$postVariables['name']		= $fullName; //3+4
		$postVariables['email']		= $email; //8
		$postVariables['password']	= $postVariables['password2'] = $password; //11&12

		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';
		}

		// Bind the post array to the user object
		if (!$user->bind( $postVariables, 'usertype' )) {
			JError::raiseError( 500, $user->getError());
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', $newUsertype);
		$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		if ($useractivation == '1') {
			jimport('joomla.user.helper');
			$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if (!$user->save()) {
			JError::raiseWarning('', JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN));
			$this->register();
			return false;
		}

		// get the userid
		$user_id = $user->get('id');
		
		$isTopBetta = $model -> userUpgradeTopBetta($user_id);
		
		$btag_cookie = JRequest::getVar('btag', null, 'cookie');

		// Create User Extension table record for new user.
		$params = array(
	      'user_id'					=> $user_id,
	      'title'					=> $title,
	      'first_name'				=> $first_name,
	      'last_name'				=> $last_name,
	      'street'					=> $street,
	      'city'					=> $city,
	      'state'					=> $state,
	      'country'					=> $country,
	      'postcode'				=> $postcode,
	      'dob_day'					=> $dob_day,
	      'msisdn'					=> $mobile,
	      'phone_number'			=> $phone,
	      'dob_month'				=> $dob_month,
	      'dob_year'				=> $dob_year,
	      'promo_code'				=> strtoupper($promo_code),
	      'heard_about'				=> $heard_about,
	      'heard_about_info'		=> $heard_about_info,
	      'marketing_opt_in_flag'	=> $optbox ? 1 : 0,
	      'source'					=> $source,
	      'btag'					=> $btag_cookie,
		);

		if (!$model->store($params)) {
			//TO DO: send web alert email to tech
			$this->setRedirect($redirectTo, 'Update Failed. Please contact webmaster.', 'error');

			return false;
		}

		$pre_registration_model	=& $this->getModel('userPreRegistration');
		$pre_registration_id = $session->get('preRegistraionID', null, 'topbettaUser');

		$params = array(
			'username'			=> $username,
			'email'				=> $email,
			'msisdn'			=> $mobile,
			'registered_flag'	=> 1,
		);

		if ($pre_registration_id != null) {
			$params['id'] = $pre_registration_id;
			$pre_registration_model->store($params);

			$session->clear('preRegistraionID', 'topbettaUser');
		}

		$pre_registration_model->updateByEmail($email, $params);
		
		//Add free credits if valid promotion code
		if(count($err['promo_code'])==0 && !empty($promo_code))
		{
			$promotion = $model->getPromotion(trim(strtoupper($promo_code)));			
			//For tournament dollars
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models' . DS . 'tournamenttransaction.php');
			$tournamentdollars_model = new TournamentdollarsModelTournamenttransaction();
				
			if($promotion) {
				$tournamentdollars_model->increment_for_promo_code($promotion[0]->pro_value, 'promo',$user_id );
			}
		}

		// Send registration confirmation mail
		$this->_sendMail($user);

		// Everything went fine, set relevant message depending upon user activation state and display message
		if ($useractivation == 1) {
			$message  = JText::_( 'REG_COMPLETE_ACTIVATE');
		} else {
			$message = JText::_( 'REG_COMPLETE' );
		}
		$this->setRedirect('/', $message);
	}

	/**
	 * Prepares the upgrade form
	 * @return void
	 */
	function upgrade()
	{
		
		$view	= JRequest::getVar( 'view', 'upgrade');
		$layout	= JRequest::getVar( 'layout', 'default' );
		$view	=& $this->getView( $view, 'html');

		$model=& $this->getModel( 'topbettaUser');
		$model->loadDynamicOptions();
		
		$user_country_model =& $this->getModel('UserCountry');
		$country_list = $user_country_model->getUserCountryList();
		$view->assign('country_list', $country_list);

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if (!$usersConfig->get( 'allowUserRegistration' )) {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		$user =& JFactory::getUser();
		
		//Check the upgrade status
		if($model->isTopbettaUser($user->id)) {
			$this->setRedirect('/user/account/settings', 'You have already upgraded your account.', 'error');
			return false;
		}
		
		if ($user->guest) {
			$msg = JText::_("Please login to upgrade your account.");

			$link = JURI::base();
			global $mainframe;
			$mainframe->redirect($link,$msg);
			
		} else {
			JRequest::setVar('view', 'upgrade');
		}
        
		//pre-populate the fields with the $_GET, such like 'promo_code' etc
		$formData			= array();
		$prepopulateFields	= array(
			'username', 'title', 'first_name', 'last_name', 'dob_day', 'dob_month', 'dob_year',
			'mobile', 'phone', 'email', 'email2', 'street', 'city', 'state', 'country', 'postcode',
			'promo_code', 'heard_about', 'heard_about_info', 'optbox', 'source', 'banner_id'
      	);

		foreach ($prepopulateFields as $prepopulateField) {
	        $formData[$prepopulateField] = JRequest::getVar($prepopulateField, null);
		}

		$session =& JFactory::getSession();

		$quick_registration_code = $session->get('quickRegistrationCode', null, 'topbettauser');
      	$session->clear('quickRegistrationCode', 'topbettauser');
		if (empty($quick_registration_code)) {
			$formData['optbox'] = 1;
		}
        
		$sessFormErrors	= array();
		if ($sessFormData = $session->get('sessFormData', null, 'topbettauser'))
		{
      		if ($sessFormErrors = $session->get('sessFormErrors', null, 'topbettauser'))
      		{
        		$session->clear('sessFormErrors', 'topbettauser');
      		}
      		foreach ($sessFormData as $k => $data) {
      			$formData[$k] = stripslashes($data);
      		}
      		$formData['password'] = $formData['password2'] = '';
      		$session->clear('sessFormData', 'topbettauser');
		}
		
		$view->assign('formErrors', $sessFormErrors);
		$view->assign('formData', $formData );
		$view->assign('options', $model->options);

		$sport_cookie = JRequest::getVar('FirstVisit', null, 'cookie');

		if (ctype_digit($sport_cookie)) {
      		JLoader::import( 'tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
			$sport					= $tournament_sport_model->getTournamentSport($sport_cookie);
			$sport_cookie			= preg_replace('/[^a-z0-9]/i', '', strtolower($sport->name));
		}
		$view->assign('sport_cookie', $sport_cookie);

		$tracking_code = '';
		if (!isset($_COOKIE['registrationTracking'])) {
			//set registrationTracking expires after 30 days
			setcookie("registrationTracking", '1', time()+2592000, '/');

			$params	=& JComponentHelper::getParams('com_topbetta_user');
			$tracking_code = $params->get('registrationTrackingCode');
		}
		$view->assign('registration_tracking_code', $tracking_code);

		parent::display();
	}

	/**
	 * Save user upgradation and notify users and admins if required
	 * @return void
	 */
	function upgrade_save()
    {

		global $mainframe;

		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );
        
		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();
         
        if ($user->guest) {
			$msg = JText::_("Please login to upgrade your account.");
            $link = JURI::base();
			global $mainframe;
			$mainframe->redirect($link,$msg);
			
		}

		$model =& $this->getModel( 'topbettaUser');
		$model->loadDynamicOptions();
		$session =& JFactory::getSession();

		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Get user registration details from post.
		$username	= JRequest::getString('username', null, 'post');
		$title		= JRequest::getString('title', null, 'post');
		$first_name	= JRequest::getString('first_name', null, 'post');
		$last_name	= JRequest::getString('last_name', null, 'post');
		$dob_day	= JRequest::getInt('dob_day', null, 'post');
		$dob_month	= JRequest::getInt('dob_month', null, 'post');
		$dob_year	= JRequest::getInt('dob_year', null, 'post');
		
		
		$mask		= JRequest::getBool('mask', false, 'post');

		
		
		$mobile			= JRequest::getString('mobile', null, 'post');
		$phone			= JRequest::getString('phone', null, 'post');
		$postcode		= JRequest::getString('postcode', null, 'post');
		$street				= JRequest::getString('street', null, 'post');
		$city				= JRequest::getString('city', null, 'post');
		$state				= JRequest::getString('state', null, 'post');
		$country			= JRequest::getString('country', null, 'post');
		$promo_code			= JRequest::getString('promo_code', null, 'post');
		$heard_about		= JRequest::getString('heard_about', null, 'post');
		$heard_about_info	= JRequest::getString('heard_about_info', null, 'post');
		$optbox				= JRequest::getBool('optbox', false, 'post');
		$privacy			= JRequest::getBool('privacy', false, 'post');
		$terms				= JRequest::getBool('terms', false, 'post');
		$source				= JRequest::getString('source', null, 'post');
		//Facebook option
		$entriesToFbWall 	= JRequest::getInt('entriesToFbWall', 0, 'post');
		$freeWinsToFbWall 	= JRequest::getInt('freeWinsToFbWall', 0, 'post');
		$paidWinsToFbWall 	= JRequest::getInt('paidWinsToFbWall', 0, 'post');
		$betWinsToFbWall 	= JRequest::getInt('betWinsToFbWall', 0, 'post');

		//do validations
		$err = array();

		//$this->_validate_country($err);

		if('' == $title) {
			$err['title'] = 'Please select a title.';
		} else if( !isset($model->options['title'][$title])) {
			$err['title'] = 'Invalid option.';
		}

		$firstnameLength = strlen($first_name);
		if ('' == $first_name) {
			$err['first_name'] = 'Please enter a first name.';
		} else if ($firstnameLength < 3) {
			$err['first_name'] = 'First name must contain at least 3 characters.';
		} else if ($firstnameLength > 50) {
			$err['first_name'] = 'Maximum length is 50.';
		}

		$lastnameLength = strlen($last_name);
		if ('' == $last_name) {
			$err['last_name'] = 'Please enter a last name.';
		} else if($lastnameLength < 3) {
			$err['last_name'] = 'Last name must contain at least 3 characters.';
		} else if($lastnameLength > 50) {
			$err['last_name'] = 'Maximum length is 50.';
		}

		if('' == $dob_day || '' == $dob_month || '' == $dob_year) {
			$err['dob'] = 'Please select the date you were born.';
		} else if (!checkdate($dob_month, $dob_day, $dob_year)) {
			$err['dob'] = 'Invalid date';
		} else {
			$age = date('Y') - $dob_year;
			if (date('md') < ($dob_month . sprintf('%02s', $dob_day))) {
				$age--;
			}

			if ($age < 18) {
				$err['dob'] = 'Only people over 18 can register.';
			}
		}

		if ('' == $street) {
			$err['street'] = 'Please enter your street address.';
		} else if (strlen($street) > 100) {
			$err['street'] = 'Street address is too long.';
		}

		if ('' == $city) {
			$err['city'] = 'Please enter the suburb/city you live in.';
		} else if (strlen($city) > 50) {
			$err['city'] = 'City name is too long.';
		}
		
		if (empty($state)) {
			$err['state'] = 'Please select the state you live in.';
		} else if(strtolower($country) == 'au' && $state == 'other') {
			$err['state'] = 'Please select the state you live in.';
		} else if(strtolower($country) != 'au' && $state != 'other'){
			$err['state'] = 'Invalid option.';
		} else if(!isset($model->options['state'][$state])) {
			$err['state'] = 'Invalid option.';
		}

		if($promo_code) {
			$this->_validate_promotion($promo_code, $err);
		}

		if ('' != $heard_about && !isset($model->options['heard_about'][$heard_about])) {
			$err['heard_about'] = 'Invalid option';
		}

		if ('' != $heard_about && '' == $heard_about_info && in_array($heard_about, array('Friend', 'Advertisement', 'Internet', 'Promotion', 'Other'))) {
			$err['heard_about_info'] = 'Please provide additional information.';
		}

		if (!$privacy) {
			$err['privacy'] = 'Please select privacy policy.';
		}

		if (!$terms) {
			$err['terms'] = 'Please select terms and Conditions.';
		}

		$redirectTo = 'index.php?option=com_topbetta_user&task=upgrade';

		if (count($err) >  0) {
			
			
			$error_message = 'There were some errors processing this form. See messages below.';
			if(isset($err['country'])){
				$error_message = 'Your current IP address doesn\'t match the country you have selected. Please try again or <a href="/contact-us">contact us</a> if you feel this is an error';
			}
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, $error_message, 'error' );

			return false;
		}
		
       
		// Put data in required fields
		//$fullName	= $first_name.' '.$last_name;
		//$homeNumber = $acode.$phone;
		$fullDOB	= $dob_year.'-'.$dob_month.'-'.$dob_day;

		// Setup user details array
		$userDetails = array(
	        'DateOfBirth'	=> "$fullDOB",
	        'FirstName'		=> "$first_name",
		);

        $date =& JFactory::getDate();
		
		// get the userid
		$user_id = $user->get('id');
		
		
		$btag_cookie = JRequest::getVar('btag', null, 'cookie');

		// Create User Extension table record for new user.
		$params = array(
	      'user_id'					=> $user_id,
	      'title'					=> $title,
	      'first_name'				=> $first_name,
	      'last_name'				=> $last_name,
	      'street'					=> $street,
	      'city'					=> $city,
	      'state'					=> $state,
	      'country'					=> $country,
	      'postcode'				=> $postcode,
	      'dob_day'					=> $dob_day,
	      'msisdn'					=> $mobile,
	      'phone_number'			=> $phone,
	      'dob_month'				=> $dob_month,
	      'dob_year'				=> $dob_year,
	      'promo_code'				=> strtoupper($promo_code),
	      'heard_about'				=> $heard_about,
	      'heard_about_info'		=> $heard_about_info,
	      'marketing_opt_in_flag'	=> $optbox ? 1 : 0,
	      'btag'					=> $btag_cookie,
		);

		if (!$model->store($params)) {
			//TO DO: send web alert email to tech
			if(!$model->updateUser($params)) {
				$this->setRedirect($redirectTo, 'Upgrade Failed. Please contact webmaster.', 'error');
				return false;
			}
		}
        
        $isTopBetta = $model -> userUpgradeTopBetta($user_id);

		if(!$isTopBetta){
            
			$this->setRedirect($redirectTo, 'Upgrade Failed. Please contact webmaster.', 'error');

			return false;
		}
		
		//Add free credits if valid promotion code
		if(count($err['promo_code'])==0 && !empty($promo_code))
		{
			$promotion = $model->getPromotion(trim(strtoupper($promo_code)));			
			//For tournament dollars
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models' . DS . 'tournamenttransaction.php');
			$tournamentdollars_model = new TournamentdollarsModelTournamenttransaction();
				
			if($promotion) {
				$tournamentdollars_model->increment($promotion[0]->pro_value, 'promo');
			}
		}

		$pre_registration_model	=& $this->getModel('userPreRegistration');
		$pre_registration_id = $session->get('preRegistraionID', null, 'topbettaUser');

		$params = array(
			'username'			=> $username,
			'email'				=> $user->email,
			'msisdn'			=> $mobile,
			'registered_flag'	=> 1,
		);

		if ($pre_registration_id != null) {
			$params['id'] = $pre_registration_id;
			$pre_registration_model->store($params);

			$session->clear('preRegistraionID', 'topbettaUser');
		}

		$pre_registration_model->updateByEmail($email, $params);

		// Everything went fine, set relevant message depending upon user activation state and display message
		$message = "Upgrade completed";
		$this->setRedirect('/', $message);
	}

	/**
	* Method to activate a user
	*
	* @return void
	*/
	public function activate()
	{
		global $mainframe;

		// Initialize some variables
		$db			=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
		$document   =& JFactory::getDocument();
		$pathway 	=& $mainframe->getPathWay();

		$usersConfig			= &JComponentHelper::getParams( 'com_users' );
		$userActivation			= $usersConfig->get('useractivation');
		$allowUserRegistration	= $usersConfig->get('allowUserRegistration');

		// Check to see if they're logged in, because they don't need activating!
		if ($user->get('id')) {
			// They're already logged in, so redirect them to the home page
			$mainframe->redirect( '/' );
		}

		if ($allowUserRegistration == '0' || $userActivation == '0') {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// create the view
		require_once (JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'view.html.php');
		$view = new topbettaUserViewRegister();

		$message = new stdClass();

		// Do we even have an activation string?
		$activation = JRequest::getVar('activation', '', '', 'alnum' );
		$activation = $db->getEscaped( $activation );

		if (empty($activation)) {
			// Page Title
			$document->setTitle(JText::_('REG_ACTIVATE_NOT_FOUND_TITLE'));
			// Breadcrumb
			$pathway->addItem(JText::_('REG_ACTIVATE_NOT_FOUND_TITLE'));

			$message->title	= JText::_('REG_ACTIVATE_NOT_FOUND_TITLE');
			$message->text	= JText::_('REG_ACTIVATE_NOT_FOUND');
			$view->assign('message', $message);
			$view->display('message');
			return;
		}

		// Lets activate this user
		jimport('joomla.user.helper');
		if (JUserHelper::activateUser($activation)) {
			// Page Title
			$document->setTitle(JText::_('REG_ACTIVATE_COMPLETE_TITLE'));
			// Breadcrumb
			$pathway->addItem(JText::_('REG_ACTIVATE_COMPLETE_TITLE'));

			$message->title	= JText::_('REG_ACTIVATE_COMPLETE_TITLE');
			$message->text	= JText::_('REG_ACTIVATE_COMPLETE');

			$params			=& JComponentHelper::getParams('com_topbetta_user');
			$message->js 	= $params->get('registrationActivationTrackingCode');
		} else {
			// Page Title
			$document->setTitle(JText::_('REG_ACTIVATE_NOT_FOUND_TITLE'));
			// Breadcrumb
			$pathway->addItem(JText::_('REG_ACTIVATE_NOT_FOUND_TITLE'));

			$message->title	= JText::_('REG_ACTIVATE_NOT_FOUND_TITLE');
			$message->text	= JText::_('REG_ACTIVATE_NOT_FOUND');
		}

		$view->assign('message', $message);
		$view->display('message');
	}

	/**
	 * Password Reset Request Method
	 *
	 * @return void
	 */
	public function requestreset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$session =& JFactory::getSession();

		// Get the input
		$email	= JRequest::getVar('email', null, 'post', 'string');
		$err	= array();
		if ('' == $email || !eregi("^[_a-z0-9-]+(\.[+_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			$err['email'] = 'Please enter a valid email address.';
		}

		$redirectTo = '/user/remind';

		if (count($err) >  0) {
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}


		// Get the model
		$model = &$this->getModel('topbettaUser');

		// Request a reset
		if ($model->requestReset($email) === false) {
			$message = JText::sprintf('PASSWORD_RESET_REQUEST_FAILED', $model->getError());
			$this->setRedirect('/user/reset', $message);
			return false;
		}

		$this->setRedirect('/user/reset/confirm');
	}

	/**
	 * Password Reset Confirmation Method
	 *
	 * @access	public
	 */
	public function confirmreset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$session =& JFactory::getSession();

		// Get the input
		$token		= JRequest::getVar('token', null, 'post', 'alnum');
		$username	= JRequest::getVar('username', null, 'post');

		$err = array();
		if ('' == $token) {
			$err['token'] = 'Please enter the token which has been sent to you.';
		}

		if ('' == $username) {
			$err['username'] = 'Please enter your username.';
		}

		$redirectTo = '/user/reset/confirm';
		if (count($err) >  0) {
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}

		// Get the model
		$model = &$this->getModel('topbettaUser');

		// Verify the token
		if ($model->confirmReset($token) === false) {
			$message = JText::sprintf('PASSWORD_RESET_CONFIRMATION_FAILED', $model->getError());
			$this->setRedirect($redirectTo, $message);
			return false;
		}

		$this->setRedirect('/user/reset/complete');
	}

	/**
	 * Password Reset Completion Method
	 *
	 * @access	public
	 */
	public function completereset()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$session =& JFactory::getSession();

		// Get the input
		$mask = JRequest::getBool('mask', null, 'post');
		if ($mask) {
			$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
		} else {
			$password	= JRequest::getString('passwordTxt', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2Txt', null, 'post', JREQUEST_ALLOWRAW);
		}

		$err = array();

		$this->_validate_password($password, $password2, $err);

		$redirectTo = '/user/reset/complete';
		if (count($err) >  0) {
			$session->set('sessFormErrors', $err, 'topbettauser');
			$session->set('sessFormData', $_POST, 'topbettauser');
			$this->setRedirect($redirectTo, 'There were some errors processing this form. See messages below.', 'error');

			return false;
		}

		// Get the model
		$model = &$this->getModel('topbettaUser');
		// User Id
		global $mainframe;
		$user_id = $mainframe->getUserState('topbettauser.reset.id');

		// Reset the password
		if ($model->completeReset($password, $password2) === false)
		{
			$message = JText::sprintf('PASSWORD_RESET_FAILED', $model->getError());
			$this->setRedirect($redirectTo, $message);
			return false;
		}

		//log to user audit
		$audit_model =& $this->getModel('UserAudit', 'TopbettaUserModel');

		$params = array(
			'user_id'		=> $user_id,
			'admin_id'		=> -1,
			'field_name'	=> 'password',
			'old_value'		=> '*',
			'new_value'		=> '*',
		);
		$audit_model->store($params);

		$message = JText::_('PASSWORD_RESET_SUCCESS');
		$this->setRedirect('/', $message);
	}

	/**
	 * Username Reminder Method
	 *
	 * @access	public
	 */
	public function remindusername()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		$session =& JFactory::getSession();

		// Get the input
		$email = JRequest::getVar('email', null, 'post', 'string');

		$err = array();
		if ('' == $email || !eregi("^[_a-z0-9-]+(\.[+_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			$err['email'] = 'Please enter a valid email address.';
		}

		$redirectTo = '/user/remind';

		if (count($err) >  0) {
			$session->set('sessFormErrors', $err, 'topbettauser');
			$session->set('sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}

		// Get the model
		$model = &$this->getModel('topbettaUser');

		// Send the reminder
		if ($model->remindUsername($email) === false) {
			$message = JText::sprintf('USERNAME_REMINDER_FAILED', $model->getError());
			$this->setRedirect('/user/remind', $message);
			return false;
		}

		$message = JText::sprintf('USERNAME_REMINDER_SUCCESS', $email);
		$this->setRedirect('/', $message);
	}

	/**
	 * Username Reminder Method
	 *
	 * @user user record
	 * @send_to_admin boolean
	 *
	 * @return void
	 */
	private function _sendMail(&$user, $send_to_admin = true )
	{
		global $mainframe;

		$db			=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_('Account details for'), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ($useractivation == 1) {
			$message = sprintf (JText::_( 'SEND_MSG_ACTIVATE' ), $name, $siteURL."user/activate/".$user->get('activation'), $username, $siteURL."help/3", "help@topbetta.com");
		} else {
			$message = sprintf (JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
        ' FROM #__users' .
        ' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if (!$mailfrom  || ! $fromname) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		$mailer			= new UserMAIL();
		$email_params	= array(
			'subject'	=> $subject,
			'mailto'	=> $email,
			'ishtml'	=> true,
		);
		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username,
			'activation_link'	=> $siteURL."user/activate/".$user->get('activation'),
		);
		$mailer->sendUserEmail('welcomeEmail', $email_params, $email_replacements);

		if ($send_to_admin) {
			// Send notification to all administrators
			// get superadministrators id
			foreach ($rows as $row) {
				if ($row->sendEmail) {
					$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
					$message2 = html_entity_decode($message2, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject, $message2);
				}
			}
		}
	}

	/**
	 * Method to exclude user
	 *
	 * @return void
	 */
	public function selfExclude()
	{
		global $mainframe;

		$user	=& JFactory::getUser();
		$model	=& $this->getModel('TopbettaUser', 'TopbettaUserModel');

		$exclusion_end_timestamp = time() + 60 * 60 * 24 * 7;
		$user_data_before_save	= $model->getUser();

		if ($model->selfExclude($user->id, $exclusion_end_timestamp)) {
			$message = JText::_('You have been excluded for 1 week from the site. An email will be sent to notify you that this period has ended.');
			$this->_sendExcludeEmail($exclusion_end_timestamp);

			$user_data_after_save = $model->getUser();
			//add user audit
			$user_audit_model		=& $this->getModel('userAudit', 'TopbettaUserModel');
			$audit_params = array(
				'user_id'		=> $user->id,
				'admin_id'		=> -1,
				'field_name'	=> 'self_exclusion_date',
				'old_value'		=> $user_data_before_save->self_exclusion_date,
				'new_value'		=> $user_data_after_save->self_exclusion_date,
			);
			$user_audit_model->store($audit_params);

			$mainframe->logout();
		} else {
			$message = JText::_('Sorry, there was a problem excluding you. Please contact our customer service department to be excluded for 1 week.');
		}

		$this->setRedirect('/', $message);
	}

	/**
	 * Method to send exclude email
	 *
	 * @return void
	 */
	private function _sendExcludeEmail($exclusion_end_timestamp)
	{
		global $mainframe;

		$user =& JFactory::getUser();

		$config =& JComponentHelper::getParams('com_topbetta_user');
		$mailfrom = $config->get('mailFrom');
		$fromname = $config->get('fromName');

		$params =& JComponentHelper::getParams('com_topbetta_user');

		$subject		= JText::_('Temporary Exclusion from TopBetta');
		$exclusion_date	= date('d/m/Y', $exclusion_end_timestamp);

		$mailer = new UserMAIL();
		$email_params	= array(
			'mailfrom'	=> $mailfrom,
			'fromname'	=> $fromname,
			'subject'	=> $subject,
			'mailto'	=> $user->email
		);

		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username,
			'date'				=> $exclusion_date
		);

		$mailer->sendUserEmail('excludeEmail', $email_params, $email_replacements);

		//send admin notifications
		$mailer = new JMAIL();
		$mailer->setSender(array($mailfrom, $fromname));
		$mailer->addReplyTo(array($mailfrom));
		$mailer->addRecipient($mailfrom);
		$mailer->setSubject('Temporary Exclusion - ' . $user->username . ' (' . $user->id . ')');
		$mailer->setBody('User ' . $user->username . ' (' . $user->id . ') has requested self-exclusion. The exclusion will be lifted on ' . $exclusion_date, false);
		$mailer->IsHTML(false);
		$mailer->Send();

	}

	/**
	 * Method to display bet limits page
	 *
	 * @return void
	 */
	public function betlimits() {
		//init model
		$user_model	=& $this->getModel('topbettaUser', 'TopbettaUserModel');
		//get user
		$user = $user_model->getUser();

		//if there's a requested bet limit change, get the request date from audit table
		$requested_date = null;
		$user_audit_model	=& $this->getModel('userAudit', 'TopbettaUserModel');
		$requested_log		= $user_audit_model->getRecentUserAuditByUserIDAndFieldName($user->user_id, array('requested_bet_limit', 'bet_limit'));
		if (!is_null($requested_log) && $requested_log->field_name == 'requested_bet_limit') {
			$requested_date		= $requested_log->update_date;
		}

		$view =& $this->getView('betlimits', 'html');
		$view->assign('user', $user);
		$view->assign('requested_date', $requested_date);
		$view->display();
	}

	/**
	 * Method to save bet limits
	 *
	 * @return boolean
	 */
	public function betlimits_save() {
		//init models
		$user_model		=& $this->getModel('topbettaUser', 'TopbettaUserModel');
		$audit_model	=& $this->getModel('UserAudit', 'TopbettaUserModel');

		//get login user details
		$user		= $user_model->getUser();

		//init session
		$session =& JFactory::getSession();

		//set up redirect url
		$this->setRedirect('/user/account/betting-limits');

		//get post var
		$no_limit = JRequest::getBool('no_limit', true, 'post');

		//set what db field to update
		$field_to_update	= 'bet_limit';
		//init request cancel flag
		$request_cancelled	= false;
		//init err array
		$err = array();

		if ($no_limit) {
			//-1 stands for no limit
			$bet_limit = -1;
		} else {
			$bet_limit	= JRequest::getVar('bet_limit', null, 'post');

			//validations for bet limit
			if ($bet_limit != (string)($bet_limit * 1) || $bet_limit < 0) {
				JError::raiseWarning(0, JText::_('Invalid bet limit.'));
				return false;
			}

			if ($bet_limit > 10000) {
				JError::raiseWarning(0, JText::_('Please select "No Limit"'));
				return false;
			}

			//converted to cents based number
			$bet_limit = bcmul($bet_limit, 100);
		}

		//if users want to increase bet limit, we store the requested value to 'requested_bet_limit'
		if (($user->bet_limit != -1 && $bet_limit > $user->bet_limit) || ($bet_limit == -1 && $user->bet_limit != -1)) {
			$field_to_update = 'requested_bet_limit';
		}

		//when users already have a request of increasing bet, but come back to reduce the limit,
		//we need to cancel the previous request
		if($bet_limit != -1 && $bet_limit < $user->bet_limit && $user->requested_bet_limit != 0) {
			$request_cancelled = true;
		}

		//update user's bet limit
		if (!$user_model->update($field_to_update, $bet_limit)) {
			JError::raiseWarning(0, JText::_("Failed to update your bet limit! Please contact us."));
			return false;
		}

		//flag to indicate if request_bet_limit has changed
		$request_bet_limit_changed = false;
		//store the value to audit table if bet_limit /request_bet_limit is changed

		if ($user->{$field_to_update} != $bet_limit) {
			//add user audit record
			$params = array(
				'user_id'		=> $user->user_id,
				'admin_id'		=> -1,
				'field_name'	=> $field_to_update,
				'old_value'		=> $user->bet_limit,
				'new_value'		=> $bet_limit,
			);
			$audit_model->store($params);

			if ('requested_bet_limit' == $field_to_update) {
				$request_bet_limit_changed = true;
			}
		}

		//if request cancelled, need to set request_bet_limit to 0
		if ($request_cancelled) {
			if (!$user_model->update('requested_bet_limit', 0)) {
				JError::raiseWarning(0, JText::_("Failed to update requested bet limit! Please contact us."));
				return false;
			}
			//add user audit record
			$params = array(
				'user_id'		=> $user->user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'requested_bet_limit',
				'old_value'		=> $user->requested_bet_limit,
				'new_value'		=> 0,
			);
			$audit_model->store($params);
		}

		//get email params
		$usersConfig = &JComponentHelper::getParams( 'com_topbetta_user' );
		$mailfrom	= $usersConfig->get('mailFrom');
		$fromname	= $usersConfig->get('fromName');

		//set up return messages and send out notification emails
		if ('requested_bet_limit' == $field_to_update) {
			if ($request_bet_limit_changed) {
				$update_msg = JText::_('Your new bet limit will take effect in 7 days.');

				//send bet limit increase request to admin
				$mailer		= new JMAIL();
				$emailBody	= "User {$user->username} ({$user->user_id}) has requested to increase the bet limit";
				$emailBody	.= " from " . bcdiv($user->bet_limit, 100, 2);
				$emailBody	.= " to " . ($bet_limit == -1 ? 'no limit' : bcdiv($bet_limit, 100, 2));
				$emailBody	.= " on " . date('j/n/Y');
				$mailer->setSender(array($mailfrom, $fromname));
				$mailer->addReplyTo(array($mailfrom));
				$mailer->addRecipient($mailfrom);
				$mailer->setSubject('Requests to Increase Bet Limits');
				$mailer->setBody($emailBody);
				$mailer->IsHTML(false);
				$mailer->Send();
			} else {
				$update_msg = JText::_('Raising bet limit request is already sent.');
			}
		} else {
			$update_msg = JText::_('Bet limit updated');

			if ($request_cancelled) {
				//send cancel bet limit increase to admin
				$mailer		= new JMAIL();
				$emailBody	= "User {$user->username} ({$user->user_id}) has cancelled the request to increase bet limit";
				$emailBody	.= " from " .  bcdiv($user->bet_limit, 100, 2);
				$emailBody	.= " to " . ($user->requested_bet_limit == -1 ? 'no limit' : bcdiv($user->requested_bet_limit, 100, 2));
				$emailBody	.= " on " . date('j/n/Y');
				$mailer->setSender(array($mailfrom, $fromname));
				$mailer->addReplyTo(array($mailfrom));
				$mailer->addRecipient($mailfrom);
				$mailer->setSubject('Requests to Increase Bet Limits - Cancelled');
				$mailer->setBody($emailBody);
				$mailer->IsHTML(false);
				$mailer->Send();
			}
		}
		$this->setMessage($update_msg);
		return true;
	}

	/**
	 * Method to resend verifcation
	 *
	 * @return void
	 */
	function resend_verification()
	{
		$model =& $this->getModel( 'topbettaUser');
		$email = rawurldecode(rawurldecode($_GET['email']));
		
		$existingUser = $model->isExisting('email', $email);
		if ($existingUser && $existingUser->block && $existingUser->activation) {
			$user =& JFactory::getUser( $existingUser->username );

			$this->_sendMail( $user, false );
			$this->setMessage(JText::_('Validation email has been sent.') );
		}

		$this->setRedirect( "/user/register" );
	}
	
	public function resendverification()
	{
		$this->resend_verification();	
	}
	
	/**
	 * Method to validate name
	 *
	 * @string $name
	 * @array $err
	 * @return void
	 */
	 
	private function _validate_name($name, $str, &$err)
	{		
		if ('' == $name) {
			$err[$str.'_name'] = "Please enter a $str name.";
		} else if (strlen($name) < 3) {
			$err[$str.'_name'] = ucfirst($str) . ' name must contain at least 3 characters.';
		} else if (strlen($name) > 50) {
			$err[$str.'_name'] = 'Maximum length is 50.';
		} 
	}
	
	/**
	 * Method to generate and validate username
	 *
	 * @string $firstname
	 * @string $lastname
	 * @return string
	 */
	 
	private function _generate_username($firstname, $lastname, $user_model)
	{
		if($firstname=="" || $lastname=="" ) {
 
			$username = "TB".rand(000000,999999);
			if ($user_model->isExisting('username', $username)) {
				$this->_generate_username($firstname, $lastname, $user_model);
			} else {
			   return $username;
			}

		}else{
           
			$username = strtolower(substr($firstname, 0, 3)).strtolower(substr($lastname, 0, 3));
			
			if ($user_model->isExisting('username', $username)) {
				$username = $username.rand(00,99);
				while($user_model->isExisting('username', $username)) {
                   
                     $username = $username.rand(00,99);
				}
				return $username;
			} else {
			   return $username;
			}

		}
	}
	
	/**
	 * Method to validate username
	 *
	 * @string $username
	 * @array $err
	 * @return void
	 */
	private function _validate_username($username, $user_model, &$err)
	{
		$usernameLength = strlen($username);
		if ('' == $username) {
			$err['username'] = 'Please enter a username';
		} else if (!preg_match('/^[a-zA-Z0-9]+$/i', $username)) {
			$err['username'] = 'Username only accepts letters and numbers';
		} else if ($usernameLength < 4) {
			$err['username'] = 'Username must contain at least 4 characters';
		} else if ($usernameLength > 30) {
			$err['username'] = 'Maximum length of username is 30';
		}
		else if ($user_model->isExisting('username', $username)) {
			$err['username'] = 'This username is already in use. Please select another one';
		}
	}

	/**
	 * Method to validate email
	 *
	 * @string $email
	 * @string $email2
	 * @array $err
	 * @return void
	 */
	private function _validate_email($email, $email2, $user_model, &$err)
	{
		if ('' == $email || !JMailHelper::isEmailAddress($email)) {
			$err['email'] = 'Please enter a valid email address';
		} else if (strlen($email) > 100) {
			$err['email'] = 'Maximum length of email is 100.';
		} else if ($existingUser = $user_model->isExisting('email', $email)) {
			$err['email']				= 'This email is already in use';
			$err['email_activation'] = ($existingUser->block && $existingUser->activation);
		}

		if (!isset($err['email']) && $email2 != $email ) {
			$err['email2'] = 'Please re-enter your email address correctly';
		}
	}

	/**
	 * Method to validate mobile number
	 *
	 * @string $mobile
	 * @array $err
	 * @return void
	 */
	private function _validate_mobile($mobile, &$err)
	{
		if (!preg_match('/^[0-9\s\-]+$/i', $mobile) || strlen($mobile) > 15) {
			$err['mobile'] = 'Please enter a valid mobile number';
		} else if ( strlen(preg_replace('/\D/', '', $mobile)) != 10 ) {
			$err['mobile'] = 'Please enter 10 digit mobile number.';
		}
	}
	
	/**
	 * Method to validate promotion code
	 *
	 * @string $code
	 * @array $err
	 * @return void
	 */
	private function _validate_promotion($code, &$err)
	{
		$model =& $this->getModel( 'topbettaUser');
		$promotion = $model->getPromotion(trim(strtoupper($code)));
		$user = $model->getUser();
		
		if (!$promotion && !empty($code)) {
			$err['promo_code'] = 'Invalid promotion code';
		} elseif (!empty($code) && $user->promo_code && $promotion[0]->pro_code) {
			$err['promo_code'] = 'You have already used a promotion code';
		}
	}


	/**
	 * Method to validate password
	 *
	 * @string $password
	 * @string $password2
	 * @array $err
	 * @return void
	 */
	private function _validate_password($password, $password2, &$err)
	{
		$passwordLength		= strlen($password);
		$passwordLeftCount	= $passwordLength;

		//MC - pwd complexity reduced for TopBetta requirements 20-10-2012
		if ($passwordLength < 6) {
			$err['password'] = 'Password minimum length is 6.';
		} else if ($passwordLength > 12) {
			$err['password'] = 'Password maximum length is 12.';
		} else {
			if( !preg_match('([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])', $password) ) 
			{ 
			    $err['password'] = 'Password requires letters and minimum 1 number';
			}			
			
			/*MC - This has been simplified above
			$passwordTypeCount = 0;

			if (preg_match_all('/[A-Z]/', $password, $match)) {
				$passwordUpperCount = count($match[0]);
				$passwordLeftCount -= $passwordUpperCount;
				if ($passwordUpperCount > 0) {
					$passwordTypeCount++;
				}
			} else {
				$err['password'] = 'Not a valid password please re-enter.';
			}

			if (!isset($err['password'])) {
				$passwordLowerCount = 0;
				if (preg_match_all('/[a-z]/', $password, $match)) {
					$passwordLowerCount = count($match[0]);
					$passwordLeftCount -= $passwordLowerCount;
					
					if ($passwordLowerCount > 0) {
						$passwordTypeCount++;
					}					
				}

				$passwordDigitCount = 0;
				if (preg_match_all('/[0-9]/', $password, $match)) {
					$passwordDigitCount = count($match[0]);
					$passwordLeftCount -= $passwordDigitCount;

					if ($passwordDigitCount > 0) {
						$passwordTypeCount++;
					}
				}

				if ($passwordLeftCount > 0) {
					$passwordTypeCount++;
				}

				//MC - only need 1 rule to match now - number
				if ($passwordTypeCount < 1) {
					$err['password'] = 'Password requires a number';
				}				
			}
			*/
		}

		if (!isset($err['password']) && $password != $password2) {
			$err['password2'] = 'Please re-enter your password correctly.';
		}
	}
	
	private function _validate_country(&$error_list)
	{	
		$country_code 	= JRequest::getString('country', null, 'post');
		
		//geoip validate before anything
		try{
			$client_geoip = new ClientGeoIP($_SERVER['REMOTE_ADDR']);
			
			if(strtolower($country_code) != strtolower($client_geoip->getCountryCode())){
				$error_list['country'] = 'IP address doesn\'t match the selected country';
			}
		}
		catch(Exception $e){
			$error_list['country'] = 'There was a problem validating your IP address';
		}
				
		$user_country 	=& $this->getModel('UserCountry');
		
		if(!is_null($user_country)){
			$mobile			= JRequest::getString('mobile', null, 'post');
			$phone			= JRequest::getString('phone', null, 'post');
			$postcode		= JRequest::getString('postcode', null, 'post');
			
			$country = $user_country->getUserCountryByCode($country_code);
			
			$not_required = array('phone');
			$number_type_list = array('mobile', 'phone');
			$validator_list = array('mobile', 'postcode', 'phone');
			
			foreach ($validator_list as $validation_type){
				
				$value = trim(${$validation_type});
				if(in_array($validation_type, $not_required) && empty($value)){
					continue;
				}
				
				$validation_regex = $country->{$validation_type . '_validation'};
				if (!preg_match('/' . $validation_regex .'/', $value)){
					$description = in_array($validation_type, $number_type_list) ? $validation_type . ' number' : $validation_type;
					$error_list[$validation_type] = 'Not a valid '. $description . ' please re-enter';
				}
			}
		}
	}
}
