<?php

function processRefundScratching($tabRaceID, $late=false){

    // set scratchin type
    $scratch_type = $late ? LATE : SCRATCHED;

      // get runners that are scratched for race
      $query  = " SELECT id, ident FROM racing_runner WHERE status = '$scratch_type' AND tab_race_id = '$tabRaceID' ";
      $scrresult = mysql_query($query);
      $numscr = mysql_num_rows($scrresult);

      l("Bet refunds - Number of {$scratch_type} found in {$tabRaceID}: {$numscr}");

      $iii = 0;
      while ($iii < $numscr) {

        // get id of scratched runner
        $runner_id =  mysql_result($scrresult,$iii,"id");
        $runner_ident = mysql_result($scrresult,$iii,"ident");

        l("Bet refunds {$scratch_type} - Working on Scratched Runner: {$runner_ident}");

        // fetch all bets
        $query = " SELECT b.resulted_flag, b.id, t.id, t.start_currency, ti.user_id, b.bet_amount FROM tbdb_tournament_racing_bet_selection as s";
        $query .= " LEFT JOIN tbdb_tournament_racing_bet as b ON b.id=s.tournament_racing_bet_id ";
        $query .= " LEFT JOIN tbdb_tournament_ticket as ti ON ti.id=b.tournament_ticket_id ";
        $query .= " WHERE s.racing_runner_id = $runner_id AND b.bet_result_status_id != (SELECT id FROM tbdb_bet_result_status WHERE name = 'fully-refunded')";
        $result = mysql_query($query);

        if($debug == DEBUG_TYPE_QUERY) {
          l("Bet refunds Bets on {$scratch_type} Query: {$query}", LOG_TYPE_DEBUG);
        }

        $num = mysql_num_rows($result);
        $i = 0;

        while ($i < $num) {

          // Get bet details
          $betID      = mysql_result($result, $i, "b.id");
          $betAmount  = mysql_result($result, $i, "b.bet_amount");
          $userID     = mysql_result($result, $i, "ti.user_id");
          $tournament_id   = mysql_result($result, $i, "t.id");
          $resulted_flag = mysql_result($result, $i, "b.resulted_flag");

          // Update status of bet to Paid
          $query  = " UPDATE tbdb_tournament_racing_bet ";
          $query .= " SET updated_date=NOW(), win_amount='$betAmount', resulted_flag=1, bet_result_status_id = (SELECT id FROM tbdb_bet_result_status WHERE name = 'fully-refunded') ";
          $query .= " WHERE id = '$betID' ";
          $queryResult = mysql_query($query);

          // if already resulted then we need to update the tournament leaderboard
          if($resulted_flag){

            l("Bet refunds - After Race Resulted scratching on: {$runner_ident} (Tournament ID: {$tournament_id}, User ID: {$userID})");

            // add bet amount back to currency
            $currency = getCurrency($userID, $tournament_id);
            $updated_currency = $currency - $betAmount;

            // update leaderboard
            updateCurrency($userID, $tournament_id, $updated_currency);
          }

          if($debug == DEBUG_TYPE_QUERY) {
            l("Bet refunds RET {$scratch_type} Bet status update: {$query}", LOG_TYPE_DEBUG);
          }

          if($debug == DEBUG_TYPE_QUERY) {
            l("BS RET {$scratch_type} Pbucks update: {$pbucksquery}", LOG_TYPE_DEBUG);
          }

           l("Bet refunds Return {$scratch_type} - Refunding Bet - UserID: {$userID}, BetID: {$betID}, Bet Amount: {$betAmount}, Old Bucks: {$myBucks}, New Bucks: {$newMyBucks}");
          $i++;
        }
        $iii++;
      }

}

