<?php
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
  label {
    display:block;
    width:150px;
    margin-right:10px;
    background-color:#eee;
    float:left;
    padding:5px;
    font-weight:bolder;
    color: #666;
  }
  select, input, textarea {
  }

  div.input-field {
    clear:both;
    padding:5px;
  }

  div.input-field p {
    clear:both;
    padding:5px 0 0 5px;
  }
input:disabled, select:disabled, .disabled
{
	background-color: #eee;
}
</style>
<form action="index.php?option=com_tournament&controller=tournamentracing&task=save" method="post" name="adminForm" id="adminForm">

    <fieldset>
    <legend><?php echo JText::_( 'Racing Tournament Details' ); ?></legend>

    <?php
    $disabled = $this->entrants_disable ? ' disabled="disabled"' : '';

    if(!empty($this->id)) {
    ?>
    <div class="input-field">
        <label for="display_id">
          <?php echo JText::_( 'Tournament ID' ); ?>:
        </label>
        <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
        <input disabled="disabled" class="text_area" type="text" name="display_id" id="display_id" size="32" maxlength="250" value="<?php echo $this->id; ?>" />
    </div>
    <?php } ?>

	<div class="input-field" id="tournament_name" <?php echo empty($this->id) ? ' style="display:none"' : '' ?>>
        <label for="name">
          <?php echo JText::_( 'Tournament Name' ); ?>:
        </label>
        <input class="text_area" type="text" name="name" id="name" size="32" maxlength="250" value="<?php echo $this->name; ?>" />
	</div>
	

    <div class="input-field">
        <label for="racing_meeting_id">
          <?php echo JText::_( 'Race Meeting Code' ); ?>:
        </label>
        <select name="meeting_id" id="meeting_id"<?php echo $disabled;?>>
          <option value="-1">Select&hellip;</option>
          <?php foreach($this->meeting_list as $meeting) { ?>
            <option value="<?php print $meeting->id; ?>"<?php print ($meeting->id == $this->meeting_id) ? ' selected="selected"' : ''; ?>><?php print date('r', strtotime($meeting->meeting_date)) . ' &mdash; ' . $this->escape($meeting->name) . ' &mdash; ' . $this->escape($meeting->meeting_code) . ' ' . $this->escape($meeting->meeting_date); ?></option>
          <?php }?>
        </select>
        <input type="text" class="text_area" id="tab_meeting_id" name="tab_meeting_id" size="32" value="<?php print $this->tab_meeting_id; ?>"<?php echo $disabled;?> />
        <p>Enter a TAB meeting ID to create a tournament for a meeting which is in the future.</p>
    </div>

    <div class="input-field">
    	<label for="start_time">
    		<?php echo JText::_('Start Time'); ?>
    	</label>
    	<input class="text_area" type="text" name="start_time" id="start_time" value="<?php echo $this->start_time; ?>"<?php echo $disabled; ?> />
    	<p>If this is not supplied it will be calculated, but will only be accurate if race data has previously been imported.</p>
    </div>

    <div class="input-field">
        <label for="tournament_value">
          <?php echo JText::_( 'Tournament Value' ); ?>:
        </label>
        <select name="tournament_value" id="tournament_value"<?php echo $disabled;?>>
          <option value="-1">Select&hellip;</option>
          <?php
                foreach($this->buyin_list as $buyin) {
                  $selected = ($buyin->id == $this->current_buy_in->id) ? ' selected="selected"' : '';
          ?>
            <option<?php print $selected; ?> value="<?php print $buyin->id; ?>"><?php print 'Buy-in: ' . $buyin->buy_in . ', Entry-fee: ' . $buyin->entry_fee; ?></option>
          <?php }?>
        </select>
    </div>

    <div class="input-field">
        <label for="gameplay">
          <?php echo JText::_( 'Jackpot' ); ?>:
        </label>
        <input name="jackpot_flag" id="jackpot_flag" type="checkbox" value="1" <?php print (empty($this->jackpot_flag)) ? '' : 'checked="checked" '; echo $disabled;?>/>
    </div>

    <div class="input-field" id="jackpot_parent" <?php print (empty($this->jackpot_flag)) ? ' style="display:none"' : ''?>>
        <label for="parentID">
          <?php echo JText::_( 'Jackpot Parent' ); ?>:
        </label>

        <select name="parent_tournament_id" id="parent_tournament_id">
          <option value="-1">Select&hellip;</option>
          <?php foreach($this->active_list as $active) { ?>
            <option value="<?php print $active->id; ?>"<?php print ($active->id == $this->parent_tournament_id) ? ' selected="selected"' : ''; ?>><?php print $active->name; ?></option>
          <?php }?>
        </select>
    </div>

    <div class="input-field">
        <label for="start_currency">
          <?php echo JText::_( 'Starting Bucks' ); ?>:
        </label>
        <input class="text_area" type="text" name="start_currency" id="start_currency" size="10" maxlength="15" value="<?php print (is_null($this->start_currency)) ? 1000 : $this->start_currency / 100; ?>"<?php echo $disabled;?> />
    </div>

    <div class="input-field">
        <label for="minimum_prize_pool">
          <?php echo JText::_( 'Minimum Prize Pool' ); ?>:
        </label>
        <input class="text_area" type="text" name="minimum_prize_pool" id="minimum_prize_pool" size="10" maxlength="10" value="<?php print (is_null($this->minimum_prize_pool)) ? 10 : $this->minimum_prize_pool / 100; ?>"<?php echo $disabled;?> />
    </div>

	<?php if (!empty($this->id)) : ?>
    <div class="input-field">
        <label for="description">
          <?php echo JText::_( 'Tournament Information' ); ?>:
        </label>
        <textarea class="text_area" name="description" id="description"><?php print $this->description; ?></textarea>
    </div>
    <?php endif; ?>

    <div class="input-field">
        <label for="status_flag">
          <?php echo JText::_( 'Publish Status' ); ?>:
        </label>
        <?php
          $checked = false;
          if(empty($this->id) || $this->status_flag == 1) {
            $checked = true;
          }
        ?>
        <input type="checkbox" value="1" name="status_flag" id="status_flag"<?php print ($checked) ? ' checked="checked"' : '';echo $disabled;?>/>
    </div>
    </fieldset>
    <fieldset>
    <legend><?php echo JText::_( 'Bet Limits' ); ?></legend>
    <p>If blank or 0 there will be no limit applied for that bet type.</p>
    <?php foreach($this->betlimit_option_list as $bettype => $betlimit) { ?>

    <div class="input-field">
        <label for="betlimit_<?php print $bettype; ?>">
          <?php echo JText::_( $betlimit['name'] ); ?>:
        </label>
        <input class="text_area" type="text" name="betlimit_<?php print $bettype; ?>" id="betlimit_<?php print $bettype; ?>" size="10" maxlength="10" value="<?php print $betlimit['value']; ?>" <?php echo $disabled;?>/> %
    </div>
    <?php } ?>
  </fieldset>
  <input type="hidden" name="task" value="" />
</form>
<script language="javascript">
window.addEvent('domready', function() {
	$('jackpot_flag').addEvent('click', function(e) {
		parentChecked = $('jackpot_flag').getProperty('checked');
		if(parentChecked == true) $('jackpot_parent').style.display = "block";
		else $('jackpot_parent').style.display = "none";
	});
});

/**
 * overwrite the Joomla Onsubmit JS validation - submitbutton
 */
function submitbutton(thebutton){
	parentJackpot = true;
	parentChecked = $('jackpot_flag').getProperty('checked');

	if(parentChecked == true && $('parent_tournament_id').getProperty('value') < 1){
		parentJackpot = confirm("Did you forget to assign a parent?");
	}
	if(parentJackpot){
		submitform(thebutton); //submit form if passed validation
	}
}
</script>

