<?php
/**
* @version		$Id: view.html.php 11673 2009-03-08 20:41:00Z willebil $
* @package		Joomla
* @subpackage	Registration/Upgrade
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
 * HTML View class for the Registration component
 *
 * @package		Joomla
 * @subpackage	Registration
 * @since 1.0
 */
class topbettaUserViewUpgrade extends JView
{
	function display($tpl = null)
	{
		
		global $mainframe;

		// Check if registration is allowed
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if (!$usersConfig->get( 'allowUserRegistration' )) {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}
		//Add stylesheet to the document formValidation.js
		$document = & JFactory::getDocument();		
		$document->addStyleSheet('components/com_topbetta_user/assets/view.register.css');

		//Add javascript files
		$document->addScript( 'components/com_topbetta_user/assets/formValidation.js' );
		$document->addScript( 'components/com_topbetta_user/assets/formEvent.js' );
		$document->addScript( 'components/com_topbetta_user/assets/pwdmeter.js' );

		$pathway  =& $mainframe->getPathway();
		$document =& JFactory::getDocument();
		$params	= &$mainframe->getParams();

	 	// Page Title
		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Upgrade' ));
			}
		} else {
			$params->set('page_title',	JText::_( 'Upgrade account' ));
		}
		$document->setTitle( $params->get( 'page_title' ) );

		$pathway->addItem( JText::_( 'New' ));

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		$user =& JFactory::getUser();
		$this->assignRef('user', $user);
		$this->assignRef('params', $params);
		
		$this->openx_banner = false;
		if (!empty($this->formData['banner_id'])) {
			$config			=& JComponentHelper::getParams('com_topbetta_user');
			$openx_banner	= $config->get('openxBanner');
			
			$this->openx_banner = str_replace('[banner id]' , $this->formData['banner_id'], $openx_banner );
			
		}
		
		$banner_name = $this->sport_cookie;
		if (empty($banner_name)) {
			$banner_name = 'generic';
		}
		$this->banner = 'register-now-' . $banner_name . '.jpg';
		parent::display($tpl);
	}
}
