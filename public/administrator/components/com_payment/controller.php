<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: controller.php 2010-08-08 23:27:25 svn $
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

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * payment Controller
 *
 * @package Joomla
 * @subpackage payment
 */
class PaymentController extends JController {
    /**
     * Constructor
     * 
     * @return void
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'default');
        }
        $this->item_type = 'Default';
        
        parent::__construct();
    }
    
    function configuration() {
    	JRequest::setVar('hidemainmenu', 1);
    	// Build the toolbar for the add function
		JToolBarHelper::title( JText::_('Payment Configuration')
		. ': [<small>Edit</small>]' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
    	$view = JRequest::getVar( 'view', 'default');
    	
    	$model =& $this->getModel( 'payment');
    	
    	$layout = JRequest::getVar( 'layout', 'configuration' );
    	$view =& $this->getView( $view, 'html');
    	$view->setLayout( $layout );
    	
    	//init form data
    	$formData = array();
    	$params =& JComponentHelper::getParams( 'com_payment' );
    	foreach( $model->paramFields as $paramField )
    	{
    		$formData[$paramField] = $params->get( $paramField );
    	}
    	
		//get the validation msg and keep the value entered after validation
		$session =& JFactory::getSession();
		
    	if( $sessFormData = $session->get('sessFormData', null, 'payment') )
    	{
    		//print_r($session->get('sessFormErrors', null, 'withdrawal'));exit;
    		if( $sessFormErrors = $session->get('sessFormErrors', null, 'payment') )
    		{
    			$view->assign( 'formErrors', $sessFormErrors);
    			$session->clear('sessFormErrors', 'payment');
    		}
    		
    		$sessParams = $sessFormData['params'];
    		foreach( $model->paramFields as $paramField )
    		{
    			$formData[$paramField] = stripslashes($sessParams[$paramField]);
    		}
    		$session->clear('sessFormData', 'payment');
    	}
    	
    	$view->assign('formData', $formData );
    	$view->assign('varReplacements', $model->rules['varReplacements']);
    	$view->display();
    }
    
    function save()
    {
		$model =& $this->getModel( 'payment' );
    	$session =& JFactory::getSession();
    	
		$failedRedirectTo = 'index.php?option='
			.JRequest::getVar('option')
			.'&task=configuration';
			
		$successRedirectTo = 'index.php?option='
			.JRequest::getVar('option');
    	
    	$err = array();
    	
    	$paramValues = array();
    	$paramsPosted = JRequest::getVar( 'params', '', 'post');
    	$postedValues = array();
    	foreach( $model->paramFields as $paramField )
    	{
    		$value = $postedValues[$paramField] = $paramsPosted[$paramField];
    		
	    	if('' == $value )
	    	{
	    		$err[$paramField] = 'You must enter a value.';
	    	}
	    	
	    	if( in_array($value, $model->rules['integerFields']) && !ctype_digit($value))
	    	{
	    		$err[$paramField] = 'You must enter an integer.';
	    	}
    	}
    	
        if( count($err) > 0 )
    	{
    		$session->set( 'sessFormErrors', $err, 'payment' );
    		$session->set( 'sessFormData', $_POST, 'payment');
    		
			$this->setRedirect( $failedRedirectTo, 'There were some errors processing this form. See messages below.' );
    		return false;
    	}

    	//store params
    	$params = &JComponentHelper::getParams( 'com_payment' );

    	$registry = new JRegistry();
    	$registry->loadArray($postedValues);
    	$postedValues = $registry->toString();
    	
    	if( !$model->updateParams($postedValues))
    	{
    		$this->setRedirect( $failedRedirectTo, 'Failed to save config!' );
    		return false;
    	}
    	
    	$this->setRedirect( $successRedirectTo, 'Payment Config Saved' );
    	
    	return true;
    }
    
    function cancel()
    {
		$redirectTo = 'index.php?option='
		.JRequest::getVar('option');
		$this->setRedirect( $redirectTo );
    }
}
?>