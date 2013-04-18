<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: view.html.php 2010-08-08 23:27:25 svn $
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the payment component
 */
class PaymentViewWithdrawalrequest extends JView {
	
	function display($tpl = null) {
		global $mainframe;
		//get userinfo
		$user =& JFactory::getUser();
		//Check if user is logged in.  If not redirect them to login page
		if ($user->guest) {
			$msg = JText::_("You need to login to access this part of the site.");
			$msgtype = "error";
			$link = 'index.php?option=com_topbetta_user&task=register';
			$mainframe->redirect($link,$msg,$msgtype);
		}
		
		$document = & JFactory::getDocument();		
		$document->setTitle( JText::_('TopBetta - Withdrawal') );
		//$model =& $this->getModel();

		//Add stylesheets to the document
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');
		
		parent::display($tpl);
	}
	
}
?>