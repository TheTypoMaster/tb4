<?php
/**
 * @version		$Id: motd.php  Michael Costa $
 * @package		API
 * @subpackage	
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');


class Api_Motd extends JController {

	function Api_Motd() {
		
	}

    function getMOTD() {
	     require_once (JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_motd' . DS . 'models' . DS . 'motd.php');
		 $model = new MotdsModelMotd();
		 $result = $model-> getTodaysMsg();

		 if(!empty($result)){
             $data = array('message_website' => $result->msg_web, 'message_mobile' => $result->msg_mob , 'message_app' => $result->msg_app );
			 return OutputHelper::json(200, $data);

		 }else{
             return OutputHelper::json( 500,array('error_msg' => 'No message for today.'));
		 }
    
	}
	

}
?>