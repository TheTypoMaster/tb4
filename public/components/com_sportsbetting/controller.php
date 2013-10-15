<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class SportsbettingController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	function display()
	{
		parent::display();
	}

	//not used anymore as this has been moved to ajax calls thru the api
	function saveBet() {
		//TODO: need to tighten the restrictions here
		// bet placement params

			// the bookmaker event ID
			 $event_id = JRequest::getVar( 'external_selection_id', 0, 'post');
			// awin/bwin/draw or competitor ID - see examples below.
			 $bet_option_id = JRequest::getVar( 'bet_place_ref', 0, 'post');
			// Amount to bet
			 $bet_amount = JRequest::getVar( 'bet_amount', 0, 'post');
			// the type of bet - see examples below
			 $bet_type_ref = JRequest::getVar( 'bet_type_ref', 0, 'post', 'STRING');
			//current dividend on the market Can't be > actual dividend. Will error and return current dividend if so.
			 $bet_dividend = JRequest::getVar( 'bet_odds', 0, 'post');
		
		//get the redirect params
		$sportID = JRequest::getVar( 'sid', 0, 'post', 'INT');
		$compID = JRequest::getVar( 'cid', 0, 'post', 'INT');
		$eventID = JRequest::getVar( 'eid', 0, 'post', 'INT');

			$bet_handicap = 1; // Always set to 1
		

		//TODO: add in the saveBet api call here
			// apiUrl = "http://topbetta.com/api/?method=saveBet";
			
			// place a bet into the betslip via the API
			$ch = curl_init();
			//$endpoint = "http://sandbox.bookmaker.com.au/api/affiliateapi/%s";
			$endpoint = "http://sandbox.bookmaker.com.au:8080/api/affiliateapi/%s";


			// bet placement params

						
			
			// get the user token
			$call = "login";
			$params = "username=topbetta&password=0p3ris";			
			curl_setopt($ch,CURLOPT_URL,sprintf($endpoint,$call));
			curl_setopt($ch,CURLOPT_POST,2);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.53.11 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10');
			$raw_output = curl_exec($ch);
			$output = json_decode($raw_output);
			$user_token = $output->token;
			//$curlMsg = "<br/>user token: $user_token";
			
			// check the token
			$call = "checkauth?token={$user_token}";
			curl_setopt($ch,CURLOPT_URL,sprintf($endpoint,$call));
			$raw_output = curl_exec($ch);
			$output = json_decode($raw_output);
			
			if(!$output->result) {
				$curlMsg = "<br>There was an error checking token - $user_token";
			} else {
				$curlMsg = "<br>Token is OK: $user_token\n";
			}
			
			//place the bet
		   $params = "eventId=$event_id&special=&handicap=$bet_handicap&betType=$bet_type_ref&betAmount=$bet_amount&optionId=$bet_option_id&dividend=$bet_dividend";
		   $call = "quickbet?token={$user_token}";
		   curl_setopt($ch,CURLOPT_URL,sprintf($endpoint,$call));
		   curl_setopt($ch,CURLOPT_POST,sizeof(explode("&",$params)));
		   curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
		   $raw_output = curl_exec($ch);

		   
		   $bet_return = (json_decode($raw_output));
		   //print_r(json_decode($raw_output));
		   print_r($bet_return);
		   
		   //$bet_return_detail = $bet_return->detail;
		   //print_r($bet_return);


/////////////////////////////
		$debugMsg = "
		<b>PLACE BET PARAMS</b><br/>
		special=, handicap=$bet_handicap<br/>
		betType=$bet_type_ref, eventId=$event_id, optionId=$bet_option_id<br/>
		betAmount=$bet_amount, dividend=$bet_dividend<br/>
		<br/>
		<b>PLACE BET RETURN</b><br/>
		result: $bet_return->result<br/>
		type: $bet_return->type<br/>
		status: $bet_return->status<br/>";
		if(isset($bet_return->detail)){ $debugMsg.="detail: $bet_return->detail<br/>"; }
		if(isset($bet_return->newOdds)){ $debugMsg.="newOdds: $bet_return->newOdds<br/>"; }
		$betPlaceResults = $bet_return->results[0];
		if(isset($betPlaceResults)){
			$debugMsg.="actualDividend: $betPlaceResults->actualDividend<br/>";
			$debugMsg.="result: $betPlaceResults->result, message: $betPlaceResults->message<br/>";
			$debugMsg.="slipPosition: $betPlaceResults->slipPosition, freebet: $betPlaceResults->freebet<br/>";
			$debugMsg.="invoice_id: $betPlaceResults->invoice_id<br/>";
		}
		$debugMsg.="hash: $bet_return->hash<br/>
		<br/>";

//$debugMsg = "print_r($bet_return)";
/* //return from successfull bet placement - bookmaker
[
	result	:	success
	type	:	bet_quick
	status	:	Confirmed! Your bet was accepted.
	results		[
		result	:	success
		message	:	Placed bet
		slipPosition	:	1
		freebet	:	null
		actualDividend	:	5.50
		invoice_id	:	560BBFC2-0448-4E9C-BDB0-00A54ABD5996
	]
	hash	:	8a90966d88f2fccbe12a4f8519825c28
]
*/


		
		//TODO: add in the error check on saveBet return
		$saveBetError = 2; // debug=2 
		$error_msg = "Event Closed"; //tmp error msg
		
		
		if ($saveBetError == 1) {
			$msg = "There Was An ERROR Placing Your Bet - $error_msg";
			$type = "error";
		} elseif ($saveBetError == 2) {
			$msg = "<div style='padding: 10px 20px;'>";
			$msg.= $curlMsg."<br/><br/>";
			$msg.= $debugMsg;
			$msg.= "</div>";
			$type= 'notice';
		} else {
			$msg = "Your Bet Has Been Placed - Bet ID: $id";
			$type= 'message';
		}
		

		$redirect_params = "&sid=$sportID&cid=$compID&eid=$eventID";
		$url = "index.php?option=com_sportsbetting".$redirect_params;
		
		$this->setRedirect($url, $msg, $type);
		
	}

}