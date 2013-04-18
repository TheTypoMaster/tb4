<?php
/*
 Buying a ticket, usually has two kinds of transactions realated : "buyin" and "entry". When a user buys a ticket and doesn't
 have sufficient tournament dollars, the required money will be transferred from account balance to tournament balance. However,
 both of them are using "tournamentdollars" as the transaction type, which cause the issue that we can't calculate the total amount
 for "buyin" and "entry" transactions(ISSUE: 7487 "Top betta - need to split entry fee and buy ins in account transactions" 8/12/2010).

 To resolve this problem, we've set up new account transaction type "buyin" and "entry".

 This task will separate the existing "tournamentdollars" transaction records to "buyin" and "entry" by tracking down account transaction
 history and tournament transaction history.
 */

include_once('../common/lib/configReader.php');
include_once('../common/lib/dbConnections.php');
include_once('../common/lib/logFunctions.php');

// Connect to DB
$localdbhandle = dbConnectionFactory('topbetta_application', 'connect');

//update all the account transctions of "tournament dollars" and notes like "Tournament Entry Fee"
l( "Updating account transactions with notes 'Tournament Entry Fee' to new type - 'entry'...\n");
$update_entry_sql = 'UPDATE `tbdb_account_transaction`
  SET account_transaction_type_id = "11"
  WHERE account_transaction_type_id = 1
  AND notes LIKE "Tournament Entry Fee%"
  ';
mysql_query($update_entry_sql);
$updated_entry_count = mysql_affected_rows();
l( "Updated $updated_entry_count records\n" );

//update all the account transctions of "tournament dollars" and notes like "Tournament Buy In"
l( "Updating account transactions with notes 'Tournament Buy In' to new type - 'buyin'...\n");
$update_buyin_sql = 'UPDATE `tbdb_account_transaction`
  SET account_transaction_type_id = "12"
  WHERE account_transaction_type_id = 1
  AND notes LIKE "Tournament Buy In%"
  ';
mysql_query($update_buyin_sql);
$updated_buyin_count = mysql_affected_rows();
l( "Updated $updated_buyin_count records\n" );


//find all the account transctions of "tournament dollars" and notes is "Account Balance is being spent on Tournament Dollars."
l( "Updating account transactions with notes 'Account Balance is being spent on Tournament Dollars' to the relevant new type ...\n");
$account_trans_sql = 'SELECT * FROM `tbdb_account_transaction`
  WHERE amount < 0 AND account_transaction_type_id = 1
  AND notes="Account Balance is being spent on Tournament Dollars."';
$account_trans = mysql_query($account_trans_sql);

$count = 0;
while( $row = mysql_fetch_array($account_trans, MYSQL_ASSOC) )
{
  //check tournament transaction record to find the relavant record
  $tourn_trans_sql = 'SELECT * FROM `tbdb_tournament_transaction`
    WHERE amount = "' . abs($row['amount']) . '"
    AND tournament_transaction_type_id = 8
    AND recipient_id = "' . $row['recipient_id'] . '"
    AND (
      created_date = "' .  $row['created_date'] . '"
      OR created_date = DATE_ADD("' . $row['created_date'] . '", INTERVAL 1 SECOND)
    )';

  $tourn_trans = mysql_query( $tourn_trans_sql );
  if($tourn_trans_row = mysql_fetch_array($tourn_trans, MYSQL_ASSOC))
  {
    //check the next record of this relavant record.
    //if the record is for the same user in the same time, it's the tournament record that we need to check the type with
    $tourn_dollars_sql = 'SELECT * FROM `tbdb_tournament_transaction`
      WHERE id ="' . ($tourn_trans_row['id'] + 1) . '"
      AND recipient_id = "' . $row['recipient_id'] . '"
      AND (
        created_date = "' .  $row['created_date'] . '"
        OR created_date = DATE_ADD("' . $row['created_date'] . '", INTERVAL 1 SECOND)
      )';
    
    $tourn_dollars_trans = mysql_query( $tourn_dollars_sql );
    if($tourn_dollars_row = mysql_fetch_array($tourn_dollars_trans, MYSQL_ASSOC))
    {
      switch( $tourn_dollars_row['tournament_transaction_type_id'])
      {
        case 1:
          //update current to entry type
          $update_sql = 'UPDATE `tbdb_account_transaction` SET account_transaction_type_id = "11" WHERE id="' . $row['id'] . '"';
          mysql_query($update_sql);
          $count++;
          break;
        case 2:
          //update current to buyin type
          $update_sql = 'UPDATE `tbdb_account_transaction` SET account_transaction_type_id = "12" WHERE id="' . $row['id'] . '"';
          mysql_query($update_sql);
          $count++;
          break;
      }
    }
    
  }
}
l( "Updated $count records\n");
l( "Total updated " . ($updated_entry_count+$updated_buyin_count+$count) . " account transaction records\n\n");

//close connection
dbConnectionFactory('topbetta_application', 'disconnect', $localdbhandle );
?>
