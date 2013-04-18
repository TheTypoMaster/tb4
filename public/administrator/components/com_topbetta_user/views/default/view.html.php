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
class TopbettaUserViewDefault extends JView {
	
	/**
	* Method to diplay the view
	* 
	* @return void
	*/
    function display($tpl = null) {
		//get task var
		$task = JRequest::getVar('task');
		
		switch ($task) {
			case 'edit':
				$this->editView();
				break;
			default:
				$this->listView();
				break;
		}
		
        parent::display($tpl);
    }
    
    /**
	 * Set up the display of the list page
	 *
	 * @return void
	 */
	public function listView()
	{
		$document	=& JFactory::getDocument();
		$document->addScript('/media/system/js/datepicker.js' );
		
		$js = "window.addEvent('domready', function(){
			$$('input.DatePicker').each( function(el){
				new DatePicker(el);
				});
		}); ";
		$document->addScriptDeclaration($js);
		
		$js = "function isNumberKey(evt)
		      {
		         var charCode = (evt.which) ? evt.which : event.keyCode
		         if (charCode > 31 && (charCode < 48 || charCode > 57))
		            return false;
		         return true;
		      }
		";
		$document->addScriptDeclaration($js);
		
		
		$css = '/media/system/css/datepicker.css';
		$document->addStyleSheet($css);
	}
	
	/**
	 * Set up the display of the edit page
	 *
	 * @return void
	 */
	public function editView()
	{
		$this->bet_limit_request_display = array();
		$row_id = 1;
		foreach ($this->bet_limit_request_list as $bet_limit_request) {
			if ($bet_limit_request->field_name == 'requested_bet_limit' && $bet_limit_request->new_value == 0) {
				continue;
			}
			$bet_limit_request->operationer		= ($bet_limit_request->admin_id == -1 ? $bet_limit_request->username : $bet_limit_request->admin);
			$bet_limit_request->value			= $bet_limit_request->new_value == -1 ? 'no limit' : '$' . bcdiv($bet_limit_request->new_value, 100,2);
			$bet_limit_request->action			= ($bet_limit_request->field_name == 'requested_bet_limit' ? 'request' : 'change');
			$bet_limit_request->row_class		= (($row_id % 2) ? '' : ' class="alt"');
			$this->bet_limit_request_display[] = $bet_limit_request;
			$row_id ++;
		}
		
        $css		= JURI::base().'components/com_topbetta_user/assets/style.css';
		$document	=& JFactory::getDocument();
		$document->addStyleSheet($css);
		
		$js = "
			window.addEvent('domready', function(){
				toggleIdentityDoc();
				
				$('identity_verified_flag').addEvent('click', toggleIdentityDoc);
			}); 
			function toggleIdentityDoc() {
				if($('identity_verified_flag').getProperty('checked')) {
					$$('div.identity_doc').each( function(el){
						el.setStyle('display', '');
					});
				} else {
					$$('div.identity_doc').each( function(el){
						el.setStyle('display', 'none');
					});
				}
			}
		";
		$document->addScriptDeclaration($js);
		
	}
}
?>