<?php
/**
 * @version		$Id: index.php  Michael Costa $
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

/* BootStrap the Joomla core */
include '../api-bootstrap.php';

/* can't continue without Joomla core */
defined('_JEXEC') or die('Restricted access');

/* include our helpers */
include 'helpers/request.php';
include 'helpers/output.php';

/* include all our req classes */
include 'classes/user.php';
include 'classes/betting.php';
include 'classes/racing.php';
include 'classes/sport.php';
include 'classes/privatetournament.php';
include 'classes/motd.php';
include 'classes/payment.php';

//print_r($mainframe);
$db = &JFactory::getDBO();

$method = RequestHelper::validate('method');

//TODO: implement auth
$apikey = RequestHelper::validate('apikey');
$secret = RequestHelper::validate('secret');

/* branch off for each method */
switch($method) {

    case 'getMOTD' :
		$motd = new Api_Motd();
		echo $motd -> getMOTD();
		break;


	/* RACE BETTING */
	/*
	 RewriteRule ^betting/racing$ /index.php?option=com_betting&Itemid=19  [L]
	 RewriteRule ^betting/racing/galloping$ /index.php?option=com_betting&open_id=0&Itemid=19  [L]
	 RewriteRule ^betting/racing/greyhounds$ /index.php?option=com_betting&open_id=1&Itemid=19  [L]
	 RewriteRule ^betting/racing/harness$ /index.php?option=com_betting&open_id=2&Itemid=19  [L]
	 RewriteRule ^betting/racing/([^/]*)/([^/]*)$ /index.php?option=com_betting&task=$1 [L]
	 RewriteRule ^betting/racing/([^/]*)/([^/]*)/([^/]*)$ /index.php?option=com_betting&task=$1&meeting_id=$2&number=$3 [L]
	 */
	//>> /api/?method=getRacing&type=r
	//>> type = r,g,h
	case 'getRacing' :
		$racing = new Api_Betting();
		echo $racing -> getRacingByType();
		break;

	//>> /api/?method=getRaceDetails&meet=102&racenum=4
	case 'getRaceDetails' :
		$race_details = new Api_Betting();
		echo $race_details -> getRaceDetails();
		break;

	//>> /api/?method=getFastBetRaces
	case 'getFastBetRaces' :
		$fast_bet = new Api_Betting();
		echo $fast_bet -> getFastBetRaces();
		break;

	//>> /api/?method=saveBet
	case 'saveBet' :
		$save_bet = new Api_Betting();
		echo $save_bet -> saveBet();
		break;
		
	//>> /api/?method=saveBet
	case 'saveTournamentBet' :
		$save_bet = new Api_Betting();
		echo $save_bet -> saveTournamentBet();
		break;

	case 'saveTournamentSportsBet' :
		$save_bet = new Api_Betting();
		echo $save_bet -> saveTournamentSportsBet();
		break;				

	case 'saveRacingBet' :
		$save_bet = new Api_Betting();
		echo $save_bet -> saveRacingBet();
		break;

	case 'saveSportBet' :
		$save_sportsbet = new Api_Betting();
		echo $save_sportsbet -> saveSportBet();
		break;	

	/* TOURNAMENTS */
	/*
	 RewriteRule ^tournament$ /index.php?option=com_tournament [L]
	 RewriteRule ^tournament/racing$ /index.php?option=com_tournament&controller=tournamentracing&Itemid=2 [L]
	 RewriteRule ^tournament/racing/cash$ /index.php?option=com_tournament&controller=tournamentracing&jackpot=0&Itemid=2 [L]
	 RewriteRule ^tournament/racing/jackpot$ /index.php?option=com_tournament&controller=tournamentracing&jackpot=1&Itemid=2 [L]
	 RewriteRule ^tournament/racing/cash/([^/]*)$ /index.php?option=com_tournament&controller=tournamentracing&jackpot=0&meeting_id=$1&Itemid=2 [L]
	 RewriteRule ^tournament/racing/jackpot/([^/]*)$ /index.php?option=com_tournament&controller=tournamentracing&jackpot=1&meeting_id=$1&Itemid=3 [L]
	 RewriteRule ^tournament/racing/component/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentracing&tmpl=component&task=$1&id=$2 [L]
	 RewriteRule ^tournament/racing/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentracing&task=$1&id=$2 [L]
	 RewriteRule ^tournament/racing/([^/]*)/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentracing&task=$1&id=$2&number=$3 [L]

	 RewriteRule ^tournament/sports$ /index.php?option=com_tournament&controller=tournamentsportevent&Itemid=16 [L]
	 RewriteRule ^tournament/sports/component/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentsportevent&tmpl=component&task=$1&id=$2 [L]
	 RewriteRule ^tournament/sports/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentsportevent&task=$1&id=$2 [L]
	 RewriteRule ^tournament/sports/([^/]*)/([^/]*)/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentsportevent&task=$1&id=$2&match_id=$3&market_id=$4 [L]
	 RewriteRule ^tournament/sports/([^/]*)/([^/]*)/([^/]*)$ /index.php?option=com_tournament&controller=tournamentsportevent&task=$1&id=$2&match_id=$3 [L]
	 RewriteRule ^tournament/sports/([^/]*) /index.php?option=com_tournament&controller=tournamentsportevent&sport_id=$1&Itemid=16 [L]

	 RewriteRule ^tournament/details/([^/]*)$ /index.php?option=com_tournament&task=tournamentdetails&id=$1 [L]
	 RewriteRule ^tournament/([^/]*)/([^/]*)$ /index.php?option=com_tournament&task=$1&id=$2 [L]

	 RewriteRule ^tournamenthistory$ /index.php?option=com_tournament&task=tournamenthistory [L]
	 RewriteRule ^tournamenthistory/([^/]*)$ /index.php?option=com_tournament&task=tournamenthistory&limitstart=$1 [L]

	 RewriteRule ^privatetournamenthistory$ /index.php?option=com_tournament&task=privatetournamenthistory [L]
	 RewriteRule ^privatetournamenthistory/([^/]*)$ /index.php?option=com_tournament&task=privatetournamenthistory&limitstart=$1 [L]

	 RewriteRule ^private/([^/]*)$ /index.php?option=com_tournament&task=tournamentdetails&identifier=$1 [L]
	 */
	//>> /api/?method=getRacingTournaments&type=cash&competition_id=
	//>> type = cash,jackpot
	//>> competition_id = galloping,greyhounds,harness
	case 'getRacingTournaments' :
		$racing_tournaments = new Api_Racing();
		echo $racing_tournaments -> getRacingTournamentsByType();
		break;

	case 'getSportsTournaments' :
		$sport_tournaments = new Api_Sport();
		echo $sport_tournaments -> getSportTournamentsByType();
		break;

    //>> /api/?method=getRaceMeeting&meeting_id=7&number=11
	//>> meeting_id = meeting id
	//>> number = game number
	case 'getRaceMeeting' :
		$race_meeting = new Api_Racing();
		echo $race_meeting -> getRaceMeeting();
		break;


	//>> /api/?method=getTournamentDetails&id=7
	//>> id = tournament id
	case 'getTournamentDetails' :
		$tournament_details = new Api_Racing();
		echo $tournament_details -> getTournamentDetails();
		break;

	case 'saveTournamentTicket' :
		$save_tournament_ticket = new Api_Betting();
		echo $save_tournament_ticket -> saveTournamentTicket();
		break;		

	/* PRIVATE TOURNAMENT */
    //>> /api/?method=getPrivateTournamentDetails
	
	case 'getPrivateTournamentForm' :
		$private_tournament_form = new Api_PrivateTournament();
		echo $private_tournament_form -> getPrivateTournamentForm();
		break;

	//>> /api/?method=registerPrivateTournament
	
	case 'registerPrivateTournament' :
		$register_private_tournament = new Api_PrivateTournament();
		echo $register_private_tournament -> registerPrivateTournament();
		break;


	/* USER/PAYMENT */
	/*
	 RewriteRule ^user$ /index.php?option=com_topbetta_user&view=myaccount&Itemid=6 [L]
	 RewriteRule ^user/account$ /index.php?option=com_topbetta_user&view=myaccount&Itemid=6 [L]
	 RewriteRule ^user/account/settings$ /index.php?option=com_topbetta_user&view=myaccount&layout=accountsettings&Itemid=6 [L]

	 RewriteRule ^user/account/deposit/type/([^/]*)/([^/]*)$ /index.php?option=com_payment&c=account&task=$1&act=$2&Itemid=6 [L]
	 RewriteRule ^user/account/deposit/type/([^/]*)$ /index.php?option=com_payment&c=account&layout=instantdeposit&type=$1&Itemid=6 [L]
	 RewriteRule ^user/account/deposit$ /index.php?option=com_payment&c=account&layout=instantdeposit&Itemid=6 [L]
	 RewriteRule ^user/account/instant-deposit/type/([^/]*)/([^/]*)$ /index.php?option=com_payment&c=account&task=$1&act=$2&Itemid=6 [L]
	 RewriteRule ^user/account/instant-deposit/type/([^/]*)$ /index.php?option=com_payment&c=account&layout=instantdeposit&type=$1&Itemid=6 [L]
	 RewriteRule ^user/account/instant-deposit$ /index.php?option=com_payment&c=account&layout=instantdeposit&Itemid=6 [L]
	 RewriteRule ^user/account/bank-deposit$ /index.php?option=com_payment&c=account&layout=instantdeposit&Itemid=6 [L]
	 RewriteRule ^user/account/bpay-deposit$ /index.php?option=com_payment&c=account&layout=instantdeposit&Itemid=6 [L]

	 RewriteRule ^user/account/withdrawal-request/type/([^/]*)$ /index.php?option=com_payment&c=withdrawal&type=$1&Itemid=6 [L]
	 RewriteRule ^user/account/withdrawal-request$ /index.php?option=com_payment&c=withdrawal&Itemid=6 [L]
	 RewriteRule ^user/account/transactions$ /index.php?option=com_payment&c=account&layout=transactions&Itemid=6 [L]
	 RewriteRule ^user/account/transactions/type/([^/]*)$ /index.php?option=com_payment&c=account&layout=transactions&transaction_type=$1&Itemid=6 [L]
	 RewriteRule ^user/account/transactions/([^/]*)$ /index.php?option=com_payment&c=account&layout=transactions&Itemid=6&limitstart=$1 [L]
	 RewriteRule ^user/account/transactions/type/([^/]*)/([^/]*)$ /index.php?option=com_payment&c=account&layout=transactions&transaction_type=$1&Itemid=6&limitstart=$2 [L]

	 RewriteRule ^user/account/tournament-transactions$ /index.php?option=com_tournamentdollars&layout=default&Itemid=6 [L]
	 RewriteRule ^user/account/tournament-transactions/([^/]*)$ /index.php?option=com_tournamentdollars&layout=default&Itemid=6&limitstart=$1 [L]
	 RewriteRule ^user/account/tournament-history$ /index.php?option=com_tournament&task=tournamenthistory [L]
	 RewriteRule ^user/account/tournament-history/([^/]*)$ /index.php?option=com_tournament&task=tournamenthistory&limitstart=$1 [L]
	 RewriteRule ^user/account/private-tournament-history$ /index.php?option=com_tournament&task=privatetournamenthistory [L]
	 RewriteRule ^user/account/private-tournament-history/([^/]*)$ /index.php?option=com_tournament&task=privatetournamenthistory&limitstart=$1 [L]

	 RewriteRule ^user/exclude$ /index.php?option=com_topbetta_user&view=myaccount&task=selfexclude [L]
	 RewriteRule ^user/refer-a-friend$ /index.php?option=com_user_referral&task=refer_friend&Itemid=6 [L]
	 RewriteRule ^user/identity-form$ /components/com_tournament/images/TopBetta_ID_check_form_v2.pdf [L]
	 RewriteRule ^user/register/([^/]*)/([^/]*)$ /index.php?option=com_topbetta_user&task=register&$1=$2 [L]
	 RewriteRule ^user/register$ /index.php?option=com_topbetta_user&task=register&Itemid=6 [L]
	 RewriteRule ^user/activate/([^/]*)$ /index.php?option=com_topbetta_user&task=activate&activation=$1&Itemid=6 [L]

	 RewriteRule ^user/resend-verification/([^/]*)$ /index.php?option=com_topbetta_user&task=resend_verification&email=$1 [L]

	 RewriteRule ^user/account/betting-limits$ /index.php?option=com_topbetta_user&task=betlimits [L]
	 RewriteRule ^user/account/betting-history$ /index.php?option=com_betting&task=bettinghistory [L]
	 RewriteRule ^user/account/betting-history/type/([^/]*)$ /index.php?option=com_betting&task=bettinghistory&result_type=$1 [L]
	 RewriteRule ^user/account/betting-history/([^/]*)$ /index.php?option=com_betting&task=bettinghistory&limitstart=$1 [L]
	 RewriteRule ^user/account/betting-history/type/([^/]*)/([^/]*)$ /index.php?option=com_betting&task=bettinghistory&result_type=$1&limitstart=$2
	 */
	// ACCOUNT ETC
	case 'getLoginHash' :
		$login_hash = new Api_User();
		echo $login_hash -> getLoginHash();
		break;

	case 'doUserLogin' :
		$user_login = new Api_User();
		echo $user_login -> doUserLogin();
		break;

	case 'doUserRegisterBasic' :
		$user_register_basic = new Api_User();
		echo $user_register_basic -> doUserRegisterBasic();
		break;

	case 'doUserRegisterTopBetta' :
		$user_register_top_betta = new Api_User();
		echo $user_register_top_betta -> doUserRegisterTopBetta();
		break;

	case 'doUserUpgradeTopBetta' :
		$user_upgrade_top_betta = new Api_User();
		echo $user_upgrade_top_betta -> doUserUpgradeTopBetta();
		break;

	case 'doUserRegisterCorporate' :
		$user_register_corporate = new Api_User();
		echo $user_register_corporate -> doUserRegisterCorporate();
		break;
	
	case 'doRequestPasswordReset' :
		$user_reset_password = new Api_User();
		echo $user_reset_password -> requestPasswordReset();
		break;
		
	case 'doConfirmPasswordReset' :
		$user_reset_password = new Api_User();
		echo $user_reset_password -> confirmPasswordReset();
		break;
		
	case 'doCompletePasswordReset' :
		$user_reset_password = new Api_User();
		echo $user_reset_password -> completePasswordReset();
		break;

	case 'doFacebookLogin' :
		$facebook_login = new Api_User();
		echo $facebook_login -> doFacebookLogin();
		break;

	case 'doUserLogout' :
		$user_logout = new Api_User();
		echo $user_logout -> doUserLogout();
		break;

	case 'checkLogin' :
		$user_login = new Api_User();
		echo $user_login -> checkLogin();
		break;

	case 'doSelfExclude' :
		$self_exclude = new Api_User();
		echo $self_exclude -> doSelfExclude();
		break;

	case 'generateJoomlaPassword' :
		$pwd = new Api_User();
		echo $pwd -> generateJoomlaPassword();
		break;		

	case 'doReferFriend' :
		$refer = new Api_User();
		echo $refer -> doReferFriend();
		break;		
		
	// PAYMENT ETC	
	case 'getBalances' :
		$funds = new Api_User();
		echo $funds -> getBalances();
		break;

	case 'doInstantDeposit' :
		$deposits = new Api_Payment();
		echo $deposits -> doInstantDeposit();
		break;

	case 'doWithdrawRequest' :
		$withdraw = new Api_Payment();
		echo $withdraw -> doWithdrawRequest();
		break;		

	case 'setBetLimit' :
		$bet_limit = new Api_Payment();
		echo $bet_limit -> setBetLimit();
		break;	
		
	case 'getUser' :
		$deposits = new Api_User();
		echo $deposits -> getUserDetails();
		break;
		
	//EXTERNAL WEBSITES 	
	case 'doUserLoginExternal' :
		$user_login = new Api_User();
		echo $user_login -> doUserLoginExternal();
		break;

	case 'doUserRegisterBasicExternal' :
		$user_register_basic = new Api_User();
		echo $user_register_basic -> doUserRegisterBasicExternal();
		break;
	
	/* DEFAULT */
	default :
		echo OutputHelper::json(500, array('error_msg' => 'Not a valid Method'));
		break;
}
?>
