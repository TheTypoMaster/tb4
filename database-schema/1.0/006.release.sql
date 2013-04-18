-- indexes release

-- tbdb_tournament
ALTER TABLE
  `tbdb_tournament`
ADD INDEX ( `tournament_sport_id` ),
ADD INDEX ( `parent_tournament_id` ),
ADD INDEX ( `name` ),
ADD INDEX ( `start_currency` ),
ADD INDEX ( `start_date` ),
ADD INDEX ( `end_date` ),
ADD INDEX ( `jackpot_flag` ),
ADD INDEX ( `paid_flag` ),
ADD INDEX ( `cancelled_flag` ),
ADD INDEX ( `status_flag` ),
ADD INDEX ( `created_date` ),
ADD INDEX ( `updated_date` );

-- tbdb_tournament_bet_limit
ALTER TABLE
  `tbdb_tournament_bet_limit`
ADD INDEX ( `tournament_id` ),
ADD INDEX ( `bet_type_id` );

-- tbdb_tournament_places_paid
ALTER TABLE
  `tbdb_tournament_places_paid`
ADD INDEX ( `entrants` );

-- tbdb_tournament_buyin
ALTER TABLE
  `tbdb_tournament_buyin`
ADD INDEX ( `buy_in` ),
ADD INDEX ( `entry_fee` ),
ADD INDEX ( `status_flag` );

-- tbdb_tournament_racing
ALTER TABLE
  `tbdb_tournament_racing`
ADD INDEX ( `tournament_id` ),
ADD INDEX ( `racing_meeting_id` );

-- tbdb_tournament_racing_bet
ALTER TABLE
  `tbdb_tournament_racing_bet`
ADD INDEX ( `tournament_ticket_id` ),
ADD INDEX ( `racing_race_id` ),
ADD INDEX ( `bet_type_id` ),
ADD INDEX ( `bet_amount` ),
ADD INDEX ( `win_amount` ),
ADD INDEX ( `resulted_flag` ),
ADD INDEX ( `bet_result_status_id` ),
ADD INDEX ( `created_date` ),
ADD INDEX ( `updated_date` );

-- tbdb_tournament_racing_bet_selection
ALTER TABLE
  `tbdb_tournament_racing_bet_selection`
ADD INDEX ( `tournament_racing_bet_id` ),
ADD INDEX ( `racing_runner_id` );

-- tbdb_tournament_sport
ALTER TABLE
  `tbdb_tournament_sport`
ADD INDEX ( `name` ),
ADD INDEX ( `status_flag` ),
ADD INDEX ( `created_date` ),
ADD INDEX ( `updated_date` );

-- tbdb_tournament_ticket
ALTER TABLE
  `tbdb_tournament_ticket`
ADD INDEX ( `tournament_id` ),
ADD INDEX ( `user_id` ),
ADD INDEX ( `entry_fee_transaction_id` ),
ADD INDEX ( `buy_in_transaction_id` ),
ADD INDEX ( `result_transaction_id` ),
ADD INDEX ( `refunded_flag` ),
ADD INDEX ( `resulted_flag` ),
ADD INDEX ( `created_date` ),
ADD INDEX ( `resulted_date` );
