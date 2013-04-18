<?php
/*
	There was a bug in tournament processor (since 2.0), which assigned the wrong tournament transaction
	types when awarding a winning Jackpot ticket.
	
	This script is to correct the those transaction types to relevant buyin or entry fee.
 */

include_once('../common/lib/configReader.php');
include_once('../common/lib/dbConnections.php');
include_once('../common/lib/logFunctions.php');

// Connect to DB
$localdbhandle = dbConnectionFactory('topbetta_application', 'connect');

l('Getting transaction type for entry fee');
$entry_type_sql = '
	SELECT * FROM tbdb_tournament_transaction_type WHERE keyword = "entry";
';
$entry_type = mysql_query($entry_type_sql);
$entry = mysql_fetch_array($entry_type, MYSQL_ASSOC);

l('Getting transaction type for buy in');
$buyin_type_sql = '
	SELECT * FROM tbdb_tournament_transaction_type WHERE keyword = "buyin";
';
$buyin_type = mysql_query($buyin_type_sql);
$buyin = mysql_fetch_array($buyin_type, MYSQL_ASSOC);

l('Getting the records which have the wrong transaction types');
$tourn_trans_sql = '
	SELECT id FROM tbdb_tournament_transaction WHERE tournament_transaction_type_id = 3 AND amount < 0;
';

$count = 0;
$tourn_trans = mysql_query($tourn_trans_sql);
while ($row = mysql_fetch_array($tourn_trans, MYSQL_ASSOC))
{
	$trans_id = $row['id'];
	
	$enry_sql = '
		SELECT
			tk.id
		FROM 
			tbdb_tournament_ticket AS tk
		INNER JOIN
			tbdb_tournament AS t
		ON
			tk.tournament_id = t.id
		WHERE
			t.jackpot_flag = 1
		AND
			tk.entry_fee_transaction_id = ' . $trans_id;
	
	$entry_trans = mysql_query($enry_sql);
	if (mysql_fetch_array($entry_trans, MYSQL_ASSOC)) {
		$new_trans_id	= $entry['id'];
		$new_notes		= $entry['description'];
	} else {
		$new_trans_id	= $buyin['id'];
		$new_notes		= $buyin['description'];
	}
	
	$update_sql = '
		UPDATE
			tbdb_tournament_transaction
		SET
			tournament_transaction_type_id	= ' . $new_trans_id . ',
			notes							= "' . $new_notes . '"
		WHERE
			id = ' . $trans_id;
	mysql_query($update_sql);
	
	$count++;
}
l( "Updated $count records\n");

//close connection
dbConnectionFactory('topbetta_application', 'disconnect', $localdbhandle );
?>
