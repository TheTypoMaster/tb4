<?php
defined('_JEXEC') or die();

JToolBarHelper::save();
JToolBarHelper::cancel();
?>
<form action="index.php?option=com_tournament" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend>Tournament Details</legend>
		<table class="admintable" border="0" cellpadding="10px" cellspacing="5px">
			<tbody>
				<tr>
					<td class='key'>
						<label for="tournament_sport_id">Sport</label>
					</td>
					<td>
						<select name="tournament_sport_id" id="tournament_sport_id" <?php echo $this->disabled;?>>
							<?php foreach($this->sport_option_list as $sport_id => $sport_name): ?>
							<option value="<?php echo $sport_id; ?>"<?php echo $this->sport_selected_list[$sport_id]; ?>><?php echo $sport_name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td class='key'>
						<label for="tournament_competition_id" class="">Competition</label>
					</td>
					<td>
						<select name="tournament_competition_id" id="tournament_competition_id" <?php echo $this->disabled;?>>
							<?php foreach($this->competition_option_list as $competition_id => $competition_name): ?>
							<option value="<?php echo $competition_id; ?>"<?php echo $this->competition_selected_list[$competition_id]; ?>><?php echo $competition_name; ?></option>
							<?php endforeach;?>
						</select>
					</td>
                    <td class='key'>
                        <label for="tournament_prize_format">Prize Payout Format</label>
                    </td>
                    <td>
                        <select name="tournament_prize_format" id="tournament_prize_format" <?php echo $this->disabled;?>>
                            <?php foreach($this->prize_option_list as $prize_format_id => $prize_format_name): ?>
                                <option value="<?php echo $prize_format_id; ?>"<?php echo $this->prize_selected_list[$prize_format_id]; ?>><?php echo $prize_format_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
				</tr>
				<tr>
					<td class='key'>
						<label for="event_group_id">Event Group</label>
					</td>
					<td>
						<select name="event_group_id" id="event_group_id" <?php echo $this->disabled;?>>
							<?php foreach($this->event_group_option_list as $eg_id => $eg_name): ?>
							<option value="<?php echo $eg_id; ?>"<?php echo $this->event_group_selected_list[$eg_id]; ?>><?php echo $eg_name; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="race_only">
							Future Meeting
							<select name="future_meeting_venue" id="future_meeting_venue" <?php echo $this->disabled;?>>
								<?php foreach ($this->venue_option_list as $venue_k => $venue) :?>
								<option value="<?php echo $this->escape($venue_k); ?>"<?php echo $this->escape($this->venue_selected_list[$venue_k]); ?>><?php echo $this->escape($venue); ?></option>
								<?php endforeach; ?>
							</select>
						</span>
					</td>
					<td class='key'>
						<label for="meeting_code">Publish Status</label>
					</td>
					<td>
						<input type="checkbox" name="status_flag" id="status_flag" value="1" <?php echo (isset($this->formdata['status_flag']) && $this->formdata['status_flag']) ? 'checked="checked"' : '' ?> <?php echo $this->disabled;?> />
					</td>
				</tr>
				<tr>
					<td class='key'>
						<label for="ticket_value">Ticket Value</label>
					</td>
					<td>
						<select name="ticket_value" id="ticket_value" <?php echo $this->disabled;?>>
							<?php foreach($this->buy_in_option_list as $buyin_id => $buyin_display): ?>
							<option value="<?php echo $buyin_id; ?>"<?php echo $this->buy_in_selected_list[$buyin_id]; ?>><?php echo $buyin_display; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td class='key'>
						<label for="minimum_prize_pool">Minimum prize-pool</label>
					</td>
					<td>
						$<input type="text" name="minimum_prize_pool" id="minimum_prize_pool" value="<?php echo $this->formdata['minimum_prize_pool']; ?>" <?php echo $this->disabled;?> />
					</td>
				</tr>
				<tr class="race_only" id="future_meeting_date">
					<td class='key'>
						<label for="start_date">Start Date</label> <br />
						(future meeting only)
					</td>
					<td colspan="3">
						<input type="date" name="start_date" id="start_date" class="DatePicker" alt="{format:'yyyy-mm-dd',yearStart:2010}" value="<?php echo $this->formdata['start_date']; ?>" <?php echo $this->disabled;?> />
						<input type="time" name="start_time" id="start_time" value="<?php echo $this->formdata['start_time']; ?>" <?php echo $this->disabled;?> />
						<p>
							Please enter the meeting start date for a future meeting. <br />
							[yyyy-mm-dd] [hh:mm:ss] 
						</p>
					</td>
				</tr>
				<tr>
					<td class='key'>
						<label for="jackpot_flag">Jackpot</label>
					</td>
					<td>
						<label for="jackpot_yes">Yes</label> <input type="radio" value="1" name="jackpot_flag" id="jackpot_yes"<?php echo $this->jackpot_yes_checked; ?> <?php echo $this->disabled;?> />
						<label for="jackpot_no">No</label> <input type="radio" value="0" name="jackpot_flag" id="jackpot_no"<?php echo $this->jackpot_no_checked; ?> <?php echo $this->disabled;?> />
					</td>
					<td class='key'>
						<label for="parent_tournament_id">Parent Tournament</label>
					</td>
					<td>
						<select name="parent_tournament_id" id="parent_tournament_id" <?php echo $this->disabled;?>>
							<?php foreach($this->parent_tournament_option_list as $tournament_id => $tournament_name): ?>
							<option value="<?php echo $tournament_id; ?>"<?php echo $this->parent_tournament_selected_list[$tournament_id]; ?>><?php echo $tournament_name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<?php if (!empty($this->id)) : ?>
					<td class='key'>
						<label for="name">Name</label>
					</td>
					<td>
						<input type="text" name="name" id="name" value="<?php echo $this->formdata['name']; ?>" />
					</td>
					<?php endif; ?>
					<td class='key'>
						<label for="start_currency">Starting currency</label>
					</td>
					<td>
						$<input type="text" name="start_currency" value="<?php echo $this->formdata['start_currency']; ?>" <?php echo $this->disabled;?> />
					</td>
					
					
					
					
					<td class='key'>
						<label for="tournament_label_id">Tournament Labels</label>
					</td>
					
					
					<td>
					
						<select name="tournament_label_id[]" id="tournament_label_id"  multiple="multiple" size ='5'>
							<?php foreach($this->tournament_label_option_list as $tournament_label_id => $tournament_label_label): ?>
							<option value="<?php echo $tournament_label_id; ?>"<?php echo in_array($tournament_label_id, $this->tournament_label_selected_list) ? 'selected="selected"' : ''?>><?php echo $tournament_label_label; ?></option>
							<?php endforeach; ?>
						</select>
					
					
					</td>
					

					<?php if (!empty($this->id)) : ?>
					<td colspan="2"></td>
					<?php endif; ?>
				</tr>
                <tr>
					<td class='key'>
						<label for="tod">Tournament of the day (TOD) for </label>
					</td>
					<td>
                    	<select name="tod_flag" id="tod_flag" >
							<?php foreach($this->tod_flag_list as $tod_kw => $tod_name): ?>
							<option value="<?php echo $tod_kw; ?>" <?php echo ($this->formdata['tod_flag']==$tod_kw) ? 'selected' : '' ?> ><?php echo $tod_name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td class='key'>
                    	<label for="fc">Free credit prize</label>(FCP)
                    </td>
					<td>
                    	<input type="checkbox" name="free_credit_flag" id="fc" <?php echo (isset($this->formdata['free_credit_flag']) && $this->formdata['free_credit_flag']==1) ? 'checked="checked"' : '' ?> value="1" />
                   </td>
				</tr>
				<?php if (!empty($this->id)) : ?>
				<tr>
					<td class='key'>
						<label for="name">Description</label>
					</td>
					<td colspan="3">
						<textarea rows="10" cols="80" name="description" id="description"><?php echo $this->formdata['description']; ?></textarea>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>
	<fieldset class="adminform" id="advanced_settings">
		<legend>Advanced Settings</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class='key'><label for="closed_betting_on_first_match_flag">Close betting on first event</label></td>
					<td><input type="checkbox" name="closed_betting_on_first_match_flag" id="closed_betting_on_first_match_flag" value="1" <?php echo isset($this->formdata['closed_betting_on_first_match_flag']) && $this->formdata['closed_betting_on_first_match_flag'] ? 'checked="checked"' : '' ?> <?php echo $this->disabled;?> /></td>
				</tr>
				<tr>
					<td class='key'><label for="reinvest_winnings_flag">Allow reinvestment of winnings</label></td>
					<td><input type="checkbox" name="reinvest_winnings_flag" id="reinvest_winnings_flag" value="1"<?php echo isset($this->formdata['reinvest_winnings_flag']) && $this->formdata['reinvest_winnings_flag'] ? 'checked="checked"' : '' ?> <?php echo $this->disabled;?> /></td>
				</tr>
				<tr>
					<td class='key'><label for="bet_limit_flag">Implement bet limit</label></td>
					<td><input type="checkbox" name="bet_limit_flag" id="bet_limit_flag" value="1"<?php echo isset($this->formdata['bet_limit_flag']) && $this->formdata['bet_limit_flag'] ? 'checked="checked"' : '' ?> <?php echo $this->disabled;?> /></td>
				</tr>

                <tr>
                    <td class='key'>
                        <label for="entries_close">Entries Close Date/Time (yyyy-mm-dd hh:mm:ss)</label>
                    </td>

                <td colspan="3">
                <input type="date" name="entries_date" id="entries_date" class="DatePicker" alt="{format:'yyyy-mm-dd',yearStart:2014}" value="<?php echo $this->formdata['entries_date']; ?>"  />
                <input type="time" name="entries_time" id="entries_time" value="<?php echo $this->formdata['entries_time']; ?>"  />

                </td>
                </tr>



			</tbody>
		</table>
		<input type="hidden" name="id" value="<?php echo $this->escape($this->id); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="is_racing_sport" id="is_racing_sport" value="<?php echo $this->escape($this->is_racing_sport); ?>" <?php echo $this->disabled;?> />
	</fieldset>




    <fieldset class="adminform">
        <legend>Tournament Sponsors</legend>
        <table class="admintable">
            <tbody>
                <tr>
                    <td class='key'>
                        <label for="tournament_sponsor_name">Tournament Sponsor Name</label>
                    </td>
                    <td>
                        <input type="text" size="128" name="tournament_sponsor_name" id="tournament_sponsor_name" value="<?php echo $this->formdata['tournament_sponsor_name']; ?>" />
                    </td>
                </tr>

                <tr>
                    <td class='key'>
                        <label for="tournament_sponsor_logo">Tournament Sponsor Logo</label>
                    </td>
                    <td>
                        <input type="text" size="128" name="tournament_sponsor_logo" id="tournament_sponsor_logo" value="<?php echo $this->formdata['tournament_sponsor_logo']; ?>" />
                    </td>
                </tr>

                <tr>
                    <td class='key'>
                        <label for="tournament_sponsor_logo_link">Tournament Sponsor Logo Link</label>
                    </td>
                    <td>
                        <input type="text" size="128" name="tournament_sponsor_logo_link" id="tournament_sponsor_logo_link" value="<?php echo $this->formdata['tournament_sponsor_logo_link']; ?>" />
                    </td>
                </tr>

            </tbody>
        </table>
    </fieldset>


</form>

<script language="javascript">
window.addEvent('domready', function() {
	$('jackpot_yes').addEvent('click', function(e) {
		$('parent_tournament_id').style.display = "block";
	});
	$('jackpot_no').addEvent('click', function(e) {
		$('parent_tournament_id').style.display = "none";
	});

	if ($('jackpot_yes').getProperty('checked')) {
		$('parent_tournament_id').style.display = "block";
	} else {
		$('parent_tournament_id').style.display = "none";
	}
});

/**
 * overwrite the Joomla Onsubmit JS validation - submitbutton
 */
function submitbutton(thebutton){
	parentJackpot = true;
	parentChecked = $('jackpot_yes').getProperty('checked');

	if(parentChecked == true && $('parent_tournament_id').getProperty('value') < 1){
		parentJackpot = confirm("Did you forget to assign a parent?");
	}
	if(parentJackpot){
		submitform(thebutton); //submit form if passed validation
	}
}
</script>
