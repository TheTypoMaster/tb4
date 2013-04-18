<?php

function processBet($type, $runnerArray, $runnerDivArray, $tabRaceID){

	foreach ($runnerArray as $key => $runner ) {

		switch($type){
			case TRIFECTA:
				// Get Tri Runners
	          	$triRunnArray = explode("  " ,$runner);
	          	$triRunner1 = $triRunnArray[0];
	          	$triRunner2 = $triRunnArray[1];
	          	$triRunner3 = $triRunnArray[2];

	            // Get all trifectas bets on race
	            $query  = " SELECT b.id, bp.ticket_id as ticketID, bp.user_id, bp.bet_amount, b.selections, bp.id, bp.percent ";
	            $query .= " FROM jos_ucbetman_tournament_bet_parent AS bp " ;
	            $query .= " LEFT JOIN jos_ucbetman_tournament_bets as b ON b.bet_parent = bp.id ";
	            $query .= " WHERE bp.paid = '0' AND bp.bet_type = 'trifecta' AND bp.tab_race_id = '$tabRaceID' ";
	            $query .= " AND selectionsA LIKE '%,$triRunner1,%' AND selectionsB LIKE '%,$triRunner2,%'  AND selectionsC LIKE '%,$triRunner3,%'";

				break;
			case QUINELLA:
				// Get Quin Runners
          		$quinSpace = strpos($runner, " ");
          		$quinRunner1 = trim(substr($runner, 0, $quinSpace));
          		$quinRunner2 = trim(substr($runner, $quinSpace+1));

          		// Get all quinella bets on race
          		$query  = " SELECT b.id, bp.ticket_id as ticketID, bp.user_id, bp.bet_amount, b.selections, bp.id, bp.percent ";
          		$query .= " FROM jos_ucbetman_tournament_bet_parent AS bp " ;
          		$query .= " LEFT JOIN jos_ucbetman_tournament_bets as b ON b.bet_parent = bp.id ";
          		$query .= " WHERE bp.paid = '0' AND bp.bet_type = 'quinella' AND bp.tab_race_id = '$tabRaceID' "; // AND b.selections LIKE '%,$runnerSecond,%'";
          		$query .= " AND selectionsA LIKE '%,$quinRunner1,%' AND selectionsB LIKE '%,$quinRunner2,%'";
				break;
			case WIN:
			case PLACE:
			default:

				// select all win/place runners
				$query = " SELECT rr.id, b.id, b.tournament_ticket_id as ticketID, ti.user_id, b.bet_amount, b.win_amount, ti.tournament_id  ";
				$query .= " FROM tbdb_tournament_racing_bet_selection AS s ";
				$query .= " LEFT JOIN racing_runner AS rr ON s.racing_runner_id = rr.id ";
				$query .= " LEFT JOIN tbdb_tournament_racing_bet AS b ON b.id = s.tournament_racing_bet_id ";
				$query .= " LEFT JOIN tbdb_tournament_ticket AS ti ON ti.id = b.tournament_ticket_id ";
				$query .= " WHERE b.bet_type_id=(SELECT id FROM tbdb_bet_type WHERE name = '$type') AND b.resulted_flag!=1 ";
				$query .= " AND rr.tab_race_id = '$tabRaceID' AND rr.number = '$runner' ";
				break;
		}

		$result = mysql_query($query);
		$num = mysql_num_rows($result);

		if($debug == DEBUG_TYPE_QUERY) {
			l("PAY PC - Place Bets on $type Query: {$query}", LOG_TYPE_DEBUG);
		}

		$i = 0;
		while ($i < $num) { // Loop on 2nd place bets

			// Get bet details
			$betID = mysql_result($result,$i,"b.id");
			$ticketID = mysql_result($result,$i,"ticketID");
			$betAmount = mysql_result($result,$i,"b.bet_amount");
			$tournament_id = mysql_result($result,$i,"ti.tournament_id");
			//$betPercentage = mysql_result($result,$i,"bp.percent");

			// Set ODDS for bet based on odds_type field in ATP meeting
			switch ($oddsType) {
				case NSW_TAB:
				default:
					$betOdds = $runnerDivArray[$key];
					break;
			}
			$userID = mysql_result($result,$i,"ti.user_id");

			// Calculate win amount
			switch($type){
				case QUINELLA:
				case TRIFECTA:
            		$betWin = ($betPercentage / 100 ) * $betOdds;
            		break;
				case WIN:
				case PLACE:
				default:
					$betWin = $betAmount * $betOdds;
					break;
			}

			// Pay winnings into users pbucks
			$currency = getCurrency($userID, $tournament_id);

			$updated_currency = ($currency - $betAmount) + $betWin;

			// Update status of bet to Paid
			$query  = " UPDATE tbdb_tournament_racing_bet ";
			$query .= " SET updated_date=NOW(), resulted_flag=1, bet_result_status_id = (SELECT id FROM tbdb_bet_result_status WHERE name = 'paid'), win_amount= $betWin ";
			$query .= " WHERE id = '$betID' ";
			$queryResult = mysql_query($query);


			// Update pbucks value for user
			updateCurrency($userID, $tournament_id, $updated_currency);

			if($debug == DEBUG_TYPE_QUERY) {
				l("PAY PC - PBUCKS UPDATE: {$pbucksquery}", LOG_TYPE_DEBUG);
			}

			l("PAY PC - Working on $type - Ticket: {$ticketID}, UserID: {$userID}, BetID: {$betID}, Bet Amount: {$betAmount}, Bet Odds: {$betOdds}, Bet Win: {$betWin}, Old Bucks: {$myBucks}, New Bucks: {$newMyBucks}");
			$i++;
		}
	}
}