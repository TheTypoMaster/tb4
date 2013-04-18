<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: view.html.php 2010-08-08 23:27:25 svn $
 * @author Oliver Shanahan
 * @package Joomla
 * @subpackage payment
 * @license @12follow 
 *
 * This is ucbetman component
 *
 *
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class ucbetmanViewucbetman extends JView {
	
	/**
	* Method to diplay the view
	* 
	* @return void
	*/
    function display($tpl = null) {
		//added css
        $css = JURI::base().'components/com_ucbetman/assets/style.css';
		$document =& JFactory::getDocument();
		$document->addStyleSheet($css);
		//added javascript
		$js = JURI::base().'components/com_ucbetman/assets/configuration.js';
		$document->addScript($js);
		
        parent::display($tpl);
    }
}
?>