<?php
/**
 * @version		$Id: user.php  Michael Costa $
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');
jimport( 'joomla.environment.request' );
jimport( 'joomla.user.user' );
jimport('joomla.user.helper');

class Api_User extends JController {

	function Api_User() {

	}

	public function getLoginHash() {
		// fetch the joomla login hash required to process the login
		$login_hash = JUtility::getToken();
		if ($login_hash) {
			$result = OutputHelper::json(200, array('login_hash' => $login_hash));
		} else {
			$result = OutputHelper::json(500, array('error_msg' => 'Problem getting login hash'));
		}
		return $result;
	}

	public function getUserDetails($iframe = FALSE) {
		// Joomla userid is being passed from Laravel
		// this fixes Joomla forgetting who is logged in :-)
		$l_user_id = JRequest::getVar('l_user_id', NULL);

		if ($l_user_id) {
			$user = & JFactory::getUser($l_user_id);
		} else {
			$user = & JFactory::getUser();
		}
		
		if (!$user->guest) {

			$component_list = array('topbetta_user');
			foreach ($component_list as $component) {
				$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
				$this -> addModelPath($path);
			}

			$name = $first_name = $last_name = ''; $tb_user = false;

			$name = $user->name;
			if($name)
			{
				$name = explode(' ', $name);
				$first_name = $name[0];
				if (isset($name[1])) $last_name = $name[1];
			}

			if (!class_exists('TopbettaUserModelTopbettaUser')) {
			JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
			}

			$tb_model = new TopbettaUserModelTopbettaUser();
			if($tb_model->isTopbettaUser($user->id) ) $tb_user = true;

			$tbuser = $tb_model->getUser($user->id);

			if ($iframe) {
			$result = array('status' => 200, 'first_name'=> $first_name,
													'last_name' => $last_name,
													'username' 	=> $user->username,
													'email' 	=> $user->email,
													'mobile' 	=> $tbuser->msisdn,
													'block' 	=> $user->block,
													'tb_user' 	=> $tb_user);
			} else {
			$result = OutputHelper::json(200, array('id'		=> (int)$user->id,
													'first_name'=> $first_name,
													'last_name' => $last_name,
													'username' 	=> $user->username,
													'email' 	=> $user->email,
													'mobile' 	=> $tbuser->msisdn,
													'block' 	=> $user->block,
													'tb_user' 	=> $tb_user));
			}
		} else {
			if ($iframe) {
				$result = array('status' => 500, 'error_msg' => 'Please login to get user details');
			} else {
				$result = OutputHelper::json(500, array('error_msg' => 'Please login to get user details'));
			}
		}
		return $result;
	}

	/*
	 * Handles the user login via a remote login form
	 */
	public function doUserLogin() {

		global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {
			//token validates - good to go
			$credentials = array();
			$credentials['username'] = JRequest::getVar('username', NULL);
			$credentials['password'] = JRequest::getVar('password', NULL);

			//$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
			//$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

			//$return_path = JRequest::getVar('return_path', NULL);

			// validate the user with joomla
			if ($credentials['username'] && $credentials['password']) {
				global $mainframe;

				$options = array();
				$options['remember'] = 1; // JRequest::getBool('remember', false);
				//$options['return'] = $return;

				//preform the login action
				$error = $mainframe -> login($credentials, $options);

				if (!JError::isError($error)) {
					// To determine the user type
					$user =& JFactory::getUser();
					if($user->id != 0){
						if( $user->isCorporate && !$user->isTopBetta ){
						   $account_type = 'corporate';
						}elseif( $user->isTopBetta && !$user->isCorporate ){
						   $account_type = 'topbetta';
						   $full_account = true;
						}else{
						   $account_type = 'basic';
						   $full_account = false;
						}

						if (!class_exists('TopbettaUserModelTopbettaUser')) {
							JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
						}

						$tb_model = new TopbettaUserModelTopbettaUser();
						$tbuser = $tb_model->getUser();

						$result = OutputHelper::json(200, array('msg' => 'Login successful','userInfo' => array('id' => (int)$user->id, 'username' => $user->username , 'name' => $user->name, 'first_name' => $tbuser->first_name, 'last_name' => $tbuser->last_name, 'accountType' => $account_type, 'full_account' => $full_account ) ));
					}else{

                         $result = OutputHelper::json(500, array('error_msg' => 'Account not activated or blocked' ));
					}

				} else {
					$result = OutputHelper::json(500, array('error_msg' => $error -> message));
				}

			} else {
				// we need both username and password
				$result = OutputHelper::json(500, array('error_msg' => 'Invalid username or password'));
			}
		} else {
			//  invalid login hash
			$result = OutputHelper::json(500, array('error_msg' => 'There was a problem with your login'));
		}

		return $result;

	}

    /**
	 * Method to Register a basic user account
	 *
	 * @params POST data
	 * @return string
	 */
	public function doUserRegisterBasic() {

        global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {

			       // Get required system objects
					$user 		= clone(JFactory::getUser());
					$pathway 	=& $mainframe->getPathway();
					$config		=& JFactory::getConfig();
					$authorize	=& JFactory::getACL();
					$document   =& JFactory::getDocument();

					require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
		            $model = new TopbettaUserModelTopbettaUser();
                    $model->loadDynamicOptions();
					$session =& JFactory::getSession();

					// If user registration is not allowed, show 403 not authorized.
					$usersConfig = &JComponentHelper::getParams( 'com_users' );
					if ($usersConfig->get('allowUserRegistration') == '0') {

						return OutputHelper::json(500, array('error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) ));
					}



					// Get user registration details from post.
					$username	= JRequest::getString('username', null, 'post');
					$first_name	= JRequest::getString('first_name', null, 'post');
					$last_name	= JRequest::getString('last_name', null, 'post');
					$email		= JRequest::getString('email', null, 'post');
					$email2		= JRequest::getString('email', null, 'post');
					$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
                    $password2	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
					$mobile		= JRequest::getString('mobile', null, 'post');
					$source		= JRequest::getString('source', null, 'post');
					$optbox		= JRequest::getVar('optbox', null, 'post');
					$btag		= JRequest::getString('btag', 'kx8FbVSXTgEWqcfzuvZcQGNd7ZgqdRLk', 'post');
					//$source		= ($source) ? $source : htmlspecialchars($_SERVER['HTTP_REFERER']);

					//do validations
					$err = array();

					if ($username) {
						$this->_validate_username($username, $model, $err);
					}
					$this->_validate_firstname($first_name, $err);
					$this->_validate_lastname($last_name, $err);
					$this->_validate_email($email, $email2, $model, $err);
					$this->_validate_password($password, $password2, $err);
					if(!empty($mobile)) $this->_validate_mobile($mobile,$err);


					$err_mag = '<br>';
					if (count($err) >  0) {
						//attempt to quickly pretty up the error messages
						//$err = str_ireplace('array', '', print_r($err, TRUE));
						foreach ($err as $er) $err_mag .= $er . '<br>';
						return OutputHelper::json(500, array('error_msg' => 'There were some errors processing this form.' ,
                                                       'errors' => $err_mag ,
							                           'data' => $_GET
						                              ));

					}

                    // $username = $this->_generate_username($first_name,$last_name, $model);
                    if (!$username) {
	                    $username = $this->_generate_username($first_name,$last_name, $model);
                    }

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

						return OutputHelper::json(500, array('error_msg' => JError::raiseError( 500, $user->getError()) ));
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
						// JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN);
						return OutputHelper::json(500, array('error_msg' => $user->getError() ));
					}

				    // Send registration confirmation mail
		            $this->_sendMail($user);

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
						  'btag'					=> $btag
					);

					if (!$model->store($params)) {
						//
					}

                    // Everything went fine, set relevant message depending upon user activation state and display message
					if ($useractivation == 1) {

						return OutputHelper::json(200, array('success' => JText::_( 'Your TopBetta account has been created. Please check your email to activate your account.') ));

					} else {

						return OutputHelper::json(200, array('success' => JText::_( 'Your TopBetta account has been created.<br>You can now login with <br>username: <b>'.$username.'</b>.' ), 'username' => $username ));
					}




		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}



    }


	/*
	 * Handles the user login via a remote login form - External
	 */
	public function doUserLoginExternal($iframe = FALSE, $login_details = FALSE) {

		global $mainframe;

		//Set key and secret
		$token_key		= JRequest::getString('tb_key',null,'post');
		$token_secret	= JRequest::getString('tb_secret',null,'post');
		$token = $this->get_external_website_key_secret($token_key,$token_secret);


		// first validate key and secret
		if ($token || $iframe) {
			//token validates - good to go
			$credentials = array();
			if ($login_details) {
				$credentials['username'] = $login_details['username'];
				$credentials['password'] = $login_details['password'];
			} else {
				$credentials['username'] = JRequest::getVar('username', NULL);
				$credentials['password'] = urldecode(JRequest::getVar('password', NULL));
			}


			// validate the user with joomla
			if ($credentials['username'] && $credentials['password']) {
				global $mainframe;

				$options = array();
				$options['remember'] = JRequest::getBool('remember', false);
				//$options['return'] = $return;

				//preform the login action
				$error = $mainframe -> login($credentials, $options);

				if (!JError::isError($error)) {
					// To determine the user type
					$user =& JFactory::getUser();
					if($user->id != 0){
						if( $user->isCorporate && !$user->isTopBetta ){
						   $account_type = 'corporate';
						}elseif( $user->isTopBetta && !$user->isCorporate ){
						   $account_type = 'topbetta';
						}else{
						   $account_type = 'basic';
						}

						if ($iframe) {
							$result = array('status' => 200, 'success' => 'Login successful','userInfo' => array('username' => $user->username , 'name' => $user->name, 'email' => $user->email, 'accountType' => $account_type ) );
						} else {
							$result = OutputHelper::json(200, array('success' => 'Login successful','userInfo' => array('username' => $user->username , 'name' => $user->name, 'email' => $user->email, 'accountType' => $account_type ) ));
						}
					}else{
						if ($iframe) {
							$result = array('status' => 500, 'error_msg' => 'Account not activated or blocked' );
						} else {
	                         $result = OutputHelper::json(500, array('error_msg' => 'Account not activated or blocked' ));
						}
					}

				} else {
					if ($iframe) {
						$result = array('status' => 500, 'error_msg' => $error -> message);
					} else {
						$result = OutputHelper::json(500, array('error_msg' => $error -> message));
					}
				}

			} else {
				// we need both username and password
				if ($iframe) {
					$result = array('status' => 500, 'error_msg' => 'Invalid username or password');
				} else {
					$result = OutputHelper::json(500, array('error_msg' => 'Invalid username or password'));
				}
			}
		} else {
			//  invalid login hash
			if ($iframe) {
				$result = array('status' => 500, 'error_msg' => 'There was a problem with your login. Not a valid key or secret');
			} else {
				$result = OutputHelper::json(500, array('error_msg' => 'There was a problem with your login. Not a valid key or secret'));
			}
		}

		return $result;

	}

    /**
	 * Method to Register a basic user account - External
	 *
	 * @params POST data
	 * @return string
	 */
	public function doUserRegisterBasicExternal($iframe = FALSE) {

        global $mainframe;


		//Set key and secret
		$token_key		= JRequest::getString('tb_key',null,'post');
		$token_secret	= JRequest::getString('tb_secret',null,'post');
		$token = $this->get_external_website_key_secret($token_key,$token_secret);


		// first validate key and secret
		if ($token || $iframe) {

			       // Get required system objects
					$user 		= clone(JFactory::getUser());
					$pathway 	=& $mainframe->getPathway();
					$config		=& JFactory::getConfig();
					$authorize	=& JFactory::getACL();
					$document   =& JFactory::getDocument();

					require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
		            $model = new TopbettaUserModelTopbettaUser();
                    $model->loadDynamicOptions();
					$session =& JFactory::getSession();

					// If user registration is not allowed, show 403 not authorized.
					$usersConfig = &JComponentHelper::getParams( 'com_users' );
					if ($usersConfig->get('allowUserRegistration') == '0') {
						if ($iframe) {
							return array('status' => 500, 'error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) );
						} else {
							return OutputHelper::json(500, array('error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) ));
						}
					}



					// Get user registration details from post.

					$username	= JRequest::getString('username', null, 'post');
					$first_name	= JRequest::getString('first_name', null, 'post');
					$last_name	= JRequest::getString('last_name', null, 'post');
					$email		= JRequest::getString('email', null, 'post');
					$email2		= JRequest::getString('email', null, 'post');
					$password	= urldecode(JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW));
                    $password2	= urldecode(JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW));
					$mobile		= JRequest::getString('mobile', null, 'post');
					$source		= $token['source'];
					$slug 		= JRequest::getString('slug', null, 'post');
					$btag 		= JRequest::getString('btag', null, 'post');
					$whitelabel = JRequest::getString('whitelabel', null, 'post');
					$optbox		= JRequest::getVar('optbox', null, 'post');
					if ($iframe) {
						$terms		= JRequest::getBool('terms', false, 'post');
					}

					//setup or source for toptippa
					if ($slug && !$btag) {
						$source = $source . '-' . substr($slug, 0, 50);
					}else if($slug && $btag) {
						$source = $source . '-' . $btag;
					}

					//remove some (+) that sometimes get through
					$first_name = str_replace('+', '', $first_name);
					$last_name = str_replace('+', '', $last_name);

					//do validations
					$err = array();

					if ($username) {
						$this->_validate_username($username, $model, $err);
					}
					$this->_validate_firstname($first_name, $err);
					$this->_validate_lastname($last_name, $err);
					$this->_validate_email($email, $email2, $model, $err);
					$this->_validate_password($password, $password2, $err);
					if(!empty($mobile)) $this->_validate_mobile($mobile,$err);
					if ($iframe) {
						if (!$terms) {
							$err['terms'] = 'Please select terms and Conditions.';
						}
					}


					$err_mag = '<br>';
					if (count($err) >  0) {
						foreach ($err as $er) $err_mag .= $er . '<br>';
						if ($iframe) {
							return array('status' => 500, 'error_msg' => $err_mag);
						} else {
							return OutputHelper::json(500, array('error_msg' => $err_mag));
						}

					}

                    if (!$username) {
	                    $username = $this->_generate_username($first_name,$last_name, $model);
                    }

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
						if ($iframe) {
							return array('status' => 500, 'error_msg' => JError::raiseError( 500, $user->getError()) );
						} else {
							return OutputHelper::json(500, array('error_msg' => JError::raiseError( 500, $user->getError()) ));
						}
					}

					// Set some initial user values
					$user->set('id', 0);
					$user->set('usertype', $newUsertype);
					$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
					$date =& JFactory::getDate();
					$user->set('registerDate', $date->toMySQL());
					$user->set('isCorporate',0);
					$user->set('isTopBetta',0);
					$user->set('entriesToFbWall',0);
					$user->set('freeWinsToFbWall',0);
					$user->set('paidWinsToFbWall',0);
					$user->set('betWinsToFbWall',0);

					// If user activation is turned on, we need to set the activation information
					$useractivation = $usersConfig->get( 'useractivation' );
					if ($useractivation == '1') {
						jimport('joomla.user.helper');
						$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
						$user->set('block', '1');
					}


					// If there was an error with registration, set the message and display form
					if (!$user->save()) {
						// JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN);
						if ($iframe) {
							return array('status' => 500, 'error_msg' => $user->getError() );
						} else {
							return OutputHelper::json(500, array('error_msg' => $user->getError() ));
						}
					}

				    // Send registration confirmation mail
		            if ($whitelabel && $slug) {
		            	$this->_sendTopTippaMail($user, TRUE, $whitelabel, $slug);
		            } else {
			            $this->_sendMail($user);
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
						  'btag'					=> $btag,
						  'marketing_opt_in_flag'	=> $optbox ? 1 : 0,
					);

					if (!$model->store($params)) {
						//
					}

                    // Everything went fine, set relevant message depending upon user activation state and display message
					if ($useractivation == 1) {
						if ($iframe) {
							return array('status' => 200, 'success' => JText::_( 'Your account has been created. Please check your email to activate your account.') );
						} else {
							return OutputHelper::json(200, array('success' => JText::_( 'Your account has been created. Please check your email to activate your account.') ));
						}

					} else {
						if ($iframe) {
							return array('status' => 200, 'success' => JText::_( 'Your account has been created.' ), 'username' => $username );
						} else {
							return OutputHelper::json(200, array('success' => JText::_( 'Your account has been created.' ), 'username' => $username ));
						}
					}


		}else{
				if ($iframe) {
					return array('status' => 500, 'error_msg' => JText::_( 'There was a problem with your registration. Not a valid key or secret' ) );
				} else {
			       return OutputHelper::json(500, array('error_msg' => JText::_( 'There was a problem with your registration. Not a valid key or secret' ) ));
				}
		}



    }


     /**
	 * Method to Register a topbetta user account
	 *
	 * @params POST data
	 * @return string
	 */
	public function doUserRegisterTopBetta() {
        global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {

			      // Get required system objects
					$user 		= clone(JFactory::getUser());
					$pathway 	=& $mainframe->getPathway();
					$config		=& JFactory::getConfig();
					$authorize	=& JFactory::getACL();
					$document   =& JFactory::getDocument();

                    require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
					$model = new TopbettaUserModelTopbettaUser();
					$model->loadDynamicOptions();
					$session =& JFactory::getSession();

					// If user registration is not allowed, show 403 not authorized.
					$usersConfig = &JComponentHelper::getParams( 'com_users' );
					if ($usersConfig->get('allowUserRegistration') == '0') {
						return OutputHelper::json(500, array('error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) ));
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
					$email2		= $email; //JRequest::getString('email2', null, 'post');
					//$mask		= JRequest::getBool('mask', false, 'post');

					//if ($mask) {
					//	$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
					//	$password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
					//} else {
					//	$password	= JRequest::getString('passwordTxt', null, 'post', JREQUEST_ALLOWRAW);
					//	$password2	= JRequest::getString('password2Txt', null, 'post', JREQUEST_ALLOWRAW);
					//}

					$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
					$password2	= $password; //JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);

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
					$optbox				= JRequest::getVar('optbox', null, 'post');
					$privacy			= JRequest::getBool('privacy', false, 'post');
					$terms				= JRequest::getBool('terms', false, 'post');
					$source				= JRequest::getString('source', null, 'post');
					//$source				= ($source) ? $source : htmlspecialchars($_SERVER['HTTP_REFERER']);


					//do validations
					$err = array();

					$this->_validate_username($username, $model, $err);
					$this->_validate_email($email, $email2, $model, $err);
					$this->_validate_password($password, $password2, $err);
					if(!empty($mobile)) $this->_validate_mobile($mobile,$err);

					//TODO:
					//$this->_validate_country($err);


					if('' == $title) {
						$err['title'] = 'Please select a title.';
					} else if( !isset($model->options['title'][$title])) {
						$err['title'] = 'Please select a title.';
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
						$err['state'] = 'Please select the state you live in.';
					} else if(!isset($model->options['state'][$state])) {
						$err['state'] = 'Please select the state you live in.';
					}

					if($promo_code) {
						$this->_validate_promotion($promo_code, $err);
					}

					if ('' != $heard_about && !isset($model->options['heard_about'][$heard_about])) {
						//$err['heard_about'] = 'Please select that How did you hear about us?';
					}

					if ('' != $heard_about && '' == $heard_about_info && in_array($heard_about, array('Friend', 'Advertisement', 'Internet', 'Promotion', 'Other'))) {
						//$err['heard_about_info'] = 'Please select that How did you hear about us?';
					}

					if (!$privacy) {
						$err['privacy'] = 'Please select privacy policy.';
					}

					if (!$terms) {
						$err['terms'] = 'Please select terms and Conditions.';
					}


					$err_mag = '<br>';
					if (count($err) >  0) {
						foreach ($err as $er) $err_mag .= $er . '<br>';
						return OutputHelper::json(500, array('error_msg' => 'There were some errors processing this form. See messages below.<br>' . $err_mag ,

							                           'data' => $_GET
						                              ));
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
                    $user->set('isTopBetta', 1 );

					// If user activation is turned on, we need to set the activation information
					$useractivation = $usersConfig->get( 'useractivation' );
					if ($useractivation == '1') {
						jimport('joomla.user.helper');
						$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
						$user->set('block', '1');
					}

					// If there was an error with registration, set the message and display form
					if (!$user->save()) {
						// JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN);
						return OutputHelper::json(500, array('error_msg' => $user->getError() ));
					}

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
					  'source'					=> $source,
					  'btag'					=> $btag_cookie,
					);

					if (!$model->store($params)) {

						return OutputHelper::json(500, array('error_msg' => 'Update to TopBettaUser Failed. Please contact webmaster.' ));

					}


					require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'userpreregistration.php');
					$pre_registration_model	=new TopbettaUserModelUserPreRegistration();
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

						return OutputHelper::json(200, array('sucess' => JText::_( 'REG_COMPLETE_ACTIVATE') ));

					} else {

						return OutputHelper::json(200, array('sucess' => JText::_( 'REG_COMPLETE' ), 'username' => $username ));
					}




		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}



    }

	/**
	 * Method to Upgrade a Basic user account to topbetta user account
	 *
	 * @params POST data
	 * @return string
	 */
	public function doUserUpgradeTopBetta() {

        global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {


			      // Get required system objects
					$user 		= clone(JFactory::getUser());
					$pathway 	=& $mainframe->getPathway();
					$config		=& JFactory::getConfig();
					$authorize	=& JFactory::getACL();
					$document   =& JFactory::getDocument();

                    require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
					$model = new TopbettaUserModelTopbettaUser();
					$model->loadDynamicOptions();
					$session =& JFactory::getSession();



					// Get user registration details from post.
					$username	= JRequest::getString('username', null, 'post');
					$title		= JRequest::getString('title', null, 'post');
					$first_name	= JRequest::getString('first_name', null, 'post');
					$last_name	= JRequest::getString('last_name', null, 'post');
					$dob_day	= JRequest::getInt('dob_day', null, 'post');
					$dob_month	= JRequest::getInt('dob_month', null, 'post');
					$dob_year	= JRequest::getInt('dob_year', null, 'post');

					$email		= JRequest::getString('email', null, 'post');


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
					$optbox				= JRequest::getVar('optbox', null, 'post');
					$privacy			= JRequest::getBool('privacy', false, 'post');
					$terms				= JRequest::getBool('terms', false, 'post');
					//$source				= JRequest::getString('source', null, 'post');


					$user =& JFactory::getUser($username);
					if($user == false) {

                        return OutputHelper::json(500, array('error_msg' => 'Please login to upgrade your account' ));

                    }


					// UserId of the logged-in user
					$user_id = $user->id;

                    if( $user->email != $email  ) {

                        return OutputHelper::json(500, array('error_msg' => 'Email entered doesn\'t match your registered email' ));

                    }


					//do validations
					$err = array();

					//TODO:
					//$this->_validate_country($err);


					if('' == $title) {
						$err['title'] = 'Please select a title.';
					} else if( !isset($model->options['title'][$title])) {
						$err['title'] = 'Please select a title.';
					}


					if('' == $dob_day || '' == $dob_month || '' == $dob_year) {
						$err['dob'] = 'Please select the date you were born.';
					} else if (!checkdate($dob_month, $dob_day, $dob_year)) {
						$err['dob'] = 'Invalid DOB date';
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
					} else if(strtolower($country) !== 'au' && $state !== 'other'){
						$err['state'] = 'Please select the state you live in.';
					} else if(!isset($model->options['state'][$state])) {
						$err['state'] = 'Please select the state you live in.';
					}

					if($promo_code) {
						$this->_validate_promotion($promo_code, $err);
					}

					if ('' != $heard_about && !isset($model->options['heard_about'][$heard_about])) {
						//$err['heard_about'] = 'Please select that How did you hear about us?';
					}

					if ('' != $heard_about && '' == $heard_about_info && in_array($heard_about, array('Friend', 'Advertisement', 'Internet', 'Promotion', 'Other'))) {
						//$err['heard_about_info'] = 'Please select that How did you hear about us?';
					}

					if (!$privacy) {
						$err['privacy'] = 'Please select privacy policy.';
					}

					if (!$terms) {
						$err['terms'] = 'Please select terms and Conditions.';
					}


					$err_mag = '<br>';
					if (count($err) >  0) {
						foreach ($err as $er) $err_mag .= $er . '<br>';
						return OutputHelper::json(500, array('error_msg' => 'There were some errors processing this form. See messages below.<br>' . $err_mag ,
                                                       'errors' => $err_mag ,
							                           'data' => $_POST
						                              ));
					}

					// Put data in required fields
					//$fullName	= $first_name.' '.$last_name;
					//$homeNumber = $acode.$phone;
					$fullDOB	= $dob_year.'-'.$dob_month.'-'.$dob_day;

					// Setup user details array
					$userDetails = array(
						'DateOfBirth'	=> "$fullDOB"
					);


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
					  'promo_code'				=> $promo_code,
					  'heard_about'				=> $heard_about,
					  'heard_about_info'		=> $heard_about_info,
					  'marketing_opt_in_flag'	=> $optbox ? 1 : 0

					);

					if (!$model->userUpgradeTopBetta($user_id)) {
						//if (!$model->store($params) || !$model->userUpgradeTopBetta($user_id)) {
						//}
						return OutputHelper::json(500, array('error_msg' => 'Update to TopBettaUser Failed. Please contact webmaster.' ));
						return false;
					}elseif(!$model->updateUser($params)) {
						return OutputHelper::json(500, array('error_msg' => 'Update to TopBettaUser Failed. Please contact webmaster.' ));
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


					require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'userpreregistration.php');
					$pre_registration_model	=new TopbettaUserModelUserPreRegistration();
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

					// Everything went fine, set relevant message depending upon user activation state and display message

					return OutputHelper::json(200, array('sucess' => "Account upgraded successfully" ));





		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}



    }

	/**
	 * Method to Register Corporate User account
	 *
	 * @params POST data
	 * @return string
	 */
	public function doUserRegisterCorporate() {

        global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {


				   // Get required system objects
					$user 		= clone(JFactory::getUser());
					$pathway 	=& $mainframe->getPathway();
					$config		=& JFactory::getConfig();
					$authorize	=& JFactory::getACL();
					$document   =& JFactory::getDocument();

					require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
		            $model = new TopbettaUserModelTopbettaUser();
                    $model->loadDynamicOptions();
					$session =& JFactory::getSession();

					// If user registration is not allowed, show 403 not authorized.
					$usersConfig = &JComponentHelper::getParams( 'com_users' );
					if ($usersConfig->get('allowUserRegistration') == '0') {

						return OutputHelper::json(500, array('error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) ));
					}



					// Get user registration details from post.

					$first_name	= JRequest::getString('first_name', null, 'post');
					$last_name	= JRequest::getString('last_name', null, 'post');
					$email		= JRequest::getString('email', null, 'post');
					$email2		= JRequest::getString('email2', null, 'post');
					$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
                    $password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);
					$url		= JRequest::getString('url', null, 'post');
					$corporate_name		= JRequest::getString('corporate_name', null, 'post');
                    $logo = (array_key_exists('logo', $_FILES)) ? $_FILES['logo'] : FALSE;

					//do validations
					$err = array();



					$this->_validate_email($email, $email2, $model, $err);
					$this->_validate_password($password, $password2, $err);
					$this->_validate_url($url, $err);
					if(is_uploaded_file($logo['tmp_name'])){
					 $this->_upload_logo($logo, $model, $err);
                    }

                    $corporatenameLength = strlen($corporate_name);
					if ('' == $corporate_name) {
						$err['corporate_name'] = 'Please enter name.';
					} else if ($corporatenameLength < 2) {
						$err['corporate_name'] = 'Name must contain at least 2 characters.';
					} else if ($corporatenameLength > 50) {
						$err['corporate_name'] = 'Maximum length is 50.';
					}

					$err_mag .= $er . '<br>';
					if (count($err) >  0) {
						//attempt to quickly pretty up the error messages
						//$err = str_ireplace('array', '', print_r($err, TRUE));
						foreach ($err as $er) $err_mag .= $er . '<br>';
						return OutputHelper::json(500, array('error_msg' => 'There were some errors processing this form.' . $err_mag ,
                                                       'errors' => $err_mag ,
							                           'data' => $_GET
						                              ));

					}

                    $username = $this->_generate_username($first_name,$last_name, $model);

                    // If first name or last name is empty , default name is set to 'Corporate User'
					if ('' == $first_name && '' == $last_name ){

						$fullName	= 'Corporate User';

					} else {

					    $fullName	= $first_name.' '.$last_name;
					}

                    // Put data in required fields
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

						return OutputHelper::json(500, array('error_msg' => JError::raiseError( 500, $user->getError()) ));
					}

					// Set some initial user values
					$user->set('id', 0);
					$user->set('usertype', $newUsertype);
					$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
					$date =& JFactory::getDate();
					$user->set('registerDate', $date->toMySQL());
					// To Specify the account is corporate account
					$user->set('isCorporate', 1 );

					// If user activation is turned on, we need to set the activation information
					$useractivation = $usersConfig->get( 'useractivation' );
					if ($useractivation == '1') {
						jimport('joomla.user.helper');
						$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
						$user->set('block', '1');
					}


					// If there was an error with registration, set the message and display form
					if (!$user->save()) {

						// JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN);
						return OutputHelper::json(500, array('error_msg' => $user->getError() ));
					}

					// get the userid
					$user_id = $user->get('id');

					// Create User Extension table record for new user.
					$params = array(
					  'user_id'					=> $user_id,
					  'corporate_name'			=> $corporate_name,
					  'url'					    => $url,
					  'logo'                    => $logo['name']
					);

					if (!$model->storeCorporate($params)) {

						return OutputHelper::json(500, array('error_msg' => 'Update to Corporate Failed. Please contact webmaster.' ));

					}

				    // Send registration confirmation mail
		            $this->_sendMail($user);

                    // Everything went fine, set relevant message depending upon user activation state and display message
					if ($useractivation == 1) {

						return OutputHelper::json(200, array('success' => JText::_( 'Your TopBetta corporate account has been created. Please check your email to activate your account.') ));

					} else {

						return OutputHelper::json(200, array('success' => JText::_( 'Your TopBetta corporate account has been created.' ) ));
					}




		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}



    }

	/**
	 * generateJoomlaPassword
	 *
	 * This is used for laravel to store a joomla based password :-)
	 */
	public function generateJoomlaPassword() {

        // first validate a legit token has been sent
		$server_token = JUtility::getToken();

		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {

			$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);

			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword($password, $salt);
			$joomla_password = $crypt.':'.$salt;

			return OutputHelper::json(200, array('joomla_password' => $joomla_password ));

		}else{

		    return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}

	}

	/**
	 * Password Reset Request Method
	 *
	 * @return void
	 */
	public function requestPasswordReset()
	{
		 // first validate a legit token has been sent
		$server_token = JUtility::getToken();

		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {
			// Get the input
			$email	= JRequest::getVar('email', null, 'post', 'string');
			$err	= array();
			if ('' == $email || !eregi("^[_a-z0-9-]+(\.[+_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
				 return OutputHelper::json(500, array('error_msg' => JText::_( 'Please enter a valid email address.' ) ));
			}

			// Get the model
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
		    $model = new TopbettaUserModelTopbettaUser();

			// Request a reset
			if ($model->requestReset($email) === false) {
				return OutputHelper::json(500, array('error_msg' => JText::sprintf('PASSWORD_RESET_REQUEST_FAILED', $model->getError())));
			}
			else {
				return OutputHelper::json(200, array('success' => JText::_( 'An e-mail has been sent to your e-mail address. Please follow the instructions in your email to reset your password.' ) ));
			}

		}else{

		      return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}
	}


	/**
	 * Password Reset Confirmation Method
	 *
	 * @access	public
	 */
	public function confirmPasswordReset()
	{
		// first validate a legit token has been sent
		$server_token = JUtility::getToken();

		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {

			// Get the input
			$token		= JRequest::getVar('token', null, 'post', 'alnum');
			$username	= JRequest::getVar('username', null, 'post');

			$err = array();
			if ('' == $token) {
				return OutputHelper::json(500, array('error_msg' => JText::_( 'Please enter the token which has been sent to you.' ) ));
			}

			if ('' == $username) {
				return OutputHelper::json(500, array('error_msg' => JText::_( 'Please enter your username.' ) ));
			}


			// Get the model
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
		    $model = new TopbettaUserModelTopbettaUser();

			// Verify the token
			if ($model->confirmReset($token) === false) {
				return OutputHelper::json(500, array('error_msg' => JText::sprintf('PASSWORD_RESET_CONFIRMATION_FAILED', $model->getError())));
			}
			else {
				return OutputHelper::json(200, array('success' => JText::_( 'Please enter and confirm your new password into the following fields.' ) ));
			}

		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}
	}

	/**
	 * Password Reset Completion Method
	 *
	 * @access	public
	 */
	public function completePasswordReset()
	{
		// first validate a legit token has been sent
		$server_token = JUtility::getToken();

		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {	// Check for request forgeries

			// Get the input
			$password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
			$password2	= JRequest::getString('password2', null, 'post', JREQUEST_ALLOWRAW);

			$err = array();

			$this->_validate_password($password, $password2, $err);

			$err_mag = '<br>';
			if (count($err) >  0) {
				foreach ($err as $er) $err_mag .= $er . '<br>';
				return OutputHelper::json(500, array('error_msg' => 'There were some errors processing this form.' . $err_mag ,
                                                      'errors' => $err_mag ,
							                           'data' => $_GET
					                              ));
					}


			// Get the model
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
		    $model = new TopbettaUserModelTopbettaUser();

			// User Id
			global $mainframe;
			$user_id = $mainframe->getUserState('topbettauser.reset.id');

			// Reset the password
			if ($model->completeReset($password, $password2) === false)
			{
				return OutputHelper::json(500, array('error_msg' => JText::sprintf('PASSWORD_RESET_FAILED', $model->getError()) ));
			}

			//log to user audit
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'useraudit.php');
			$audit_model = new TopbettaUserModelUserAudit();

			$params = array(
				'user_id'		=> $user_id,
				'admin_id'		=> -1,
				'field_name'	=> 'password',
				'old_value'		=> '*',
				'new_value'		=> '*',
			);

			$audit_model->store($params);

			return OutputHelper::json(200, array('success' => JText::_('PASSWORD_RESET_SUCCESS')));

		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}
	}




	/**
	 * Method to Upload Image
	 *
	 * @array $logo
	 * @array $err
	 * @return void
	 */

	private function _upload_logo($logo, $user_model, &$err)
	{


		if($existingLogo = $user_model->isExistingLogo($logo['name']) ) {
		   	$err['logo'] = 'Logo name already exists';
		} else {

		        $allowedExts = array('png','jpg','jpeg','gif');
				$extension = end(explode(".", $logo["name"]));



				if ((($logo["type"] == "image/gif") || ($logo["type"] == "image/jpeg") || ($logo["type"] == "image/jpg") || ($logo["type"] == "image/png"))
					&& ($logo["size"] < 20000)
					&& in_array($extension, $allowedExts))
					{
					if ($logo["error"] > 0)
						{
						   $err['logo'] = $post["error"] ;
						 }
					else
						{
							$filepath = JPATH_SITE.DS.'images/CorporateLogos/'.$logo["name"];

                            if(!move_uploaded_file($logo["tmp_name"],$filepath))
                              {
									$err['logo'] = "Error uploading your logo";
                               }



						 }
				  }
				else
				  {
					$err['logo'] = "Invalid extension and/or file size too large";
				  }
		}
	}

	/**
	 * Method to validate firtname
	 *
	 * @string $firstname
	 * @array $err
	 * @return void
	 */

	private function _validate_firstname($fname, &$err)
	{
		if ('' == $fname) {
			$err['first_name'] = 'Please enter a first name.';
		} else if (strlen($fname) < 3) {
			$err['first_name'] = 'First name must contain at least 3 characters.';
		} else if (strlen($fname) > 50) {
			$err['first_name'] = 'Maximum length is 50.';
		}
	}

	/**
	 * Method to validate lastname
	 *
	 * @string $lasttname
	 * @array $err
	 * @return void
	 */

	private function _validate_lastname($lname, &$err)
	{
		if ('' == $lname) {
			$err['last_name'] = 'Please enter a last name.';
		} else if (strlen($lname) < 3) {
			$err['last_name'] = 'Last name must contain at least 3 characters.';
		} else if (strlen($lname) > 50) {
			$err['last_name'] = 'Maximum length is 50.';
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
			//$err['email_activation'] = ($existingUser->block && $existingUser->activation);
		}

		if (!isset($err['email']) && $email2 != $email ) {
			$err['email2'] = 'Please re-enter your email address correctly';
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
		}

		if (!isset($err['password']) && $password != $password2) {
			$err['password2'] = 'Please re-enter your password correctly.';
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
	 * Method to validate url
	 *
	 * @string $url
	 * @array $err
	 * @return void
	 */
	private function _validate_url($url , &$err)
	{

		if ('' == $url) {
			$err['url'] = 'Please enter a url';
		} else if (!preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $url)) {
			$err['url'] = 'Invalid url';
		}
	}



	/**
	 * sendMail Method
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
		$siteURL		= str_replace('/api/','/',$siteURL);

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

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
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
					$admin_msg = "Hello %s,\n\nA new user has registered at %s.\nThis e-mail contains their details:\n\nName: %s\nE-mail: %s\nUsername: %s\n\nPlease do not respond to this message. It is automatically generated and is for information purposes only.";

					$message2 = sprintf ( $admin_msg, $row->name, $sitename, $name, $email, $username);
					$message2 = html_entity_decode($message2, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject, $message2);
				}
			}
		}
	}

	/**
	 * sendTopTippaMail Method
	 *
	 * @user user record
	 * @send_to_admin boolean
	 *
	 * @return void
	 */
	private function _sendTopTippaMail(&$user, $send_to_admin = true, $whitelabel, $slug )
	{

		global $mainframe;

		$db			=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		//$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$mailfrom 		= 'help@toptippa.com.au';
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();
		$siteURL		= str_replace('/api/','/',$siteURL);

		$subject 	= sprintf ( JText::_('Account details for'), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		$message = $this->_toptippaBody($name, $username, $whitelabel, $slug);

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

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
		$mailer			= new UserMAIL();

		$email_params	= array(
			'mailfrom'	=> 'help@toptippa.com.au',
			'fromname'	=> 'TopTippa Admin',
			'subject'	=> $subject,
			'mailto'	=> $email,
			'body' 		=> $message,
			'ishtml'	=> true,
		);
		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username,
			'activation_link'	=> $siteURL."user/activate/".$user->get('activation'),
		);
		$mailer->sendUserToptippaEmail('welcomeEmail', $email_params, $email_replacements);

		if ($send_to_admin) {
			// Send notification to all administrators
			// get superadministrators id
			foreach ($rows as $row) {
				if ($row->sendEmail) {
					$admin_msg = "Hello %s,\n\nA new user has registered at %s.\nThis e-mail contains their details:\n\nName: %s\nE-mail: %s\nUsername: %s\n\nPlease do not respond to this message. It is automatically generated and is for information purposes only.";

					$message2 = sprintf ( $admin_msg, $row->name, $sitename, $name, $email, $username);
					$message2 = html_entity_decode($message2, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject, $message2);
				}
			}
		}
	}

	private function _toptippaBody($fullname, $username, $whitelabel, $slug) {
		$body = "
		<html><body>
Dear $fullname ($username)<br>
<h3>Welcome to TopTippa!</h3>
Your TopTippa account has been created.<br>
You may login to the http://www.toptippa.com.au/$slug TopTippa website using the following username and the password you specified.<br><br>
<b>Username: $username</b><br>
<b>Password: (as entered on website)</b><br><br>
TopTippa tipping competitions have a twist – Each selection will be given points associated to the team based from their chance of winning. You receive the points associated with your selection if they are the winners of that game... Pick the outsider and move up the leader board...<br>
<br>
Check out the TopTippa rules here | Check out the TopTippa terms and Conditions here<br><br>
Need More Help?<br>
If you have a more specific question you can always contact our support staff who'll be more than happy to help.<br>
Phone: 1300 886 503	Email: help@toptippa.com.au<br>
Cheers,<br><br>
The TopTippa Team<br>
<hr>
<h3>Welcome to TopBetta!</h3>
You have also created a TopBetta “Basic” account – A Basic account only allows you enter all of the FREE promotional tournaments<br><br>
PLEASE NOTE: Your TopBetta basic account is NOT a wagering account. You will not be able to place wagers or enter paid tournaments to win CASH until you complete your full registration “Click here to UPGRADE your TopBetta account”
<br><br><b>TopBetta Introduction</b><br>
TopBetta is a leading Australian online race betting site offering a range of betting options on Horse, Greyhound and Harness Racing. We also offer Australia's only online Racing & Sports tournament betting. Plus all our members are eligible for special offers and competitions.
<br><br><b>New Member Welcome Offer</b><br>
As a new member you are entitled to a once only 100% deposit bonus. For example, deposit up to $100 and we'll match your deposit amount in Tournament Dollars*. The Welcome Offer is valid for 7 days only from today. Activate your account today and DEPOSIT!
<hr>
Need More Help?<br><br>
If you have a more specific question you can always contact our support staff who'll be more than happy to help.<br>
Phone: 1300 886 503	Email: help@topbetta.com<br><br>
Cheers,<br>
The TopTippa and TopBetta Team.<br>
<br>
Licensed and regulated in Australia. Copyright © 2013 TopBetta Pty Ltd. All rights reserved.<br>
Must be 18+<br>
</body></html>
		";

		return $body;
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

	public function doUserLogout() {
		global $mainframe;

		//preform the logout action
		$error = $mainframe -> logout();
		if (!JError::isError($error)) {
			$result = OutputHelper::json(200, array('msg' => 'You have been logged out'));
		} else {
			$result = OutputHelper::json(500, array('error_msg' => 'There was a problem trying to logout'));
		}

		return $result;
	}

	public function doFacebookLogin() {

		global $mainframe;
        // first validate a legit token has been sent
		$server_token = JUtility::getToken();



		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {
			//token validates - good to go

					// RECEIVE FACEBOOK AUTH DETAILS VIA API FROM MOBILE
					$fb_details = array();
					$fb_details['fb_id'] = JRequest::getVar('fb_id', NULL);
					$fb_details['first_name'] = JRequest::getVar('first_name', NULL);
					$fb_details['last_name'] = JRequest::getVar('last_name', NULL);
					$fb_details['email_address'] = JRequest::getVar('email_address', NULL);

					if (!$fb_details['fb_id']) {
						return OutputHelper::json(500, array('error_msg' => 'No facebook id sent!'));
					}

					require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
					require_once (JPATH_BASE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php');

					$userMapModel = new JFBConnectModelUserMap();

					$jUserId = $userMapModel -> getJoomlaUserId($fb_details['fb_id']);
					$msg = '';

					// ALREADY MAPPED IN FB USERMAP TABLE?
					if (!$jUserId) {
						// NO
						$jUserEmailId = $userMapModel -> getJoomlaUserIdFromEmail($fb_details['email_address']);

						// DOES THEIR FB EMAIL EXIST IN USER TABLE?
						if ($jUserEmailId && $jUserEmailId != 0) {
							// YES
							// MAKE SURE THIS EMAIL ISN'T ALRADY MAPPED
							$jUserId = $userMapModel -> getFacebookUserId($jUserEmailId);
							if (!$jUserId) {
								// MAP FB ACCOUNT TO JOOMLA ACCOUNT IN USERMAP TABLE

								if ($this -> mapUser($fb_details['fb_id'], $jUserEmailId)) {

									$user_details = &JFactory::getUser($jUserEmailId);

									JPluginHelper::importPlugin('user');
									$response->username = $user_details -> username ;

									$options = array();
									$result = $mainframe->triggerEvent('onLoginUser', array((array)$response, $options));

									$user_logged_in = &JFactory::getUser();

									if ( $user_logged_in -> id && $user_logged_in -> id != 0 ) {

										   return OutputHelper::json(200, array('msg' => array('msg' => 'Connected Facebook profile to TopBetta account' , 'email' => $user_logged_in -> email ) ));

									}else{

											return OutputHelper::json(500, array('error_msg' => JText::_( 'Login Failed.' ) ));

									}



								} else {
									return OutputHelper::json(500, array('error_msg' => 'Failed to connect your Facebook profile to TopBetta account'));
								}

								// Update the temp jId so that we login below
								$jUserId = $jUserEmailId;

							}
						} else {
							// NO
							// CREATE JOOMLA ACCOUNT AND MAP ACCOUNTS IN USERMAP TABLE
							//if ($this -> createFacebookOnlyUser($fbUserId))
							//$jUserId = $userMapModel -> getJoomlaUserId($fbUserId);

							// Get required system objects
							$user 		= clone(JFactory::getUser());
							$pathway 	=& $mainframe->getPathway();
							$config		=& JFactory::getConfig();
							$authorize	=& JFactory::getACL();
							$document   =& JFactory::getDocument();

							require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
							$model = new TopbettaUserModelTopbettaUser();
							$model->loadDynamicOptions();
							$session =& JFactory::getSession();

							// If user registration is not allowed, show 403 not authorized.
							$usersConfig = &JComponentHelper::getParams( 'com_users' );
							if ($usersConfig->get('allowUserRegistration') == '0') {

								return OutputHelper::json(500, array('error_msg' => JError::raiseError( 403, JText::_( 'Access Forbidden' )) ));
							}


							// Get user registration details from post.

							$first_name	= $fb_details['first_name'];
							$last_name	= $fb_details['last_name'];
							$email		= $fb_details['email_address'];

						    // Generates alphanumeric password of length 8
						    $pw = '';
							for($i=0; $i<8; $i++) {
								$pw .= chr(rand(65, 122));
							}

							$password	= $pw;

							$username = $this->_generate_username($first_name,$last_name, $model);

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

								return OutputHelper::json(500, array('error_msg' => JError::raiseError( 500, $user->getError()) ));
							}

							// Set some initial user values
							$user->set('id', 0);
							$user->set('usertype', $newUsertype);
							$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
							$date =& JFactory::getDate();
							$user->set('registerDate', $date->toMySQL());
							$user->set('activation', '' );


							// If there was an error with registration, set the message and display form
							if (!$user->save()) {
								// JText::_( $user->getError(). ' - Username:'.$user->get('username').' - Error Ref:'. $newUserPIN);
								return OutputHelper::json(500, array('error_msg' => $user->getError() ));
							}

							$credentials = array();
							$credentials['username'] = $username;
							$credentials['password'] = $password;
							$options = array();
							$options['remember'] = JRequest::getBool('remember', false);


							//preform the login action
							$error = $mainframe -> login($credentials, $options);

							$newUser = &JFactory::getUser();

							if (!$this -> mapUser($fb_details['fb_id'], $newUser->id )) {

								return OutputHelper::json(500, array('error_msg' => 'Mapping failed' ));
							}

							// Send registration confirmation mail
		                    $this->_sendMailFB($newUser , $password);

                           return OutputHelper::json(200, array('msg' => 'New user created , mapped and logged in' ));

						}
					}

					// YES - ALREADY MAPPED
					// LOGIN TO JOOMLA ACCOUNT

                    $user_details = &JFactory::getUser($jUserId);

					JPluginHelper::importPlugin('user');
					$response->username = $user_details -> username ;

					$options = array();
					$result = $mainframe->triggerEvent('onLoginUser', array((array)$response, $options));

					$user_logged_in = &JFactory::getUser();

					if ( $user_logged_in -> id && $user_logged_in -> id != 0 ) {

						   return OutputHelper::json(200, array('msg' => array('email' => $user_logged_in -> email ) ));

					}else{

							return OutputHelper::json(500, array('error_msg' => JText::_( 'Login Failed.' ) ));

					}



		}else{

		       return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}

	}

	/**
	 * sendMail Method (Facebook)
	 *
	 * @user user record
	 * @send_to_admin boolean
	 *
	 * @return void
	 */
	private function _sendMailFB(&$user, $password , $send_to_admin = true )
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


		$message = sprintf (JText::_( 'COM_JFBCONNECT_EMAIL_REGISTERED_BODY' ), $name, $sitename ,$siteURL, $username , $password);
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

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
		$mailer			= new UserMAIL();

		$email_params	= array(
			'subject'	=> $subject,
			'mailto'	=> $email,
			'ishtml'	=> true,
		);
		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username
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



	/*
	 * TODO: Remove this function - testing only
	 */
	public function checkLogin() {
		$user = &JFactory::getUser();

		header('Access-Control-Allow-Origin: http://m.topbetta.com');
		OutputHelper::_debug($user);
	}

	/*
	 * Maps a facebook user id with a joomla user id
	 * Should only need to be called first time for a user
	 */

	//TODO: fix the access_token - this will probably prevent a desktop login via facebook
	private function mapUser($fbUid, $jUserId = null) {

		if (!$jUserId || !$fbUid) {
			return false;
		}

		//NOTE: jfbconnect mapUser was borked. Doing it here for convenience.
		$db = &JFactory::getDBO();
		$query = "INSERT INTO #__jfbconnect_user_map (`id`, `j_user_id`, `fb_user_id`, `access_token`, `authorized`, `created_at`, `updated_at`)
						VALUES (NULL, " . $db -> quote($jUserId) . ",  " . $db -> quote($fbUid) . ", 'wdwdfw', '1', " . $db -> quote(JFactory::getDate() -> toMySQL()) . ",  " . $db -> quote(JFactory::getDate() -> toMySQL()) . ");";
		$db -> setQuery($query);

		return $db -> query();
	}

	//
	// PAYMENT ETC
	//

	function getBalances() {

		//Check the cookie to keep login the user
		global $mainframe;
		jimport('joomla.utilities.utility');
		$hash = JUtility::getHash('JLOGIN_REMEMBER');

		if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				jimport('joomla.utilities.simplecrypt');
				//Create the encryption key, apply extra hardening using the user agent string
				$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

				$crypt	= new JSimpleCrypt($key);
				$str	= $crypt->decrypt($str);

				$options = array();
				$options['silent'] = true;
					if (!$mainframe->login(@unserialize($str), $options)) {
							// Clear the remember me cookie
							setcookie( JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, '/' );
						}
			}

		$user = &JFactory::getUser();

		// Include the syndicate functions only once
		require_once (JPATH_BASE . DS . 'modules' . DS . 'mod_bslogin' . DS . 'helper.php');

		// Require wagering library
		jimport('mobileactive.wagering.bet');

        //get the tournament of the day id regardless if logged in
        if (!class_exists('TournamentModelTournament')) {
            JLoader::import('tournament', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
        }
		$tournament_model = JModel::getInstance('Tournament', 'TournamentModel');


		/* Tournament of the Day info */
		$tod_keyword = JRequest::getVar('tod_keyword','ALL');
        $tod = $tournament_model->isThereTournamentOfTheDay(date('Y-m-d'),$tod_keyword);
        $tod_id = (int)$tod[0]->id;
		if($tod_id==0) {
			$tod = $tournament_model->isThereTournamentOfTheDay(date('Y-m-d'));
			$tod_id = (int)$tod[0]->id;
		}

		//TODO: this should come from the database
		$tod_prize = false;
		$tod_prize_url = 'https://www.topbetta.com/images/murray_prize.png';
		/* End of Tournament of the Day info */

		$ticket_list = array();
		$funds = array();
		if (!$user -> guest) {

			if (!class_exists('TournamentModelTournamentTicket')) {
				JLoader::import('tournamentticket', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelTournamentRacing')) {
				JLoader::import('tournamentracing', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelTournamentSport')) {
				JLoader::import('tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelTournamentLeaderboard')) {
				JLoader::import('tournamentleaderboard', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelTournamentSportEvent')) {
				JLoader::import('tournamentsportevent', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelMeeting')) {
				JLoader::import('meeting', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('BettingModelBet')) {
				JLoader::import('bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			if (!class_exists('BettingModelBetSelection')) {
				JLoader::import('betselection', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			if (!class_exists('PaymentModelAccounttransaction')) {
				JLoader::import('accounttransaction', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
			}

			if (!class_exists('TournamentdollarsModelTournamenttransaction')) {
				JLoader::import('tournamenttransaction', JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models');
			}

			if (!class_exists('TournamentModelTournamentSportEvent')) {
				JLoader::import('race', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelRace')) {
				JLoader::import('race', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentModelPrivate')) {
				JLoader::import('tournamentprivate', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}

			if (!class_exists('TournamentdollarsModelTournamenttransaction')) {
			JLoader::import('tournamenttransaction', JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models');
			}
			$payment_dollars_model = JModel::getInstance('Accounttransaction', 'PaymentModel');

			if (!class_exists('PaymentModelAccounttransaction')) {
			JLoader::import('accounttransaction', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
			}
			$tournament_dollars_model = JModel::getInstance('Tournamenttransaction', 'TournamentdollarsModel');

			$ticket_model = JModel::getInstance('TournamentTicket', 'TournamentModel');

			$racing_model = JModel::getInstance('TournamentRacing', 'TournamentModel');
			$leaderboard_model = JModel::getInstance('TournamentLeaderboard', 'TournamentModel');

			$tickets_open = array();

			$open_ticket_list = $ticket_model -> getTournamentTicketActiveListByUserID($user -> id);

			$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
			$racing_sports = $tournament_sport_model -> excludeSports;

			$tournament_sport_event_model = JModel::getInstance('TournamentSportEvent', 'TournamentModel');

			//Tournament entered
			$tournaments_entered_ids = array();
			//Order tournaments as per the start date
			$tournaments_entered_sorted = $tournaments_entered_sorted_ids = array();
			$race_model = JModel::getInstance('Race', 'TournamentModel');

			foreach ($open_ticket_list as $ticket) {
				$tournament = $tournament_model -> getTournament($ticket -> tournament_id);
				$tournament_sport = $tournament_sport_model -> getTournamentSport($tournament -> tournament_sport_id);
				$bet_open = strtotime($tournament -> end_date) > time();
				$tournament_type = in_array($tournament_sport -> name, $racing_sports) ? 'racing' : 'sports';
				if ('sports' == $tournament_type && $bet_open) {
					$sport_tournament = $tournament_sport_event_model -> getTournamentSportEventByTournamentID($ticket -> tournament_id);
					$bet_open = strtotime($sport_tournament -> betting_closed_date) > time();
				}


				if($tournament_type == 'racing') // stop sports events untill it's ready
				{
					$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport -> name)));

					//get the current race time
					$tournament 	= $racing_model->getTournamentRacingByTournamentID($ticket -> tournament_id);
					$number4t = $race_model -> getNextRaceNumberByMeetingID($tournament -> meeting_id);
					if (is_null($number4t)) {
						$number4t = $race_model -> getLastRaceNumberByMeetingID($tournament -> meeting_id);
					}
					$current_race = $race_model->getRaceByMeetingIDAndNumberApi($tournament -> meeting_id,$number4t);

					//set the start date 2 days ahead if there is no races
					if(!$current_race) $current_race->start_date = time() + 48 * 60 * 60;

					$tournaments_entered_sorted[strtotime($current_race->start_date)] = $ticket -> tournament_id;

					//get number of entrants
					$tournament_entrants = $ticket_model->countTournamentEntrants($tournament->id);

					$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id] = array('ticket_id' => $ticket -> id, 'icon' => $icon_image, 'buy_in' => $ticket -> buy_in > 0 ? ('$' . number_format($ticket -> buy_in / 100, 2)) : 'Free', 'tournament_name' => $ticket -> tournament_name, 'togo' => $this->formatCounterText(strtotime($current_race->start_date)), 'bet_open_txt' => $tournament -> cancelled_flag ? 'Cancelled' : ($bet_open ? 'BETTING OPEN' : 'BETTING CLOSED'), 'bet_open_class' => ($bet_open && !$tournament -> cancelled_flag) ? 'betting-open' : 'betting-closed', 'qualified_txt' => $tournament -> cancelled_flag ? 'Cancelled' : 'Pending', 'qualified_class' => 'ticket-pending', 'leaderboard_rank' => 'N/A', 'betta_bucks' => '$' . number_format($ticket_model -> getAvailableTicketCurrency($ticket -> tournament_id, $user -> id) / 100, 2), 'tournament_type' => $tournament_type, 'tournament_entrants' => $tournament_entrants, 'tournament_id' => $ticket->tournament_id);

					$leaderboard = $leaderboard_model -> getLeaderBoardRankByUserAndTournament($user -> id, $tournament);

					if ($leaderboard && !$tournament -> cancelled_flag) {
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['qualified_txt'] = ($leaderboard -> qualified ? 'Qualified' : 'Pending');
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['qualified_class'] = ($leaderboard -> qualified ? 'ticket-qualified' : 'ticket-pending');
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['leaderboard_rank'] = ($leaderboard -> rank == '-' ? 'N/Q' : $leaderboard -> rank);
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['betta_bucks'] = '$' . number_format($ticket_model -> getAvailableTicketCurrency($tournament -> id, $user -> id) / 100, 2);
					}

                    if ($tournament->private_flag > 0) {

                        $private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
                        $private_tournament 		= $private_tournament_model->getTournamentPrivateByTournamentID($ticket -> tournament_id);
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['identifier'] = ($private_tournament->display_identifier) ? $private_tournament->display_identifier : false;
					} else {
						$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['identifier'] = false;
					}
					$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['private'] = $tournament->private_flag;

					//add some additional fileds
					$tournament_filds 	= $racing_model->getTournamentRacingByTournamentID($ticket->tournament_id);
					$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['meeting_name'] = $tournament_filds->meeting_name;
					$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['value'] = ($tournament_filds->buy_in > 0) ? Format::currency($tournament_filds->buy_in, true) . '/' . Format::currency($tournament_filds->minimum_prize_pool, true) : 'FREE' . '/' . Format::currency($tournament_filds->minimum_prize_pool, true);
					$tickets_open[strtotime($current_race->start_date)][$ticket -> tournament_id]['sport_name'] = $tournament_filds->sport_name;

					$tournaments_entered_ids[] = $ticket -> tournament_id;


				}
			}

			//sort the tournament by next race
			ksort($tickets_open);
			$tickets_open_sorted = array();
			$tickets_closed_sorted = array();
			foreach ($tickets_open as $time => $tickets) {
				foreach($tickets as $tun_id => $ticket)
				{
					if($ticket['bet_open_txt'] == 'BETTING OPEN') $tickets_open_sorted[$tun_id] = $ticket;
					else $tickets_closed_sorted[$tun_id] = $ticket;
				}
			}

			$ticket_button_class = (empty($tickets_open) ? ' class="inactive"' : '');

			$tickets_recent = array();
			$recent_ticket_list = $ticket_model -> getTournamentTicketRecentListByUserID($user -> id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');

			foreach ($recent_ticket_list as $ticket) {
				$tournament = $tournament_model -> getTournament($ticket -> tournament_id);
				$tournament_sport = $tournament_sport_model -> getTournamentSport($tournament -> tournament_sport_id);
				$bet_open = strtotime($tournament -> end_date) > time();
				$tournament_type = in_array($tournament_sport -> name, $racing_sports) ? 'racing' : 'sports';

				if($tournament_type == 'racing') // stop sports events untill it's ready
				{

					$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport -> name)));

					//get the last race time
					$tournament 	= $racing_model->getTournamentRacingByTournamentID($ticket -> tournament_id);
					$number4t = $race_model -> getLastRaceNumberByMeetingID($tournament -> meeting_id);
					$current_race = $race_model->getRaceByMeetingIDAndNumberApi($tournament -> meeting_id,$number4t);

					$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id] = array('ticket_id' => $ticket -> id, 'icon' => $icon_image, 'buy_in' => $ticket -> buy_in > 0 ? ('$' . number_format($ticket -> buy_in / 100, 2)) : 'Free', 'tournament_name' => $ticket -> tournament_name, 'tournament_id' => $ticket -> tournament_id, 'bet_open_txt' => $tournament -> cancelled_flag ? 'CANCELLED' : 'COMPLETED', 'bet_open_class' => 'betting-completed', 'qualified_txt' => 'All Paying', 'qualified_class' => 'ticket-qualified', 'leaderboard_rank' => 'N/A', 'tournament_type' => $tournament_type, );

					//add some additional fileds
					$tournament_filds 	= $racing_model->getTournamentRacingByTournamentID($ticket->tournament_id);
					$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['meeting_name'] = $tournament_filds->meeting_name;
					$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['value'] = ($tournament_filds->buy_in > 0) ? Format::currency($tournament_filds->buy_in, true) . '/' . Format::currency($tournament_filds->minimum_prize_pool, true) : 'FREE' . '/' . Format::currency($tournament_filds->minimum_prize_pool, true);
					$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['sport_name'] = $tournament_filds->sport_name;

                    if ($tournament->private_flag == 1) {

                        $private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
                        $private_tournament 		= $private_tournament_model->getTournamentPrivateByTournamentID($ticket->tournament_id);
						$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['identifier'] = ($private_tournament->display_identifier) ? $private_tournament->display_identifier : false;
					} else {
						$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['identifier'] = false;
					}

					$prize = 0;
					if (!$ticket -> cancelled_flag && $ticket -> result_transaction_id) {
						if ($ticket -> jackpot_flag) {
							//$transaction_record = $user -> tournament_dollars -> getTournamentTransaction($ticket -> result_transaction_id);
							$transaction_record = $tournament_dollars_model -> getTournamentTransaction($ticket -> result_transaction_id);
						} else {
							//$transaction_record = $user -> account_balance -> getAccountTransaction($ticket -> result_transaction_id);
							$transaction_record = $payment_dollars_model -> getAccountTransaction($ticket -> result_transaction_id);
						}

						if ($transaction_record && $transaction_record -> amount > 0) {
							$prize = $transaction_record -> amount;
						}
					}
					$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['prize'] = ('$' . number_format($prize / 100, 2));

					$leaderboard = $leaderboard_model -> getLeaderBoardRankByUserAndTournament($user -> id, $tournament);
					if ($leaderboard) {
						$tickets_recent[strtotime($current_race->start_date)][$ticket -> tournament_id]['leaderboard_rank'] = ($leaderboard -> rank == '-' ? 'N/Q' : $leaderboard -> rank);
					}
				}
			}

			//sort the tournament by last race
			krsort($tickets_recent);

			$tickets_recent_sorted = array();
			foreach ($tickets_recent as $time => $tickets) {
				foreach($tickets as $tun_id => $ticket) $tickets_recent_sorted[$tun_id] = $ticket;
			}

			$meeting_model = JModel::getInstance('Meeting', 'TournamentModel');
			$bet_model = JModel::getInstance('Bet', 'BettingModel');
			$unresulted_bet_list = $bet_model -> getActiveBetListByUserID($user -> id);
			$bets_unresulted = modbsLoginHelper::getBetDisplayListApi($unresulted_bet_list);

			$recent_bet_list = $bet_model -> getBetRecentListByUserID($user -> id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
			$bets_recent = modbsLoginHelper::getBetDisplayListApi($recent_bet_list, true);

			$bet_button_class = (empty($bets_unresulted) ? ' class="inactive"' : '');

			//account balances
			$payment_model = JModel::getInstance('Accounttransaction', 'PaymentModel');
			$amount = $payment_model -> getTotal($user -> id);
			if (!empty($amount)) {
				$amount = $amount / 100;
			}
			$funds['account_balance'] = '$ ' . number_format($amount, 2, '.', ',');

			$tournament_model = JModel::getInstance('Tournamenttransaction', 'TournamentdollarsModel');
			$tournament_amount = $tournament_model -> getTotal($user -> id);
			if (!empty($tournament_amount)) {
				$tournament_amount = $tournament_amount / 100;
			}
			$funds['tournament_dollars'] = '$ ' . number_format($tournament_amount, 2, '.', ',');

			if (!class_exists('TopbettaUserModelTopbettaUser')) {
			JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
			}

			//Get user status
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
			$tb_user = false;
			$tb_model = new TopbettaUserModelTopbettaUser();
			if($tb_model->isTopbettaUser($user->id) ) $tb_user = true;

			$result = OutputHelper::json(200, array('funds' => $funds, 'tickets_open' => $tickets_open_sorted, 'tickets_recent' => array('tickets_closed_sorted' => $tickets_closed_sorted, 'tickets_recent_sorted' => $tickets_recent_sorted), 'tournaments_entered_ids' => $tournaments_entered_ids, 'bets_unresulted' => $bets_unresulted, 'bets_recent' => $bets_recent, 'tb_user' => $tb_user, 'tod_id' => $tod_id, 'tod_prize' => $tod_prize, 'tod_prize_url' => $tod_prize_url));

		} else {
			$result = OutputHelper::json(500, array('error_msg' => 'Need to be logged in first.', 'tod_id' => $tod_id, 'tod_prize' => $tod_prize, 'tod_prize_url' => $tod_prize_url));
		}

		return $result;
	}


	public function getBettingHistory() {

		 // first validate a legit token has been sent
		$server_token = JUtility::getToken();

		if (JRequest::getVar($server_token, FALSE,'', 'alnum')) {

			if (!class_exists('BettingModelBet')) {
				JLoader::import('bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			if (!class_exists('BettingModelBetSelection')) {
				JLoader::import('betselection', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			if (!class_exists('BettingModelBetResultStatus')) {
				JLoader::import('BetResultStatus', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			if (!class_exists('BettingModelBetProduct')) {
				JLoader::import('BetProduct', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
			}

			// CONTROLLER CODE
			global $mainframe, $option;

			$bet_model					=& $this->getModel('Bet', 'BettingModel');
			$bet_selection_model		=& $this->getModel('BetSelection', 'BettingModel');
			$bet_result_status_model	=& $this->getModel('BetResultStatus', 'BettingModel');
			$bet_product_model			=& $this->getModel('BetProduct', 'BettingModel');
			$bet_origin_model			=& $this->getModel('BetOrigin', 'BettingModel');

			// Joomla userid is being passed from Laravel
			// this fixes Joomla forgetting who is logged in :-)
			$l_user_id = JRequest::getVar('l_user_id', NULL);

			if ($l_user_id) {
				$user =& JFactory::getUser($l_user_id);
			} else {
				$user =& JFactory::getUser();
			}
			// $user =& JFactory::getUser();

			if (!$user -> id) {

				return OutputHelper::json(500, array('error_msg' => 'Please login first'));

			}


			$result_type	= JRequest::getVar('result_type', null);

			$lists = array();

			$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_history_from_date', 'filter_history_from_date');
			$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_history_to_date', 'filter_history_to_date');

			$lists['from_date']	= $filter_from_date;
			$lists['to_date']	= $filter_to_date;

			$filter = array(
				'user_id'		=> $user->id,
				'result_type'	=> $result_type,
				'from_time'		=> $filter_from_date ? strtotime($filter_from_date) : null,
				'to_time'		=> $filter_to_date ? (strtotime($filter_to_date) + 24 * 60 * 60) : null,
			);

			$offset = $mainframe->getUserStateFromRequest(
				JRequest::getVar('limitstart', 0, '', 'int'),
				'limitstart',
				0
			);

			$limit = $mainframe->getCfg('list_limit');
			$bet_list = $bet_model->getBetFilterList($filter, 'b.id DESC', 'ASC', $limit, $offset);

			jimport('joomla.html.pagination');
			$total = $bet_model->getBetFilterCount($filter);
			$pagination = new JPagination($total, $offset, $limit);



			// VIEW.HTML.PHP
			$bet_display_list = array();

			$component_list = array('tournament', 'topbetta_user');
			foreach ($component_list as $component) {
				$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
				$this -> addModelPath($path);
			}

			$meeting_model = &$this -> getModel('Meeting', 'TournamentModel');
			$selection_result_model = &$this -> getModel('SelectionResult', 'TournamentModel');

			//$bet_selection_model	=& $this->getModel('BetSelection');
			//$selection_result_model	=& $this->getModel('SelectionResult');
			//$meeting_model			=& $this->getModel('Meeting');

			require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'helpers' . DS . 'helper.php');

			$wagering_bet = WageringBet::newBet();

			$i = 1;
			foreach ($bet_list as $bet) {
				$label		= BettingHelper::getBetTicketDisplay($bet->id);
				$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);

				$bet_display_list[$bet->id] = array(
					'link'			=> '/betting/racing/meeting/' . $meeting->id . '/' . $bet->event_number,
					'row_class'		=> $i % 2 == 0 ? 'odds' : 'even',
					'bet_time'		=> $bet->created_date,
					'label'			=> $label,
					'bet_type'		=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
					// 'amount'		=> FORMAT::currency($bet->bet_amount),
					'amount'		=> $bet->bet_amount,
					// 'bet_total'		=> FORMAT::currency(abs($bet->bet_total)),
					'bet_total'		=> abs($bet->bet_total),
					// 'bet_freebet_amount'		=> FORMAT::currency(abs($bet->bet_freebet_amount)),
					'bet_freebet_amount'		=> abs($bet->bet_freebet_amount),
					'dividend'		=> '&mdash;',
					'paid'			=> '&mdash;',
					'result'		=> 'CONFIRMED',
					'half_refund'	=> false
				);

				if ($bet->refunded_flag && !$bet->win_amount) {
					$bet_display_list[$bet->id]['result']	= 'REFUNDED';
					if ($bet->refund_amount > 0) {
						// $bet_display_list[$bet->id]['paid']	= Format::currency($bet->refund_amount);
						$bet_display_list[$bet->id]['paid']	= $bet->refund_amount;
					}

				}
				else if($bet->bet_result_status == 'pending')
				{
					$bet_display_list[$bet->id]['result']			= 'PENDING';
				}
				else if ($bet->resulted_flag && empty($bet->win_amount)) {
					$bet_display_list[$bet->id]['result']	= 'LOSS';
					$bet_display_list[$bet->id]['paid']		= 'NIL';
				} else if ($bet->resulted_flag) {
					$bet_display_list[$bet->id]['result']	= 'WIN';
					// $bet_display_list[$bet->id]['paid']		= Format::currency($bet->win_amount);
					$bet_display_list[$bet->id]['paid']		= $bet->win_amount;

					if ($wagering_bet->isStandardBetType($bet->bet_type)) {
						$selection_result	= $selection_result_model->getSelectionResultBySelectionID($bet->selection_id);
						$win_dividend		= $selection_result->win_dividend;
						$place_dividend		= $selection_result->place_dividend;

						switch ($bet->bet_type) {
							case WageringBet::BET_TYPE_WIN:
								// $bet_display_list[$bet->id]['dividend'] = Format::odds($win_dividend);
								$bet_display_list[$bet->id]['dividend'] = $win_dividend;
								break;
							case WageringBet::BET_TYPE_PLACE:
								// $bet_display_list[$bet->id]['dividend'] = Format::odds($place_dividend);
								$bet_display_list[$bet->id]['dividend'] = $place_dividend;
								break;
							case WageringBet::BET_TYPE_EACHWAY:
								// $bet_display_list[$bet->id]['dividend']  = Format::odds($win_dividend);
								$bet_display_list[$bet->id]['dividend']  = $win_dividend;
								$bet_display_list[$bet->id]['dividend'] .= '/';
								// $bet_display_list[$bet->id]['dividend'] .= Format::odds($place_dividend);
								$bet_display_list[$bet->id]['dividend'] .= $place_dividend;
								break;
						}
					} else {
						$bet_dividends = unserialize($bet->{$bet->bet_type . '_dividend'});

						$bet_display_list[$bet->id]['dividend'] = '&mdash;';
						$dividends_count = count($bet_dividends);

						if ($dividends_count == 1) {
							// $bet_display_list[$bet->id]['dividend'] = Format::odds(array_shift($bet_dividends));
							$bet_display_list[$bet->id]['dividend'] = array_shift($bet_dividends);
						} else if ($dividends_count > 1) {
							$bet_display_list[$bet->id]['dividend'] = array();
							foreach ($bet_dividends as $combination => $bet_dividend) {
								// $bet_display_list[$bet->id]['dividend'][] = $combination . ': ' . Format::odds($bet_dividend);
								$bet_display_list[$bet->id]['dividend'][] = $combination . ': ' . $bet_dividend;
							}
							$bet_display_list[$bet->id]['dividend'] = implode('<br />', $bet_display_list[$bet->id]['dividend']);
						}
					}

					if ($bet->refunded_flag) {
						$scrached_list = $bet_selection_model->getBetSelectionListByBetIDAndSelectionStatus($bet->id, 'scratched');
						$scrached_display = array();
						foreach ($scrached_list as $scrached) {
							$scrached_display[] = $scrached->number . '. ' . $scrached->name;
						}

						$bet_display_list[$bet->id]['half_refund'] = array(
							'label'		=> implode(', ', $scrached_display),
							'bet_type'	=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
							'amount'	=> '&mdash;',
							'bet_total'	=> '&mdash;',
							'dividend'	=> '&mdash;',
							// 'paid'		=> Format::currency($bet->refund_amount),
							'paid'		=> $bet->refund_amount,
							'result'	=> 'REFUND'
						);
					}
				}
				$i++;
			}

			if (count($bet_display_list) > 0) {

				return OutputHelper::json(200, array('bet_list' => $bet_display_list, 'pagination' => $pagination, 'user' => $user->id ));


			}
			else {
				return OutputHelper::json(500, array('error_msg' => 'No betting history found'));
			}

		}else{

		      return OutputHelper::json(500, array('error_msg' => JText::_( 'Invalid Token' ) ));
		}


	}

	public function doSelfExclude() {

		global $mainframe;
		if (!class_exists('TopbettaUserModelTopbettaUser')) {
			JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
		}

		$user	=& JFactory::getUser();
		$model	=& $this->getModel('TopbettaUser', 'TopbettaUserModel');

		$exclusion_end_timestamp = time() + 60 * 60 * 24 * 7;
		$user_data_before_save	= $model->getUser();

		if ($model->selfExclude($user->id, $exclusion_end_timestamp)) {

			$this->_sendExcludeEmail($exclusion_end_timestamp);

			$user_data_after_save = $model->getUser();
			//add user audit

			require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'useraudit.php');
			$user_audit_model = new TopbettaUserModelUserAudit();

			// $user_audit_model		=& $this->getModel('userAudit', 'TopbettaUserModel');
			$audit_params = array(
				'user_id'		=> $user->id,
				'admin_id'		=> -1,
				'field_name'	=> 'self_exclusion_date',
				'old_value'		=> $user_data_before_save->self_exclusion_date,
				'new_value'		=> $user_data_after_save->self_exclusion_date,
			);
			$user_audit_model->store($audit_params);

			$mainframe->logout();
			return OutputHelper::json(200, array('msg' => JText::_('You have been excluded for 1 week from the site. An email will be sent to notify you that this period has ended.')));
		} else {
			return OutputHelper::json(500, array('error_msg' => JText::_('Sorry, there was a problem excluding you. Please contact our customer service department to be excluded for 1 week.')));
		}

	}

	public function doReferFriend() {

		if (!class_exists('TopbettaUserModelTopbettaUser')) {
			JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
		}
		$userModel =& $this->getModel( 'topbettaUser', 'TopbettaUserModel');

		$user =& JFactory::getUser();
		$userId = $user->get('id');

		if (!$userId) {

			return OutputHelper::json(500, array('error_msg' => 'Please login first.'));

		}

		$friendEmail = JRequest::getString('friend_email', null, 'post');
		$subject = JRequest::getString('subject', null, 'post');
		$message = JRequest::getString('message', null, 'post');

		$err = array();

		if( '' == $friendEmail || !JMailHelper::isEmailAddress($friendEmail))
		{
			$err['friend_email'] = 'Please enter a valid email.';
		}
		else if( $userModel->isExisting('email', $friendEmail) )
		{
			$err['friend_email'] = 'Sorry! The email address you have provided is already associated with an existing Topbetta user.';
		}

		if( '' == $subject )
		{
			$err['subject'] = 'Please enter an email subject';
		}

		if( count($err) >  0 )
		{

			return OutputHelper::json(500, array('error_msg' => $err));

		}

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');

		$mailer = new UserMAIL();

		$email_params	= array(
			'subject'	=> $subject,
			'mailto'	=> $friendEmail,
			'mailfrom'	=> $user->email,
			'fromname'	=> $user->name,
			'ishtml'	=> true
		);
		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username,
			'userid'			=> $userId,
			'custom message'	=> $message,
			'custom link'		=> JURI::base() . '/user/register/ref_id/' . $userId
		);
		if($mailer->sendUserEmail('referFriendEmail', $email_params, $email_replacements)) {
			return OutputHelper::json(200, array('msg' => JText::_('An email has been sent to your friend.')));
		} else {
			return OutputHelper::json(500, array('error_msg' => 'Failed to send email to your friend.'));
		}

	}

	/**
	 * Method to send exclude email
	 *
	 * @return void
	 */
	private function _sendExcludeEmail($exclusion_end_timestamp)
	{
		global $mainframe;

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');

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

		//$mailer->sendUserEmail('excludeEmail', $email_params, $email_replacements);
		//var_dump($mailer);

		//send admin notifications
		$mailer = new JMAIL();
		$mailer->setSender(array($mailfrom, $fromname));
		$mailer->addReplyTo(array($mailfrom));
		$mailer->addRecipient($mailfrom);
		$mailer->setSubject('Temporary Exclusion - ' . $user->username . ' (' . $user->id . ')');
		$mailer->setBody('User ' . $user->username . ' (' . $user->id . ') has requested self-exclusion. The exclusion will be lifted on ' . $exclusion_date, false);
		$mailer->IsHTML(false);
		//$mailer->Send();
		//var_dump($mailer);

	}

	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	protected function formatCounterText($time) {
		if ($time < time()) {
			return FALSE;
		}

		$remaining = $time - time();

		$days = intval($remaining / 3600 / 24);
		$hours = intval(($remaining / 3600) % 24);
		$minutes = intval(($remaining / 60) % 60);
		$seconds = intval($remaining % 60);

		$text = $seconds . ' sec';
		if ($minutes > 0) {
			$text = $minutes . ' min';
		}

		if ($hours > 0) {
			$min_sec_text = '';

			if ($days == 0) {
				$min_sec_text = $text;
			}

			$text = $hours . ' hr ' . $min_sec_text;
		}

		if ($days > 0) {
			$text = $days . ' d ' . $text;
		}
		return $text;
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
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
		$model = new TopbettaUserModelTopbettaUser();
		$promotion = $model->getPromotion(trim(strtoupper($code)));
		$user = $model->getUser();

		if (!$promotion && !empty($code)) {
			$err['promo_code'] = 'Invalid promotion code';
		} elseif (!empty($code) && $user->promo_code && $promotion[0]->pro_code) {
			$err['promo_code'] = 'You have already used a promotion code';
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
	 * Method to set key and secret for external website
	 *
	 */
	public function get_external_website_key_secret($key, $secret)
	{
		$token = $token_new = array();

		//For webiste 1
		$token['am739264054']['secret'] 	= 'h37viWA936oYjesUmi';
		$token['am739264054']['source'] 	= 'am1';

		//TopTippa
		$token['tt783629816']['secret'] 	= 'pdAyFnu8za8kKaquh2';
		$token['tt783629816']['source'] 	= 'tip';

		//For webiste 2
		$token['67890']['secret'] 	= 'CDE0123456789';
		$token['67890']['source'] 	= 'w2';

		if($token[$key]['secret'] == $secret)
		{
			$token_new = $token[$key];
		}
		else
		{
			$token_new = '';
		}

		return $token_new;
	}

}
?>
