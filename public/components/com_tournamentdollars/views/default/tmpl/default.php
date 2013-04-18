<?php defined('_JEXEC') or die('Restricted access');

$selectedDate = date('d / m / Y');
$openingBal = '';
$closingBal = '';
?>



<div id="bettaWrap" >
  <div class="moduletable">
    <h3>My Account</h3>
    <div class="innerWrap">
      <div class="hdrBar"><div id="hdrBar_trans"></div><span class="transaction_title">Tournament Transactions</span><div id="date_select"><span class="date_select_txt">DATE: </span><?php echo $selectedDate; ?></div></div>

<?php
if( $this->transactions )
{
?>
<div style="text-align:right"><?php echo $this->page->getPagesCounter();?></div>
<table id="receipt" border="1" class="mytrans" width="100%">
  <tr>
    <th>TRANS. No.</th>
    <th>TIME</th>
    <th>TRANSACTION DESCRIPTION</th>
    <th>VALUE ($)</th>
    <th>TYPE</th>
  </tr>

<?php
  foreach( $this->transactions as $transaction )
  {
?>
  <tr>
    <td><?php echo htmlspecialchars($transaction->id) ?></td>
    <td><?php echo htmlspecialchars($transaction->created_date) ?></td>
<?php
    $ticket = null;
    $tournament = null;
    if($transaction->buy_in_transaction_id )
    {
      $ticket = $transaction->tournament_id;
      $tournament = $transaction->tournament;
      $sport = in_array($transaction->sport_name, $this->racing_sports) ? 'racing' : 'sports';
    }
    if($transaction->entry_fee_transaction_id )
    {
      $ticket = $transaction->tournament_id2;
      $tournament = $transaction->tournament2;
      $sport = in_array($transaction->sport_name2, $this->racing_sports) ? 'racing' : 'sports';
    }
    if($transaction->result_transaction_id )
    {
      $ticket = $transaction->tournament_id3;
      $tournament = $transaction->tournament3;
      $sport = in_array($transaction->sport_name3, $this->racing_sports) ? 'racing' : 'sports';
    }
    if( $ticket )
    {
    	$tra
?>
    <td><a href="/tournament/<?php echo $sport ?>/game/<?php echo htmlspecialchars($ticket) ?>"><?php echo htmlspecialchars($tournament ? $tournament : $transaction->description) ?></a></td>
<?php
    }
    else if( $transaction->friend_username )
    {
?>
      <td>Referral payment for user "<?php echo htmlspecialchars($transaction->friend_username) ?>"</td>
<?php
    }
    else
    {
?>
    <td><?php echo htmlspecialchars($transaction->description);
				if ($transaction->bet_entry_id && isset($this->transactions_description[$transaction->bet_entry_id][$transaction->id])) 
				echo '<br>' . $this->transactions_description[$transaction->bet_entry_id][$transaction->id]; ?></td>
<?php
    }
?>
    <td>
<?php
    if( $transaction->amount == 0 )
    {
      //we shouldn't create transactions records with amount equals to 0,
      //but we need to deal with it just in case it happens
      echo '<div>' . sprintf('$%.2f' , abs($transaction->amount/100)) . '</div>';
    }
    else if( $transaction->amount > 0 )
    {
      echo '<div class="positive">' . sprintf('$%.2f' , abs($transaction->amount/100)) . '</div>';
    }
    else
    {
      echo '<div class="negative">' . sprintf('$%.2f' , abs($transaction->amount/100)) . '</div>';
    }
?>
    </td>
    <td>
<?php
    if( $transaction->amount > 0 )
    {
      echo 'Deposit -';
    }
    if( $transaction->amount < 0 )
    {
      echo 'Withdrawal -';
    }
    echo htmlspecialchars($transaction->type);
?>
    </td>
  </tr>
<?php
  }
?>
</table>

<?php
   echo preg_replace('/\/index.php\?option=com_tournamentdollars&amp;layout=default&amp;Itemid=6(&amp;limitstart=){0,1}/s', '/user/account/tournament-transactions/', $this->page->getPagesLinks());
?>
<br />
<br />
<?php
}
else
{
?>
  <p>There are no transactions to list.</p>
<?php
}
?>
    </div>
  </div>
</div>
