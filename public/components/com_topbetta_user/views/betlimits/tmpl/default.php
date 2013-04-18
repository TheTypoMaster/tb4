<?php defined('_JEXEC') or die; ?>

<h3>My Account - Bet Limits</h3>
<div id="bet-limit-wrap">
	<form action="/index.php" method="post">
		<fieldset>
			<legend>Limit Your Losses</legend> 
			<label for="no_limit">No Limit <input id="no_limit" class="limit_radio" name="no_limit" type="radio" value="1"<?php echo $this->no_limit ? ' checked="checked"' : '' ?>/> </label> 
			<label for="has_limit">My Loss Limit: <input id="has_limit" class="limit_radio" name="no_limit" type="radio" value="0"<?php echo $this->no_limit ? '' : ' checked="checked"' ?>/>
			<strong>AUD $</strong><input id="bet_limit" name="bet_limit" type="text" value="<?php echo $this->bet_limit ?>" /> </label>
			<span class="bet-limit-message"><?php echo $this->escape($this->requested_limit_change) ?></span>
			<input id="bet-limit-submit" type="submit" name="submit" value="UPDATE" /> 
			<input type="hidden" name="option" value="com_topbetta_user" /> 
			<input type="hidden" name="task" value="betlimits_save" />
			<div>
				<p>A loss limit means that you will not be able to lose an amount greater than this in a 24-hour day.</p>
				
				<p>From midnight to midnight (<?php echo $this->time_zone; ?>), you will be allowed to place bets and enter cash tournaments (provided the funds are available in your account) up to your loss limit. You may also continue to bet with any cash winnings you receive on that day provided your total spend minus winnings remains under your limit.</p>
				
				<p>You may lower your loss limit at any time, but raising it will only take effect after 7 days.</p>
				
				<p>If you would like to block your access to the site entirely, you can request <a onclick="return confirm('Clicking ok will block you from accessing the site for a period of 1 week, and log you out. Click cancel to stay.');" href="/user/exclude">self-exclusion</a>. Should you require any further information on loss limits or responsible gambling, please check the <a href="/help">Help</a> section or <a href="/contact-us">contact us</a>.</p>
			</div>
		</fieldset>
	</form>
</div> 