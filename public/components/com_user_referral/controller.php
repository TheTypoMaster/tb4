<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
include_once( JPATH_ROOT.DS.'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php' );

/**
 * User Referral Controller
 */
class UserReferralController extends JController
{
	/**
	 * Prevents access to tasks requiring authentication
	 *
	 * @return void
	 */
	public function __construct()
	{
		$user =& JFactory::getUser();

		parent::__construct();
	}

	/**
	 * Display refer a friend page
	 *
	 * @return void
	 */
	function refer_friend()
	{
		$view = JRequest::getVar( 'view', 'default');
		$view =& $this->getView( $view, 'html');
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$config				=& JComponentHelper::getParams('com_topbetta_user');
		$referral_payment	= $config->get('referral_payment');
		$referral_email_txt	= $config->get('referFriendEmailText');
		
		$replacements = array(
			'custom message'	=> '',
			'custom link'		=> JRoute::_(JURI::base() . 'user/register/ref_id/' . $userId),
			'userid'			=> $userId,
		);
		foreach($replacements as $token => $replace) {
			$referral_email_txt = str_replace( '[' . $token . ']', $replace, $referral_email_txt );
		}

		$view->setLayout('referfriend');
		$view->assign( 'itemid', JRequest::getString('Itemid', null, 'get'));
		
		//pre-populate subject
		$formData = array(
			'subject' => JText::_('REFER_FRIEND_EMAIL_SUBJECT')
		);

		$session =& JFactory::getSession();
		if( $sessFormData = $session->get('sessFormData', null, 'topbettauser') )
		{
			$formData = array();
			if( $sessFormErrors = $session->get('sessFormErrors', null, 'topbettauser') )
			{
				$view->assign( 'formErrors', $sessFormErrors);
				$session->clear('sessFormErrors', 'topbettauser');
			}

			foreach($sessFormData as $k => $data) {
				$formData[$k] = stripslashes($data);
			}
			$session->clear('sessFormData', 'topbettauser');
		}

		$view->assign('formData', $formData);
		$view->assign('referral_payment', sprintf('%d', $referral_payment / 100));
		$view->assign('referral_email_txt', nl2br($referral_email_txt));
		$view->display();
	}

	/**
	 * Send referral email to a friend
	 *
	 * @return void
	 */
	function send_referral_email()
	{
		$session =& JFactory::getSession();
		$userModel =& $this->getModel( 'topbettaUser', 'TopbettaUserModel');

		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$friendEmail = JRequest::getString('friend_email', null, 'post');
		$subject = JRequest::getString('subject', null, 'post');
		$message = JRequest::getString('message', null, 'post');
		$itemId = JRequest::getInt('itemid', null, 'post');

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

		$redirectTo = '/user/refer-a-friend';

		if( count($err) >  0 )
		{
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

			return false;
		}

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
			$this->setRedirect( $redirectTo, 'An email has been sent to your friend.' );
		} else {
			$session->set( 'sessFormErrors', $err, 'topbettauser' );
			$session->set( 'sessFormData', $_POST, 'topbettauser');
			$this->setRedirect( $redirectTo, 'Failed to send email to your friend.', 'error' );
		}
	}


}