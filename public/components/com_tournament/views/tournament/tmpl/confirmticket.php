<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="ticketWrap">
<div id="selectTournWrap">
  <div id="selectTourn">
    <div id="selectTournIn">
      <form action="/index.php" target="_parent" method="post" name="ticketForm">
        <input type="hidden" name="option" value="com_tournament">
        <input type="hidden" name="controller" value="tournamentsportevent">
        <input type="hidden" name="task" value="saveticket">
        <input type="hidden" name="id" value="<?php echo $this->tournament->id; ?>">

        <div class="selectTournLogo">
          <img src="/components/com_tournament/images/<?php echo $this->escape($this->tournament->image); ?>.png" alt="" border="0" width="68" height="98">
        </div>

        <div class="selectTournInfo">
          <div class="selectTournName"><?php echo $this->escape($this->tournament->name); ?></div>
          <div class="selectTournType"><?php echo $this->escape(ucwords($this->tournament->sport_name))?> <?php echo isset($this->tournament->competition_name) ? "&ndash;".$this->escape($this->tournament->competition_name) : ''?></div>
          <div class="selectTournGame"><?php echo $this->escape($this->tournament->gameplay); ?></div>
        </div>
        <div class="clr"></div>
        <div class="selectTournGameTime">
          <?php echo $this->tournament->display_time; ?>
          <?php echo $this->tournament->display_counter; ?>
        </div>

        <div class="selectTournConfrim">
          <div id="confirmChk" onclick="confirmToggle();">
            <a href="#">&nbsp;</a>
          </div>
          <div class="selectTournConfrimTxt" onclick="confirmToggle();">CLICK TO CONFIRM PURCHASE</div>
          <input id="playButt" class="confirmButt" type="submit" disabled="disabled" value="" onclick="submitTicket(this);" />
          <div class="clr"></div>
        </div>

        <div class="selectTournDetails">
          <div class="selectTournDetailsL">
            <div class="selectTournTR">Date: <?php echo $this->tournament->display_date; ?></div>
            <div class="selectTournTR">Tourn ID: <?php echo $this->tournament->id; ?></div>
            <div class="selectTournTL">Cost: <?php echo $this->tournament->display_value; ?></div>
          </div>
          <div class="selectTournDetailsR">
            <div class="selectTournTR">Entrants: <?php echo $this->tournament->entrants; ?></div>
            <div class="selectTournTR">Places Paid: <?php echo $this->tournament->place_count; ?></div>
            <div class="selectTournTL">Prize Pool: <?php echo $this->tournament->prize_pool; ?></div>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
</div>