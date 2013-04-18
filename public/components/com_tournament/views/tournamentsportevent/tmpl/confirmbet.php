<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
  <div class="mainbdr2">
    <div id="bettixPopWrap">
      <form action="/index.php" name="atpBetForm" id="atpBetForm" method="POST">
        <input type="hidden" name="option" value="com_tournament" />
        <input type="hidden" name="controller" value="tournamentsportevent" />
        <input type="hidden" name="task" value="savebet" />
        <input type="hidden" name="id" value="<?php print $this->tournament->id; ?>" />
        <input type="hidden" name="match_id" value="<?php print $this->match->tournament_match_id; ?>" />
        

        <div class="bettixPanel">
          <div class="bettixHead">
            <div class="bettixHeadTitle">Tournament Bet Confirmation</div>
            <div class="bettixHeadRace"><?php echo $this->escape($this->header) ?></div>
          </div>

          <div class="bettixTpanel">
            <table width="100%">
              <tr>
                <td class="selHead">BET TYPE</td>
                <td class="selHead lft">SELECTION</td>
                <td class="selHead">AMOUNT</td>
                <td class="selHead">ODDS</td>
                <td class="selHead">SOURCE</td>
                <td class="selHead">TO WIN</td>
              </tr>

<?php
$class = null;
foreach( $this->bet_rows as $row)
{
  $class = (is_null($class) || $class == 2) ? 1 : 2;
?>

            <tr>
              <td class="sel cap w60"><?php echo $this->escape($row['market_name']); ?></td>
              <td class="sel lft"><?php echo $this->escape($row['offer_name']); ?></td>
              <td class="sel rgt w60"><?php echo $this->escape($row['amount']); ?></td>
              <td class="sel w60"><span<?php echo $row['odds_class'] ?>><?php echo $this->escape($row['odds']); ?><?php echo $this->escape($row['odds_text'])?></span></span></td>
              <td class="sel w60">UNiTAB</td>
              <td class="sel w60"><?php echo $this->escape($row['win']); ?></td>
            </tr>

<?php

  $class = ($class == 1) ? 2 : 1;
}
?>
          </table>
        </div>
<?php if($this->odds_updated) {?>
		<div class="bettixNotes">PLEASE NOTE: Some odds prices have updated. Changes noted above by *NEW!</div>
<?php }?>
        <div class="bettixFoot">TOTAL BET FOR THIS TICKET: <?php echo $this->display_total; ?></div>
        <div class="bettixFootMsg">Bet cannot be cancelled. To continue to place your bets click SUBMIT BET.</div>
        <input id="cancelBets" type="button" name="cancelBets" value="CANCEL" onclick="$('sbox-window').close(); "/>
        <input id="confirmBets" type="submit" name="confirmBets" value="SUBMIT BET" onclick="this.disabled=true,this.form.submit();" />
        <div class="clear"></div>
      </div>
    </form>
    <div class="clear"></div>
  </div>
</div>