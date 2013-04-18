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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class TournamentdollarsViewDefault extends JView {
	/**
	* Method to diplay the view
	* 
	* @return void
	*/
	
	function display($tpl = null) {
		$document = & JFactory::getDocument();		
		$document->setTitle(JText::_('TopBetta - Tournament Transactions'));
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');
		
		parent::display($tpl);
	}
}
?>