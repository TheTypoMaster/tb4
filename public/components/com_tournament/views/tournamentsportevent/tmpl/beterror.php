<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="mainbdr2">
  <div id="bettixPopWrap">
    <div class="bettixPanel">
      <div class="bettixHead">
        <div class="bettixHeadTitle">Bet Confirmation - <?php print $this->escape($this->tournament_name); ?></div>
        <div class="bettixHeadRace">Match <?php print $this->escape($this->match_name); ?></div>
      </div>
      <div class="bettixMessage">
        <div class="bettixMessageTxt error"><?php echo $this->escape($this->error) ?></div>
      </div>
      <div class="buttwrap">
        <input id="closepoptix" type="button" name="cancelBets" value="PLEASE ADJUST YOUR BET" onclick="$('sbox-window').close();"/>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>