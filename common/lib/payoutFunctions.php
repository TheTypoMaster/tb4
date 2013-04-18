<?php
/**
 * Get transaction type data for an account transaction
 *
 * @param string $keyword
 * @return object
 */
function getAccountTransactionTypeByKeyword($keyword)
{
  $query =
    "SELECT
      id,
      description
    FROM
      tbdb_account_transaction_type
    WHERE
      keyword = '{$keyword}'";

  $result = mysql_query($query);
  return mysql_fetch_object($result);
}

/**
 * Add funds to a user's account balance
 *
 * @param integer $user_id
 * @param integer $amount
 * @param string $keyword
 * @param string $notes
 * @return boolean
 */
function incrementAccountBalance($user_id, $amount, $keyword, $notes = null)
{
  $type_data = getAccountTransactionTypeByKeyword($keyword);
  if(is_null($notes)) {
    $notes = $type_data->description;
  }

  $query =
    "INSERT INTO tbdb_account_transaction (
      recipient_id,
      giver_id,
      session_tracking_id,
      account_transaction_type_id,
      amount,
      notes,
      created_date
    ) VALUES (
      '{$user_id}',
      0,
      0,
      {$type_data->id},
      {$amount},
      '{$notes}',
      NOW()
    )";

  $result = mysql_query($query);
  return mysql_insert_id();
}

/**
 * Get transaction type data for a tournament transaction
 *
 * @param string $keyword
 * @return object
 */
function getTournamentTransactionTypeByKeyword($keyword)
{
  $query =
    "SELECT
      id,
      description
    FROM
      tbdb_tournament_transaction_type
    WHERE
      keyword = '{$keyword}'";

  $result = mysql_query($query);
  return mysql_fetch_object($result);
}

/**
 * Add funds to a user's tournament balance
 *
 * @param integer $user_id
 * @param integer $amount
 * @param string $keyword
 * @param string $notes
 * @return boolean
 */
function incrementTournamentDollars($user_id, $amount, $keyword, $notes = null)
{
  $type_data = getTournamentTransactionTypeByKeyword($keyword);
  if(is_null($notes)) {
    $notes = $type_data->description;
  }

  $query =
    "INSERT INTO tbdb_tournament_transaction (
      recipient_id,
      giver_id,
      session_tracking_id,
      tournament_transaction_type_id,
      amount,
      notes,
      created_date
    ) VALUES (
      '{$user_id}',
      0,
      0,
      {$type_data->id},
      {$amount},
      '{$notes}',
      NOW()
    )";

  $result = mysql_query($query);
  return mysql_insert_id();
}

/**
 * Deduct funds from a user's tournament balance
 *
 * @param integer $user_id
 * @param integer $amount
 * @param string $keyword
 * @param string $notes
 * @return boolean
 */
function decrementTournamentDollars($user_id, $amount, $keyword, $notes = null)
{
  return incrementTournamentDollars($user_id, ($amount * -1), $keyword, $notes);
}

/**
 * Get a ticket ID for a tournament and user
 *
 * @param integer $tournament_id
 * @param integer $user_id
 * @return integer
 */
function getTicketID($tournament_id, $user_id, $include_refunded=true)
{
  $ticket_id = null;
  $query =
    "SELECT
      id
    FROM
      tbdb_tournament_ticket
    WHERE
      tournament_id = {$tournament_id}
    AND
      user_id = {$user_id}";
  if(!$include_refunded)
  {
    $query .=
      " AND refunded_flag = 0";
  }

  $result = mysql_query($query);
  
  if($ticket = mysql_fetch_object($result)){
    $ticket_id = $ticket->id;
  }
  
  return $ticket_id;
}

/**
 * Set the transaction ID for a tournament payout
 *
 * @param integer $ticket_id
 * @param integer $user_id
 * @param integer $transaction_id
 * @return boolean
 */
function setResultTransactionID($tournament_id, $user_id, $transaction_id)
{
  $ticket_id = getTicketID($tournament_id, $user_id);

  $query =
    "UPDATE
      tbdb_tournament_ticket
    SET
      result_transaction_id = {$transaction_id}
    WHERE
      id = {$ticket_id}";

  return mysql_query($query);
}

/**
 * Email notification to winners
 *
 * @param integer $user_id
 * @param integer $tournament_id
 * @return void
 */

function sendWinnerNotification($user_id, $tournament_name, $amount, $parent_tournament ='' ){

  $mail = "Congratulations %s! You're a winner in Topbetta's %s tournament.";

  $query =
    "SELECT
      username, email
    FROM
      tbdb_users
    WHERE
      id = {$user_id}";

    $result = mysql_query($query);
    $user =  mysql_fetch_object($result);

  if($parent_tournament == ''){ // cash pay out notification
    $mail .= " You've won $%s cash! This amount has been credited to your Account Balance.";
    $mail .= " Remember, to withdraw your cash you need to provide us with the Identification Document.\n\n";


  } else { // ticket awarded notification
    $mail .= " You've won";
    if($amount == 0){
      $mail .= " a ticket to the next tournament round: $parent_tournament.\n\n";
    }
    else{
      $mail .= " $%s in Tournament Dollars. These can be used to enter more tournaments on Topbetta and compete for cash prizes.\n\n";
    }
  }

  $mail .= "Login to check out the details https://www.topbetta.com \n\n";
  $mail .= "Thanks for playing Topbetta!\n\n";
  $mail .= "Regards,\n\n";
  $mail .= "The TopBetta Team\n";
  $mail .= "help@topbetta.com\n\n";
  
  //get terms and conditions from user component
  $terms = getTermsAndConditions();
  
  if(empty($terms)){
    $terms = "Terms and Conditions - http://www.topbetta.com/content/article/2\n";
    $terms .= "Any prices and times contained in this email are correct at the time of publication.\n\n";
    $terms .= "This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity it is addressed. If you have received this email in error, please notify TopBetta Customer Service. The recipient should check any attachments in this email for the presence of viruses. TopBetta accepts no liability for any damage caused by any virus.\n";
    $terms .= "If you feel you may have a problem with gambling, click here: http://www.topbetta.com/help/5. If you want to talk to someone who can help with information, counselling and referral, call G-Line Australia on 1800 858 858 or Lifeline on 131114.\n";
  }
  $mail .= $terms;
  
  $mail = sprintf($mail, $user->username, $tournament_name, number_format($amount/100,2));
  
  $headers = 'From: no-reply@topbetta.com' . "\r\n" .
    'Reply-To: no-reply@topbetta.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  mail($user->email, 'Topbetta Winner Notification', $mail, $headers);
}

/**
 * Get a terms and condition text from user component parameter
 *
 * @return string the content of terms and conditions
 */
function getTermsAndConditions() {
  static $terms = null;

  if(is_null($terms)) {
    $query =
    "SELECT
      params
    FROM
      tbdb_components
    WHERE
      id = 67";
    $result = mysql_query($query);
    if($result){
      $row = mysql_fetch_object($result);
      if(preg_match("/disclaimerText=([^\\n]*)/s", $row->params, $m)){
        $terms = str_replace('\n', "\n", $m[1]);
      }
    }
  }

  return $terms;
}
