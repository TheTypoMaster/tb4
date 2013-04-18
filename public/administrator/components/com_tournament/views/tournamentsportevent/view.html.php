<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );


/**
 * Adding the Page title based on the tasks
 */
switch( JRequest::getVar('task') )
{
case 'editSport':
	JToolBarHelper::title( JText::_( 'Sports Events Manager - Add/Edit a Sport' ), 'generic.png' );
	break;
case 'editCompetition':
	JToolBarHelper::title( JText::_( 'Sports Events Manager - Add/Edit an Competition' ), 'generic.png' );
	break;
case 'editEvent':
	JToolBarHelper::title( JText::_( 'Sports Events Manager - Add/Edit an Event' ), 'generic.png' );
	break;
default:
	JToolBarHelper::title( JText::_( 'Sports Events Manager' ), 'generic.png' );
	break;
}
class TournamentViewTournamentSportEvent extends JView
{
  public function display($tpl = null)
  {
    $task = JRequest::getVar('task');
    switch($task) {
      case 'editSport':
        $this->editSport();//sport
        break;
      case 'event':
        $this->eventManager();//sport
        break;
      case 'editCompetition' :
      	$this->editCompetition();
      	break;
      case 'editEvent' :
      	$this->editEvent();
      	break;
      case 'view':
        $this->view();
        break;
      case 'list':
      default:
        $this->listView();
        break;
    }

     // page setup
    $document = & JFactory::getDocument();
    $document->addScript(JURI::root() . DS .'components/com_tournament/assets/common.js');

    if(JRequest::getVar( 'task' ) == "editEvent"){
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

    parent::display($tpl);
  }

	public function listView()
	{
      /**
       * Buttons on top right
       */
	JToolBarHelper::addNew('newEvent','Add Event');
    //JToolBarHelper::preferences('com_tournament', '350');

    	$sports_list =& $this->get('sports_list', null);
    	
    	if (is_null($sports_list)) {
    		$sports_list = array();
    	}
    	
    	// formatting for table items here
    	foreach($sports_list as $tournament) {
      		$tournament->parent_name    = (empty($tournament->parent_name)) ? 'None' : $tournament->parent_name;
      		$tournament->sport_name     = ucfirst($tournament->sport_name);

      		$tournament->prize_formula  = (empty($tournament->jackpot_flag) || (int)$tournament->parent_tournament_id <= 0) ? 'Cash' : 'Ticket';
      		$tournament->gameplay       = (empty($tournament->jackpot_flag)) ? 'Single' : 'Jackpot';
      		$tournament->cancelled      = (empty($tournament->admin_cancelled_flag)) ? 'Active' : 'Cancelled';
      		$tournament->status         = (empty($tournament->status_flag)) ? 'Unpublished' : 'Published';

      		$tournament->buy_in         = (empty($tournament->buy_in)) ? 'Free' : number_format($tournament->buy_in / 100, 2);
      		$tournament->entry_fee      = (empty($tournament->entry_fee)) ? 'Free' : number_format($tournament->entry_fee / 100, 2);
    	}
    	
		$this->assign('sports_list', $sports_list);
	}
	/**
	 * Method to add/edit Sport
	 */
	public function editSport()
	{
		JToolBarHelper::save("saveSport", "Save");
   		JToolBarHelper::cancel();

	}
	/**
	 * Method to add/edit competition
	 */
	public function editCompetition()
	{
		JToolBarHelper::save("saveCompetition", "Save");
		JToolBarHelper::deleteListX("Do you really want to delete the competition?","deleteCompetition", "Delete");
   		JToolBarHelper::cancel();

	}
	/**
	 * Method to add/edit Event
	 */
	public function editEvent()
	{
		JToolBarHelper::save("saveEvent", "Save");
		JToolBarHelper::deleteListX("Do you really want to delete the event?","deleteEvent", "Delete");
   		JToolBarHelper::cancel();

	}
}