<?php


function getCurrency($user_id, $tournament_id){

	$query  = " SELECT currency FROM tbdb_tournament_leaderboard WHERE user_id = '$user_id' AND tournament_id = '$tournament_id'";
    $result = mysql_query($query);
    $amount = mysql_result($result, 0, "currency");

    return $amount;
}

function updateCurrency($user_id, $tournament_id, $new_amount){

    $query  = " UPDATE tbdb_tournament_leaderboard SET currency = '$new_amount' WHERE user_id = '$user_id' AND tournament_id = '$tournament_id'";
    $result =  mysql_query($query);

}
