<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Sportsbetting Component
 *
 * @package    Sportsbetting
 */

class SportsbettingViewSportsbetting extends JView
{
	function display($tpl = null) {
		$document = & JFactory::getDocument();		
		//set document title
		
		$titleText = 'TopBetta - Sports Betting';
		$currentUrl = $_SERVER["REQUEST_URI"];
		$currentUrlArr = explode('/', $currentUrl);
		$titleText.= ' | '.ucfirst($currentUrlArr[3]). ' | '.ucwords(str_replace('-', ' ', $currentUrlArr[4]));
		$document->setTitle( JText::_($titleText) );

		//setup vars
		$assets_path = 'components/com_sportsbetting/assets/';
		
		$sid = JRequest::getVar( 'sid', 0, 'get', 'INT');
		$cid = JRequest::getVar( 'cid', 0, 'get', 'INT');
		$eid = JRequest::getVar( 'eid', 0, 'get', 'INT');

		//add stylesheets to the document
		$document->addStyleSheet(JURI::base().$assets_path.'sportsbetting.css');
		//add scripts to the document

		$document->addScript(JURI::base().$assets_path.'sportsbetting.js');
		//$document->addScript(JURI::base().'components/com_tournament/assets/common.js');
		//-- jquery-ui --//
		//$document->addScript("//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js");

		//-- iosSlider plugin --//
		//$document->addScript(JURI::base().$assets_path.'jquery.iosslider.min.js');
		$document->addScript(JURI::base().$assets_path.'jquery.carouFredSel-6.2.0-packed.js');
		//$document->addScript(JURI::base().$assets_path.'jquery.ba-throttle-debounce.min.js');
		$document->addScript(JURI::base().$assets_path.'jquery.touchSwipe.min.js');

		//get data from the model		
		//$sports =& $this->get('Sports');

		//$data =& $this->get('Data');
		$model = $this->getModel();
		
		//setup the time range
		$now_time = time();
		$data =& $model->getData($now_time, $sid, $cid, $eid);
		//$this->assignRef( 'data', $data );
		$this->assignRef( 'sportsNcomps', $data['sportsNcomps'] );
		$this->assignRef( 'events', $data['events'] );
		$this->assignRef( 'typesNoptions', $data['typesNoptions'] );

		$this->assignRef( 'sportID', $data['ids'][0] );
		$this->assignRef( 'compID', $data['ids'][1] );
		$this->assignRef( 'eventID', $data['ids'][2] );

		parent::display($tpl);
	}
}


