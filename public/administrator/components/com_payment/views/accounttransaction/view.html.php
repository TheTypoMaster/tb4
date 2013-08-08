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
class PaymentViewAccounttransaction extends JView
{
	/**
	* Method to diplay the view
	*
	* @return void
	*/
	function display($tpl = null)
	{
        parent::display($tpl);

        JHTML::_('behavior.mootools');

        //added css
        //$css = JURI::base().'components/com_payment/assets/style.css';
        $css = '/administrator/components/com_payment/assets/style.css';
		$document =& JFactory::getDocument();
		$document->addStyleSheet($css);

		if( 'add' == JRequest::getVar( 'task' ))
		{
			//added javascript
			//$js = JURI::base().'components/com_payment/assets/autocomplete.js';
			$js = '/administrator/components/com_payment/assets/autocomplete.js';
			$document->addScript($js);
		}
		else
		{
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
    }
}
?>