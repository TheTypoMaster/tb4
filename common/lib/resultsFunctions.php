<?php

function getDividend($runnerPosition, $runnerNumber, $raceDividends){
  foreach ($raceDividends as $raceDividend){
    //echo "#TAB RES - Race Divs :$raceDividend \n";
    if ( $raceDividend['!ID'] == $runnerPosition && $raceDividend['Selections'] == "$runnerNumber"){
      return $raceDividend['Dividend'];
    }
  }
}

function getRunnerName($raceID, $runnerNumber) {
  $runnerNameQuery  = " SELECT r.name";
  $runnerNameQuery .= " FROM race AS e ";
  $runnerNameQuery .= " LEFT JOIN runner AS r ON e.id = r.race_id ";
  $runnerNameQuery .= " WHERE e.tab_race_id = '$raceID' AND r.number = '$runnerNumber'";
  $runnerName = mysql_query($runnerNameQuery);
  $runnerName = mysql_result($runnerName,0,"r.name");
  $runnerName = str_replace ("'", "\'", $runnerName);
  return $runnerName;
}