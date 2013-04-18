<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="ticketWrap">
<div id="selectTournWrap">
  <div id="selectTourn">
    <div id="selectTournIn">
        <div class="selectTournLogo">
        	<img src="/components/com_tournament/images/<?php echo $this->escape($this->tournament->image); ?>.png" alt="" border="0" width="68" height="98">
        </div>

        <div class="selectTournInfo">
          <div class="selectTournName"><?php echo $this->tournament->name; ?></div>
		  <div class="selectTournType"><?php echo $this->escape(ucwords($this->tournament->sport_name))?></div>
          <div class="selectTournGame"><?php echo $this->tournament->gameplay; ?></div>
        </div>
        <div class="clr"></div>

        <div class="selectTournError">
          <div class="error">
            <?php print $this->error; ?>
          </div>
        </div>
    </div>
  </div>
</div>
</div>