function processRefundPartialPay($tab_race_id){

    // get runners that are not scratched for race
      $query  = " SELECT COUNT(*) as num_of_runners FROM racing_runner WHERE (status != '".LATE."' AND status != '".SCRATCHED."') AND tab_race_id = '$tab_race_id' ";
      $result = mysql_query($query);
      $num_of_runners = mysql_fetch_object($result)->num_of_runners;

      // if race has less than 4 runners then its partial pay
      if($num_of_runners <= 4){

        l("Bet refunds - Partial pay - place refunds");

        // select all place bets on these runners
        $query = "SELECT b.id, ti.user_id, b.bet_amount, t.id as tournament_id
              FROM tbdb_tournament_racing_bet_selection as s
          LEFT JOIN tbdb_tournament_racing_bet as b ON b.id=s.tournament_racing_bet_id
          LEFT JOIN racing_runner as r ON r.id=s.racing_runner_id
          LEFT JOIN tbdb_tournament_ticket as ti ON ti.id=b.tournament_ticket_id
          LEFT JOIN tbdb_tournament as t ON t.id=ti.tournament_id
          WHERE r.tab_race_id='$tab_race_id' AND b.resulted_flag!=1 AND b.bet_type_id=2";

        $result = mysql_query($query);
        $num_rows = mysql_num_rows($result);

        while($bet = mysql_fetch_object($result)){

          l("Bet refunds - User ID: {$bet->user_id}, Tournament #{$bet->tournament_id}");

          l("Bet refunds - Refund amount: {$bet->bet_amount}, New currency: $updated_currency");

          // refund the runners
          // Update status of bet to Paid
          $query  = " UPDATE tbdb_tournament_racing_bet ";
          $query .= " SET updated_date=NOW(), win_amount='{$bet->bet_amount}', resulted_flag=1, bet_result_status_id = (SELECT id FROM tbdb_bet_result_status WHERE name = 'fully-refunded') ";
          $query .= " WHERE id = '{$bet->id}' ";
          $queryResult = mysql_query($query);

    }

    if($num_rows < 1){
      l("Bet refunds - Partial Pay - No bets to process");
    }
  }
}

