<?php
require_once '../common/shell-bootstrap.php';

class Sandbox extends TopBettaCLI
{
	public function initialise()
	{
		jimport('mobileactive.wagering.bet');
		jimport('mobileactive.wagering.api');
		
		$this->addComponentModels('tournament');

		$this->meeting			 =& JModel::getInstance('Meeting', 'TournamentModel');
		$this->race			 =& JModel::getInstance('Race', 'TournamentModel');
		
		$this->tournament_bet  =& JModel::getInstance('TournamentBet', 'TournamentModel');
	}
	
	public function execute()
	{
		//$output = $this->race->getRaceTimesByMeetingID(495);
		//var_dump($output);
		$this->service_test();
	}
	
	public function bet_test(){
	
		/*$bet = Bet::newBet(Bet::BET_TYPE_FIRSTFOUR);
		$bet->amount = 50;
		$bet->flexi_flag = false;
		$bet->odds = 0;
		$bet->boxed_flag = 1;
		
		$bet->addSelection(5);
		$bet->addSelection(323);
		$bet->addSelection(23);
		$bet->addSelection(24);*/
		
		$bet = WageringBet::newBet(WageringBet::BET_TYPE_TRIFECTA, 100, false, false)
		//->addSelection(21)
		//->addSelection(22)
		->addSelection(1,1)		
		->addSelection(2,1)
				->addSelection(3,2)
				//->addSelection(26,2)
				->addSelection(4,3);
				
		//echo $bet->getBetSelectionObject();
		
		if($bet->isValid()){
			$this->l($bet->getTotalBetAmount());
			$this->l($bet->getCombinationCount());
		}
		else{
			$this->l($bet->getCombinationCount());
			$this->l($bet->getErrorMessage());
		}
	}
	
	public function service_test(){
		
//		$bet = WageringBet::newBet(WageringBet::BET_TYPE_TRIFECTA, 100, true, false, 6)
//				->addSelection(5)		
//				->addSelection(4)
//				->addSelection(6)
//				->addSelection(3);
		
		/*$bet2 = WageringBet::newBet(WageringBet::BET_TYPE_FIRSTFOUR, 100, true, true, 2)
		->addSelection(2)
		->addSelection(9)
		->addSelection(6)
		->addSelection(3); */
				//->addSelection(5, 3);
		
//		$event_object = new stdClass;
//		$event_object->meeting_code = 'VIC';
//		$event_object->type_code = 'R';
//				
		$api = WageringApi::getInstance(WageringApi::API_TASTAB);
		
		//var_dump($api->validateBet($bet, $event_object));
		
		//var_dump($api->placeBet($bet, $event_object, 0));
		
		//var_dump($api->placeBet($bet, $event_object, md5(rand())));
		
		var_dump($api->getAccountHistory());
		
		var_dump($api->getErrorList());
		
		/* while(1){
			$api = WageringApi::getInstance(WageringApi::API_TASTAB);
			$push = WageringApiTastabService::getInstance('WageringApiTastabPush');
			$status  = $push->_getCurrentStatus();
			var_dump($status);
			sleep(600);
		} */
		
		//$bet_list = $this->tournament_bet->getUnresultedTournamentBetListByEventID(1359);
		
		//var_dump($bet_list);
	}
	
	public function push_test(){
		
		$api = WageringApi::getInstance(WageringApi::API_TASTAB);
		
		$push = $api->getPushService();
		//$push->processNextMessage();
		echo $push->getXML();
		//while(1){
			//var_dump($push->getMeetingList());
		//}
	}
}

$sandbox = new Sandbox;
$sandbox->execute();