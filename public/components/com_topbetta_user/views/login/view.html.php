<?php
/**
* @version		$Id: view.html.php 12352 2009-06-24 13:52:57Z ian $
* @package		Joomla
* @subpackage	Login
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * User component login view class
 *
 * @package		Joomla
 * @subpackage	Users
 * @since	1.0
 */
class topbettaUserViewLogin extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		// Initialize variables
		$document	=& JFactory::getDocument();
		$user		=& JFactory::getUser();
		$pathway	=& $mainframe->getPathway();
		$image		= '';

		if ($this->getLayout() == 'simplelogin'){
			$this->getsatisfaction_login();
		} else{
			//redirct to home page as login module is always visible
			$link = '/';
			$mainframe->redirect($link);
		}
		
		$menu   =& JSite::getMenu();
		$item   = $menu->getActive();
		if($item)
			$params	=& $menu->getParams($item->id);
		else
			$params	=& $menu->getParams(null);


		$type = (!$user->get('guest')) ? 'logout' : 'login';

		// Set some default page parameters if not set
		$params->def( 'show_page_title', 				1 );
		if (!$params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Login' ));
			}
		if(!$item)
		{
			$params->def( 'header_login', 			'' );
			$params->def( 'header_logout', 			'' );
		}

		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'login', 					'index.php' );
		$params->def( 'logout', 				'index.php' );
		$params->def( 'description_login', 		1 );
		$params->def( 'description_logout', 		1 );
		$params->def( 'description_login_text', 	JText::_( 'LOGIN_DESCRIPTION' ) );
		$params->def( 'description_logout_text',	JText::_( 'LOGOUT_DESCRIPTION' ) );
		$params->def( 'image_login', 				'key.jpg' );
		$params->def( 'image_logout', 				'key.jpg' );
		$params->def( 'image_login_align', 			'right' );
		$params->def( 'image_logout_align', 		'right' );
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		if ( !$user->get('guest') )
		{
			$title = JText::_( 'Logout');

			// pathway item
			$pathway->addItem($title, '' );
			// Set page title
			$document->setTitle( $title );
		}
		else
		{
			$title = JText::_( 'Login');

			// pathway item
			$pathway->addItem($title, '' );
			// Set page title
			$document->setTitle( $title );
		}

		// Build login image if enabled
		if ( $params->get( 'image_'.$type ) != -1 ) {
			$image = 'images/stories/'.$params->get( 'image_'.$type );
			$image = '<img src="'. $image  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" hspace="10" alt="" />';
		}

		// Get the return URL
		if (!$url = JRequest::getVar('return', '', 'method', 'base64')) {
			$url = base64_encode($params->get($type));
		}

		$errors =& JError::getErrors();

		$this->assign('image' , $image);
		$this->assign('type'  , $type);
		$this->assign('return', $url);

		$this->assignRef('params', $params);


		parent::display($tpl);
	}
	/**
	 * getsatisfaction login
	 */
	public function getsatisfaction_login(){
		$do_login = JRequest::getBool('gs_login', false);
		$do_logout = JRequest::getVar('gs_logout', false);
		
		JRequest::setVar('tmpl', 'component');
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_topbetta_user/assets/gs-login.css');

		$this->assign('fastpass_login', $do_login);
		$this->assign('fastpass_logout', $do_logout);
	}
}