function processRefundAbandoned($tabRaceID){
  //check if there are over 50% races abandoned
  $query  = " SELECT meeting_id FROM racing_race";
  $query .= " WHERE tab_race_id = '$tabRaceID'";

  $result = mysql_query($query);
  $meetingID = mysql_fetch_object($result)->meeting_id;
  l("Bet refunds Abandoned - The Meeting ID of $tabRaceID is $meetingID");

  $totalRaceCount = countRacesByMeetingIDAndStatus($meetingID);
  l("Bet refunds Abandoned - Total Races in meeting $meetingID: $totalRaceCount ");
  $abandonedRaceCount = countRacesByMeetingIDAndStatus($meetingID, 'Abandoned');
  l("Bet refunds Abandoned - Abandoned races in meeting $meetingID: $abandonedRaceCount ");

  //if over 50% races are abandoned, the touranment will be cancelled and ticket cost will be refunded
  if($abandonedRaceCount * 2 >= $totalRaceCount) {

    //cancel tournament
    $query  = " SELECT t.id, t.buy_in, t.entry_fee, t.cancelled_flag, t.paid_flag, t.name FROM tbdb_tournament t";
    $query .= " LEFT JOIN tbdb_tournament_racing as tr ON t.id = tr.tournament_id";
    $query .= " WHERE tr.racing_meeting_id = '$meetingID'";
    $query .= " AND t.cancelled_flag = 0";
    $query .= " AND t.paid_flag = 0";

    $result = mysql_query($query);

    while($tournament = mysql_fetch_object($result)){

      l("Bet refunds Abandoned - More than 50% races are abandoned. Cancelling tournament {$tournament->id}");

      $query  = " UPDATE tbdb_tournament SET cancelled_flag = 1,";
      $query .= " cancelled_reason = 'More than 50% touranment races are abandoned'";
      $query .= " WHERE id = " . $tournament->id;
      mysql_query($query);

      if($tournament->buy_in > 0){
        l("Bet refunds Abandoned - Tournament not free. Refunding Tickets ");

        //get users in this tournament
        $query = "SELECT user_id FROM tbdb_tournament_ticket WHERE refunded_flag = 0
         AND resulted_flag = 0 AND tournament_id = {$tournament->id}";

        $users_to_refund_result = mysql_query($query);

        while($users_to_refund = mysql_fetch_assoc($users_to_refund_result)){
          //refund ticket costs
          l("Bet refunds Abandoned - Refuding ticket cost to user id: {$users_to_refund['user_id']}");
          $result_transaction_id = incrementTournamentDollars($users_to_refund['user_id'], $tournament->buy_in + $tournament->entry_fee, 'REFUND', "More than 50% races in tournament \"{$tournament->name}(#{$tournament->id})\" are abandoned. Refund tickets." );
          setResultTransactionID($tournament->id, $users_to_refund['user_id'], $result_transaction_id);
        }
        // set ticket to refunded
        $query = 'UPDATE tbdb_tournament_ticket SET refunded_flag = 1 WHERE tournament_id='.$tournament->id.'';
        mysql_query($query);
      }
    }

    //all the bets on the races in this meeting need to be refunded
    $tabRaces = array();
    $query  =" SELECT tab_race_id FROM racing_race";
    $query .=" WHERE meeting_id = '$meetingID'";

    $result = mysql_query($query);
    while($row = mysql_fetch_object($result)) {
      $tabRaces[] = $row->tab_race_id;
    }

    $tabRaceID = join('\',\'', $tabRaces);
  }

  l("Bet refunds Abandoned - Refunding play bucks");
  //get runners in the race
  $query  = " SELECT id, ident FROM racing_runner WHERE tab_race_id IN ('" . $tabRaceID . "') ";

  $runner_result = mysql_query($query);

  if($runner_result) {
    $runner_num = mysql_num_rows($runner_result);

    while ($runner = mysql_fetch_object($runner_result)) {
      // get id of runner
      $runner_id =  $runner->id;
      $runner_ident = $runner->ident;

      // fetch all bets which are not refunded
      $query = " SELECT b.resulted_flag, b.id as bet_id, t.id as tournament_id, t.start_currency, ti.user_id, b.bet_amount FROM tbdb_tournament_racing_bet_selection as s";
      $query .= " LEFT JOIN tbdb_tournament_racing_bet as b ON b.id=s.tournament_racing_bet_id ";
      $query .= " LEFT JOIN tbdb_tournament_ticket as ti ON ti.id=b.tournament_ticket_id ";
      $query .= " LEFT JOIN tbdb_tournament as t ON t.id=ti.tournament_id ";
      $query .= " WHERE s.racing_runner_id = $runner_id AND b.bet_result_status_id != (SELECT id FROM tbdb_bet_result_status WHERE name = 'fully-refunded')";

      $result = mysql_query($query);

      if($result) {
        while ($bet = mysql_fetch_object($result)) {
          // Get bet details
          $betID      = $bet->bet_id;
          $betAmount  = $bet->bet_amount;
          $userID     = $bet->user_id;
          $tournament_id   = $bet->tournament_id;
          $resulted_flag = $bet->resulted_flag;

          // Update status of bet to Paid
          $query  = " UPDATE tbdb_tournament_racing_bet ";
          $query .= " SET updated_date=NOW(), win_amount='$betAmount', resulted_flag=1, bet_result_status_id = (SELECT id FROM tbdb_bet_result_status WHERE name = 'fully-refunded') ";
          $query .= " WHERE id = '$betID' ";
          $queryResult = mysql_query($query);

          // if already resulted then we need to update the tournament leaderboard
          if($resulted_flag){

            // add bet amount back to currency
            $currency = getCurrency($userID, $tournament_id);
            $updated_currency = $currency - $betAmount;

            // update leaderboard
            updateCurrency($userID, $tournament_id, $updated_currency);
          }

          l("Bet refunds Abandoned - Refunding Bet - UserID: {$userID}, BetID: {$betID}, Bet Amount: {$betAmount}");
        }
      }
      else {
        l("Bet refunds Abandoned - No bets on {$runner_ident} in race '{$tabRaceID}'");
      }
    }
  }
  else {
    l("Bet refunds Abandoned - No runners data in race '{$tabRaceID}'");
  }
}

function countRacesByMeetingIDAndStatus($meetingID, $status = null){
  $query  =" SELECT count(*) as count FROM racing_race";
  $query .=" WHERE meeting_id = $meetingID";
  if(!empty($status)){
    $query .=" AND status = '$status'";
  }

  $result = mysql_query($query);
  $row = mysql_fetch_object($result);

  return $row->count;
}

