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
 */
class PaymentController extends JController
{

    protected function _showBankDeposit(){
    	jimport('mobileactive.client.geoip');
    	$show = false;
    	try{
    		$client_geoip = new ClientGeoIP($_SERVER['REMOTE_ADDR']);
			
			if($client_geoip->getCountryCode() == 'AU'){
				$show = true;
			}
		}
		catch(Exception $e){
			trigger_error("Problem with GeoIP client ['{$e->getMessage()}']");
			$show = true;
		}
		
		return $show;
    }
}
?>