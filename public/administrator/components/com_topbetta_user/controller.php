<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: controller.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * payment Controller
 *
 * @package Joomla
 * @subpackage payment
 */
class TopbettaUserController extends JController
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct() {
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'default');
		}
		$this->item_type = 'Default';

		parent::__construct();
	}

	/**
	 * Method to diplay the form
	 *
	 * @return Boolean true on success
	 */
	function display()
	{
		JToolBarHelper::title( JText::_( 'Topbetta Users' ), 'generic.png' );
		JToolBarHelper::preferences('com_topbetta_user', '350');

		$view = JRequest::getVar( 'view', 'default');
		$layout = JRequest::getVar( 'layout', 'default' );

		$view =& $this->getView( $view, 'html');

		$model =& $this->getModel( 'topbettauser' );
		$model->loadDynamicOptions();

		$view->setModel( $model, true );
		$view->setLayout( $layout );

		global $mainframe;
		// Prepare list array
		$lists = array();
		// Get the user state
		$filter_order = $mainframe->getUserStateFromRequest($option.'filter_order','filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir','filter_order_Dir', 'ASC');
		$filter_search = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_search', 'filter_topbettauser_search');
		$filter_state = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_state', 'filter_topbettauser_state');
		$filter_heard_about = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_heard_about', 'filter_topbettauser_heard_about');
		$filter_status = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_status', 'filter_topbettauser_status');
		$filter_marketing = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_status', 'filter_topbettauser_marketing');
		$filter_registration_from_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_registration_from_date', 'filter_topbettauser_registration_from_date');
		$filter_registration_to_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_registration_to_date', 'filter_topbettauser_registration_to_date');
		$filter_login_from_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_login_from_date', 'filter_topbettauser_login_from_date');
		$filter_login_to_date = $mainframe->getUserStateFromRequest($option.'filter_topbettauser_login_to_date', 'filter_topbettauser_login_to_date');


		// Build the list array for use in the layout
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['search'] = $filter_search;
		$lists['state'] = $filter_state;
		$lists['heard_about'] = $filter_heard_about;
		$lists['status'] = $filter_status;
		$lists['marketing'] = $filter_marketing;
		$lists['registration_from_date'] = $filter_registration_from_date;
		$lists['registration_to_date'] = $filter_registration_to_date;
		$lists['login_from_date'] = $filter_login_from_date;
		$lists['login_to_date'] = $filter_login_to_date;

		$users =& $model->getUsers();

		$page =& $model->getPagination();
		// Assign references for the layout to use
		$view->assignRef('lists', $lists);
		$view->assignRef('users', $users);
		$view->assignRef('page', $page);
		$view->assignRef('total_account_balance', $model->getUserBalance( 'account'));
		$view->assignRef('total_tournament_balance', $model->getUserBalance( 'tournament'));

		$view->assign('options', $model->options);

		$view->display();
	}

	/**
	 * Method to edit topbetta user
	 *
	 * @return Boolean true on success
	 */
	function edit()
	{
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel', 'Close');

		$view = JRequest::getVar( 'view', 'default');
		$layout = JRequest::getVar( 'layout', 'edit' );
		$userId = JRequest::getVar( 'user_id' );
		JRequest::setVar('hidemainmenu', 1);

		$view =& $this->getView( $view, 'html');
		$model =& $this->getModel( 'topbettauser');
		$model->loadDynamicOptions();

		$user = $model->getUser( $userId );
		$account_balance = $model->getUserBalance( 'account', $userId );
		$tournament_balance = $model->getUserBalance( 'tournament', $userId );

		if( !$user )
		{
			//user does not exist, redirect to the list page
			$redirectTo = 'index.php?option=com_topbetta_user';
			$this->setRedirect( $redirectTo );
			return false;
		}

		$formData = array(
	      'user_id' => $user->user_id,
	      'username' => $user->username,
	      'title' => $user->title,
	      'first_name' => $user->first_name,
	      'last_name' => $user->last_name,
	      'dob_day' => $user->dob_day,
	      'dob_month' => $user->dob_month,
	      'dob_year' => $user->dob_year,
	      'mobile' => $user->msisdn,
	      'phone' => $user->phone_number,
	      'email' => $user->email,
	      'street' => $user->street,
	      'city' => $user->city,
	      'state' => $user->state,
	      'country' => $user->country,
	      'postcode' => $user->postcode,
	      'heard_about' => $user->heard_about,
	      'heard_about_info' => $user->heard_about_info,
	      'marketing' => $user->marketing_opt_in_flag,
	      'status' => $user->block ? 'inactive' : 'active',
	      'identity_verified_flag' => $user->identity_verified_flag,
	      'identity_doc' => $user->identity_doc,
	      'identity_doc_id' => $user->identity_doc_id,
	      'jackpot_reminder' => $user->email_jackpot_reminder_flag,
	      'bsb_number' => $user->bsb_number,
	      'bank_account_number' => $user->bank_account_number,
	      'account_name' => $user->account_name,
	      'bank_name' => $user->bank_name,
	      'source' => $user->source,
	      'self_exclusion_date' => $user->self_exclusion_date,
	      'bet_limit' => $user->bet_limit == -1 ? '' : bcdiv($user->bet_limit, 100, 2),
	      'no_limit' => $user->bet_limit == -1,
		);

		$session =& JFactory::getSession();
		if( $sessFormData = $session->get('sessFormData', null, 'topbettauser') )
		{
			if( $sessFormErrors = $session->get('sessFormErrors', null, 'topbettauser') )
			{
				$view->assign( 'formErrors', $sessFormErrors);
				$session->clear('sessFormErrors', 'topbettauser');
			}
			foreach($sessFormData as $k => $data) {
				$formData[$k] = stripslashes($data);
			}
			foreach (array('jackpot_reminder', 'marketing', 'identity_verified_flag', 'no_limit') as $k) {
				$formData[$k] = isset($sessFormData[$k]);
			}
			
			$session->clear('sessFormData', 'topbettauser');
		}
		
		//store user audit info
		$path = JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models';
		$this->addModelPath($path);
		$audit_model =& $this->getModel('UserAudit', 'TopbettaUserModel');
		$bet_limit_request_list = $audit_model->getUserAuditListByUserIDAndFieldName($userId, array('bet_limit', 'requested_bet_limit'));

		$view->setModel($model, true);
		$view->setLayout($layout);
		
		$user_country_model =& $this->getModel('UserCountry');
		$country_list = $user_country_model->getUserCountryList();
		$view->assign('country_list', $country_list);

		// Assign references for the layout to use
		$view->assignRef('user', $user);
		$view->assignRef('options', $model->options);
		$view->assignRef('formData', $formData);
		$view->assign('account_balance', $account_balance);
		$view->assign('tournament_balance', $tournament_balance);
		$view->assign('bet_limit_request_list', $bet_limit_request_list);
		$view->display();

		return true;
	}

	/**
	 * Method to save a user record
	 *
	 * @return boolean true on success
	 */
	function save()
	{
		$model =& $this->getModel( 'topbettauser' );
		$model->loadDynamicOptions();
		$session =& JFactory::getSession();

		// Get user registration details from post.
		$userId = JRequest::getInt('user_id', null, 'post');
		$user =& JFactory::getUser($userId);
		//redirect to user list if the user doesn't exist
		if( $user->id == 0 )
		{
			$this->setRedirect( 'index.php?option=com_topbetta_user', 'There were some errors processing this form. See messages below.', 'error' );
			return false;
		}

		$failedRedirectTo = 'index.php?option=com_topbetta_user&task=edit&user_id=' . $userId;
		$successRedirectTo = 'index.php?option=com_topbetta_user';

		$username = JRequest::getString('username', null, 'post');
		$title = JRequest::getString('title', null, 'post');
		$first_name = JRequest::getString('first_name', null, 'post');
		$last_name = JRequest::getString('last_name', null, 'post');
		$dob_day = JRequest::getInt('dob_day', null, 'post');
		$dob_month = JRequest::getInt('dob_month', null, 'post');
		$dob_year = JRequest::getInt('dob_year', null, 'post');
		$mobile = JRequest::getString('mobile', null, 'post');
		$phone = JRequest::getString('phone', null, 'post');
		$email = JRequest::getString('email', null, 'post');
		$email2 = JRequest::getString('email2', null, 'post');
		$change_password = JRequest::getBool('change_password', null, 'post');
		$password = JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
		$street = JRequest::getString('street', null, 'post');
		$city = JRequest::getString('city', null, 'post');
		$state = JRequest::getString('state', null, 'post');
		$country = JRequest::getString('country', null, 'post');
		$postcode = JRequest::getInt('postcode', null, 'post');
		$promo_code = JRequest::getString('promo_code', null, 'post');
		$heard_about = JRequest::getString('heard_about', null, 'post');
		$heard_about_info = JRequest::getString('heard_about_info', null, 'post');
		$marketing = JRequest::getBool('marketing', false, 'post');
		$status = JRequest::getString('status', null, 'post');
		$email_jackpot_reminder_flag = JRequest::getString('jackpot_reminder', null, 'post');

		// identity and bank details
		$identity_verified_flag = JRequest::getVar('identity_verified_flag', 0, 'post');
		$identity_doc			= JRequest::getVar('identity_doc', null, 'post');
		$identity_doc_id		= JRequest::getVar('identity_doc_id', null, 'post');
		$bsb_number = JRequest::getString('bsb_number', null, 'post');
		$bank_account_number = JRequest::getString('bank_account_number', null, 'post');
		$account_name = JRequest::getString('account_name', null, 'post');
		$bank_name = JRequest::getString('bank_name', null, 'post');
		$source = JRequest::getString('source', null, 'post');
		
		$self_exclusion_date = JRequest::getString('self_exclusion_date', null, 'post');
		$no_limit = JRequest::getBool('no_limit', false, 'post');
		$bet_limit = JRequest::getString('bet_limit', null, 'post');

		//do validations
		$err = array();

		$usernameLength = strlen($username);
		if( '' == $username )
		{
			$err['username'] = 'Please enter a username.';
		}
		else if (!preg_match('/^[a-zA-Z0-9]+$/i', $username))
		{
			$err['username'] = 'Only accept letters and numbers.';
		}
		else if( $usernameLength < 4  )
		{
			$err['username'] = 'Username must contain at least 4 characters.';
		}
		else if( $usernameLength > 30 )
		{
			$err['username'] = 'Maximum length is 30.';
		}
		else if( $model->isExisting('username', $username, $userId) )
		{
			$err['username'] = 'This username is already in use. Please select another one.';
		}

		if( '' == $title )
		{
			$err['title'] = 'Please select a title.';
		}
		else if( !isset($model->options['title'][$title]))
		{
			$err['title'] = 'Invalid option.';
		}

		$firstnameLength = strlen($first_name);
		if( '' == $first_name )
		{
			$err['first_name'] = 'Please enter a first name.';
		}
		else if( $firstnameLength < 3 )
		{
			$err['first_name'] = 'First name must contain at least 3 characters.';
		}
		else if( $firstnameLength > 50 )
		{
			$err['first_name'] = 'Maximum length is 50.';
		}

		$lastnameLength = strlen($last_name);
		if( '' == $last_name )
		{
			$err['last_name'] = 'Please enter a last name.';
		}
		else if( $lastnameLength < 3 )
		{
			$err['last_name'] = 'Last name must contain at least 3 characters.';
		}
		else if( $lastnameLength > 50 )
		{
			$err['last_name'] = 'Maximum length is 50.';
		}

		if( '' == $dob_day || '' == $dob_month || '' == $dob_year )
		{
			$err['dob'] = 'Please select the date you were born.';
		}
		else if( !checkdate($dob_month, $dob_day, $dob_year))
		{
			$err['dob'] = 'Invalid date';
		}
		else
		{
			$age = date('Y') - $dob_year;
			if( date('md') < ($dob_month . sprintf('%02s', $dob_day)) )
			{
				$age--;
			}

			if( $age < 18 )
			{
				$err['dob'] = 'Only people over 18 can register.';
			}
		}

		if( '' == $email || !JMailHelper::isEmailAddress($email))
		{
			$err['email'] = 'Please enter a valid email address.';
		}
		else if( strlen($email) > 100 )
		{
			$err['email'] = 'Maximum length is 100.';
		}
		else if( $model->isExisting('email', $email, $userId) )
		{
			$err['email'] = 'This email is already in use.';
		}

		if( $change_password )
		{
			$passwordLeftCount = $passwordLength = strlen($password);

			if( $passwordLength < 8 )
			{
				$err['password'] = 'Minimum length is 8.';
			}
			else if( $passwordLength > 12 )
			{
				$err['password'] = 'Maximum length is 12.';
			}
			else
			{
				$passwordTypeCount = 0;
				if(preg_match_all('/[A-Z]/', $password, $match))
				{
					$passwordUpperCount = count($match[0]);
					$passwordLeftCount -= $passwordUpperCount;
					if( $passwordUpperCount > 0 )
					{
						$passwordTypeCount++;
					}
				}
				else
				{
					$err['password'] = 'Not a valid password please re-enter.';
				}

				if(!isset($err['password']))
				{
					$passwordLowerCount = 0;
					if(preg_match_all('/[a-z]/', $password, $match))
					{
						$passwordLowerCount = count($match[0]);
						$passwordLeftCount -= $passwordLowerCount;
					}

					$passwordDigitCount = 0;
					if(preg_match_all('/[0-9]/', $password, $match))
					{
						$passwordDigitCount = count($match[0]);
						$passwordLeftCount -= $passwordDigitCount;

						if( $passwordDigitCount > 0 )
						{
							$passwordTypeCount++;
						}
					}

					if( $passwordLeftCount > 0 )
					{
						$passwordTypeCount++;
					}

					if( $passwordTypeCount < 2 )
					{
						$err['password'] = 'Not a valid password please re-enter.';
					}
				}

				if( !isset($err['password']) && $password != $password2 )
				{
					$err['password2'] = 'Please re-enter your password correctly.';
				}
			}
		}

		if( !$change_password && ($password != '' || $password2 != '' ))
		{
			$err['change_password'] = 'Please tick the box to change password.';
		}
		if( '' == $street )
		{
			$err['street'] = 'Please enter your street address.';
		}
		else if( strlen($street) > 100 )
		{
			$err['street'] = 'Street address is too long.';
		}

		if( '' == $city )
		{
			$err['city'] = 'Please enter the suburb/city you live in.';
		}
		else if( strlen($city) > 50 )
		{
			$err['city'] = 'City name is too long.';
		}

		if( '' == $state )
		{
			$err['state'] = 'Please select the state you live in.';
		}
		else if( !isset($model->options['state'][$state]))
		{
			$err['state'] = 'Invalid option.';
		}

		if( $promo_code && !ctype_digit($promo_code))
		{
			$err['promo_code'] = 'Not a valid promotional code.';
		}

		if( '' != $heard_about && !isset($model->options['heard_about'][$heard_about]))
		{
			$err['heard_about'] = 'Invalid option';
		}

		if( '' != $heard_about && '' == $heard_about_info && in_array($heard_about, array('Friend', 'Advertisement', 'Internet', 'Promotion', 'Other')))
		{
			$err['heard_about_info'] = 'Please provide additional information.';
		}
		
		if( '' == $status )
		{
			$err['status'] = 'Please select an option';
		}
		else if( !isset($model->options['status'][$status]))
		{
			$err['status'] = 'Invalid option.';
		}
		
		if( $self_exclusion_date != '' && !preg_match('/^([0-9]{4})-([0-1][0-9])-([0-3][0-9])\s([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/', $self_exclusion_date))
		{
			$err['self_exclusion_date'] = 'Invalid date.';
		}
		
		if( $bet_limit != '' && $no_limit)
		{
			$err['bet_limit'] = 'Please remove the value in "Bet Limit" to update bet limit to no limit';
		}
		else if( !$no_limit && $bet_limit != (string)($bet_limit * 1) || $bet_limit < 0 )
		{
			$err['bet_limit'] = 'Invalid value.';
		}

		if( count($err) >  0 )
		{
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $failedRedirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}

		if( $change_password )
		{
			$user->setParam('password', $password );
		}

		$user_data_before_save = $model->getUser($userId);
		$data = array(
	      'username' => $username,
	      'password' => $password,
	      'password2' => $password2,
	      'email' => $email,
	      'name' => $first_name . ' ' . $last_name,
	      'block' => $status == 'active' ? 0 : 1,
		);
		if( !$user->bind($data) )
		{
			$this->setRedirect( $failedRedirectTo, 'Failed to bind data!', 'error' );

			return false;
		}
		if( !$user->save(true) )
		{
			$this->setRedirect( $failedRedirectTo, 'Failed to store data!', 'error' );

			return false;
		}

		$bet_limit = bcmul($bet_limit, 100);
		$params = array(
	        'user_id' => $userId,
	        'title' => $title,
	        'first_name' => $first_name,
	        'last_name' => $last_name,
	        'street' => $street,
	        'city' => $city,
	        'state' => $state,
	        'country' => $country,
	        'postcode' => $postcode,
	        'dob_day' => $dob_day,
	        'msisdn' => $mobile,
	        'phone_number' => $phone,
	        'dob_month' => $dob_month,
	        'dob_year' => $dob_year,
	        'promo_code' => $promo_code,
	        'heard_about' => $heard_about,
	        'heard_about_info' => $heard_about_info,
	        'marketing_opt_in_flag' => $marketing ? 1 : 0,
	        'identity_verified_flag' => $identity_verified_flag,
	        'identity_doc' => $identity_doc,
	        'identity_doc_id' => $identity_doc_id,
	        'bsb_number' => $bsb_number,
	        'bank_account_number' => $bank_account_number,
			'account_name' => $account_name,
			'bank_name' => $bank_name,
			'email_jackpot_reminder_flag' => $email_jackpot_reminder_flag,
			'source' => $source,
			'self_exclusion_date' => empty($self_exclusion_date) ? null : $self_exclusion_date,
			'bet_limit' => $no_limit ? -1 : $bet_limit,
			'requested_bet_limit' => $user_data_before_save->bet_limit == $bet_limit ? $user_data_before_save->requested_bet_limit : 0,
		);
		
		if (!$model->store( $params ))
		{
			//TO DO: send web alert email to tech
			$this->setRedirect( $failedRedirectTo, 'Update Failed. Please contact webmaster.', 'error' );

			return false;
		}
		
		//store user audit info
		$path = JPATH_SITE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models';
		$this->addModelPath($path);
		$audit_model =& $this->getModel('UserAudit', 'TopbettaUserModel');
		
		$admin_user = JFactory::getUser();
		$user_params = array_merge($params, $data);
		
		foreach ($user_params as $field => $value) {
			if ($field == 'password') {
				continue;
			}
			
			if ($field == 'password2' && !empty($value)) {
				$audit_params = array(
					'user_id'		=> $user_data_before_save->user_id,
					'admin_id'		=> $admin_user->id,
					'field_name'	=> 'password',
					'old_value'		=> '*',
					'new_value'		=> '*',
				);
				$audit_model->store($audit_params);
				continue;
			}
			
			if ($value != $user_data_before_save->{$field}) {
				$audit_params = array(
					'user_id'		=> $user_data_before_save->user_id,
					'admin_id'		=> $admin_user->id,
					'field_name'	=> $field,
					'old_value'		=> (string)$user_data_before_save->{$field},
					'new_value'		=> (string)$value,
				);
				$audit_model->store($audit_params);
			}
		}

		$this->setRedirect( $successRedirectTo, 'User Updated' );

		return true;
	}

	/**
	 * Method to cancel
	 *
	 * @return void
	 */
	function cancel()
	{
		$redirectTo = 'index.php?option='
		.JRequest::getVar('option');
		$this->setRedirect( $redirectTo );
	}

	/**
	 * Method to export users in csv
	 *
	 * @return void
	 */
	function csv_export()
	{
		$model =& $this->getModel( 'topbettauser' );
		TopbettaUserHelper::exportUserCsv($model->getUsers(true));
		exit;
	}
}
?>