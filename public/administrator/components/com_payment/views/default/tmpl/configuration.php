<?php
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs, {}); }); });
</script>
<form name="adminForm" id="adminForm" method="post" action="index.php">
	<dl class="tabs">
		<div>
			<dt id="generic" style="cursor: pointer;" class="closed"><span>Generic Configuration</span></dt>
			<dt id="paypal" style="cursor: pointer;" class="closed"><span>Paypal Configuration</span></dt>
			<dt id="eway" style="cursor: pointer;" class="closed"><span>Eway Configuration</span></dt>
			<dt id="moneybooker" style="cursor: pointer;" class="closed"><span>MoneyBookers Configuration</span></dt>
		</div>
		<div class="current">
			<dd style="display: block;">
				<fieldset>
					<legend>Generic Configuration</legend>
					<table cellspacing="1" class="admintable">
					<tbody>
						<tr>
							<td class="key">Help Email</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['help_email']) ?>" name="params[help_email]" class="inputbox" />
								<?php echo $this->formErrors['help_email'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['help_email']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Sender's Email</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['sender_email']) ?>" name="params[sender_email]" class="inputbox" />
								<?php echo $this->formErrors['sender_email'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['sender_email']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Sender's Name</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['sender_name']) ?>" name="params[sender_name]" class="inputbox" />
								<?php echo $this->formErrors['sender_name'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['sender_name']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Notifying Email Subject</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['withdrawal_notify_email_subject']) ?>" name="params[withdrawal_notify_email_subject]" class="inputbox" />
								<?php echo $this->formErrors['withdrawal_notify_email_subject'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_notify_email_subject']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Notifying Email Body</td>
							<td>
								<textarea name="params[withdrawal_notify_email_body]" style="width: 300px; height: 150px"><?= htmlspecialchars($this->formData['withdrawal_notify_email_body']) ?></textarea>
								<?php echo $this->formErrors['withdrawal_notify_email_body'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_notify_email_body']) . '</div>'  : ''?>
								&nbsp;
								<div id="email_variables">
									<a href="#" onclick="return false;" id="show_variable">email variables</a>
									<div id="variable_list">
										<url>
<?php
foreach ( $this->varReplacements as $var => $replacement )
{
	echo '<li><strong>[' . htmlspecialchars($var) . ']</strong>: <em>' . htmlspecialchars($replacement) . '</em></li>';
}
?>
										</url>
										<div id="variable_close">[close]</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Approval Email Subject</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['withdrawal_approval_email_subject']) ?>" name="params[withdrawal_approval_email_subject]" class="inputbox" />
								<?php echo $this->formErrors['withdrawal_approval_email_subject'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_approval_email_subject']) . '</div>'  : ''?>
								
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Approval Email Body</td>
							<td>
								<textarea name="params[withdrawal_approval_email_body]" style="width: 300px; height: 150px"><?= htmlspecialchars($this->formData['withdrawal_approval_email_body']) ?></textarea>
								<?php echo $this->formErrors['withdrawal_approval_email_body'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_approval_email_body']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Denial Email Subject</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['withdrawal_denial_email_subject']) ?>" name="params[withdrawal_denial_email_subject]" class="inputbox" />
								<?php echo $this->formErrors['withdrawal_denial_email_subject'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_denial_email_subject']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Withdrawal Denial Email Body</td>
							<td>
								<textarea name="params[withdrawal_denial_email_body]" style="width: 300px; height: 150px"><?= htmlspecialchars($this->formData['withdrawal_denial_email_body']) ?></textarea>
								<?php echo $this->formErrors['withdrawal_denial_email_body'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['withdrawal_denial_email_body']) . '</div>'  : ''?>
							</td>
						</tr>
					</tbody>
					</table>
				</fieldset>
			</dd>
			<dd style="display: none;">
				<fieldset>
					<legend>Paypal Configuration</legend>
					<table cellspacing="1" class="admintable">
					<tbody>
						<tr>
							<td class="key">Enable Paypal</td>
							<td>
								<input type="radio" class="inputbox" <?= $this->formData['paypal_enabled'] === '0' ? 'checked="checked"' : '' ?> value="0" id="params[paypal_enabled]0" name="params[paypal_enabled]" />
								<label for="params[paypal_enabled]0">No</label>
								<input type="radio" class="inputbox" <?= $this->formData['paypal_enabled'] === '1' ? 'checked="checked"' : '' ?> value="1" id="params[paypal_enabled]1" name="params[paypal_enabled]">
								<label for="params[paypal_enabled]1">Yes</label>
								<?php echo $this->formErrors['paypal_enabled'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_enabled']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Account Name</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['paypal_account']) ?>" size="30" name="params[paypal_account]" class="inputbox">
								<?php echo $this->formErrors['paypal_account'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_account']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Paypal URL</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['paypal_url']) ?>" size="30" name="params[paypal_url]" class="inputbox">
								<?php echo $this->formErrors['paypal_url'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_url']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Deposit Item Name</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['paypal_item_name']) ?>" size="30" name="params[paypal_item_name]" class="inputbox">
								<?php echo $this->formErrors['paypal_item_name'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_item_name']) . '</div>'  : ''?>
								<div id="paypal_item">
									<a href="#" onclick="return false;" id="show_suggestion">suggestion</a>
									<div id="paypal_item_suggestion">
										In order to indentify the payers in Paypal website, two variables can be used:<br />
										[name]: customer's full name<br />
										[pin]: customer's pin number<br />
										for example: <em>Top Betta Instant Deposit For [name] ([pin])</em>
										<div id="paypal_item_suggestion_close">[close]</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="key">Min Deposit Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['paypal_min_deposit']) ?>" name="params[paypal_min_deposit]" class="inputbox" />
								<?php echo $this->formErrors['paypal_min_deposit'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_min_deposit']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Min Withdrawal Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['paypal_min_withdrawal']) ?>" name="params[paypal_min_withdrawal]" class="inputbox" />
								<?php echo $this->formErrors['paypal_min_withdrawal'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_min_withdrawal']) . '</div>'  : ''?>
							</td>
						</tr>
					</tbody>
					</table>
				</fieldset>
			</dd>
			<dd style="display: none;">
				<fieldset>
					<legend>Eway Configuration</legend>
					<table cellspacing="1" class="admintable">
					<tbody>
						<tr>
							<td width="185" class="key">Enable EWAY</td>
							<td>
								<input type="radio" class="inputbox" <?= $this->formData['eway_enabled'] === '0' ? 'checked="checked"' : '' ?> value="0" id="params[eway_enabled]0" name="params[eway_enabled]" />
								<label for="params[eway_enabled]0">No</label>
								<input type="radio" class="inputbox" <?= $this->formData['eway_enabled'] === '1' ? 'checked="checked"' : '' ?> value="1" id="params[eway_enabled]1" name="params[eway_enabled]">
								<label for="params[eway_enabled]1">Yes</label>
								<?php echo $this->formErrors['eway_enabled'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_enabled']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td width="185" class="key">EWAY Account</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['eway_account']) ?>" size="30" name="params[eway_account]" class="inputbox">
								<?php echo $this->formErrors['eway_account'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_account']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td width="185" class="key">EWAY URL</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['eway_url']) ?>" size="30" name="params[eway_url]" class="inputbox">
								<?php echo $this->formErrors['eway_url'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_url']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Min Deposit Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['eway_min_deposit']) ?>" name="params[eway_min_deposit]" class="inputbox" />
								<?php echo $this->formErrors['eway_min_deposit'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_min_deposit']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Min Withdrawal Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['eway_min_withdrawal']) ?>" name="params[eway_min_withdrawal]" class="inputbox" />
								<?php echo $this->formErrors['eway_min_withdrawal'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_min_withdrawal']) . '</div>'  : ''?>
							</td>
						</tr>
					</tbody>
					</table>
				</fieldset>
			</dd>
			<!-- MoneyBookers Configurations Start -->
			<dd style="display: none;">
				<fieldset>
					<legend>MoneyBookers Configuration</legend>
					<table cellspacing="1" class="admintable">
					<tbody>
						<tr>
							<td width="185" class="key">Enable MoneyBookers</td>
							<td>
								<input type="radio" class="inputbox" <?= $this->formData['moneybookers_enabled'] === '0' ? 'checked="checked"' : '' ?> value="0" id="params[moneybookers_enabled]0" name="params[moneybookers_enabled]" />
								<label for="params[moneybookers_enabled]0">No</label>
								<input type="radio" class="inputbox" <?= $this->formData['moneybookers_enabled'] === '1' ? 'checked="checked"' : '' ?> value="1" id="params[moneybookers_enabled]1" name="params[moneybookers_enabled]">
								<label for="params[moneybookers_enabled]1">Yes</label>
								<?php echo $this->formErrors['moneybookers_enabled'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_enabled']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td width="185" class="key">MoneyBookers Account</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_account']) ?>" size="30" name="params[moneybookers_account]" class="inputbox">
								<?php echo $this->formErrors['moneybookers_account'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_account']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td width="185" class="key">MoneyBookers URL</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_url']) ?>" size="30" name="params[moneybookers_url]" class="inputbox">
								<?php echo $this->formErrors['moneybookers_url'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_url']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Min Deposit Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_min_deposit']) ?>" name="params[moneybookers_min_deposit]" class="inputbox" />
								<?php echo $this->formErrors['moneybookers_min_deposit'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_min_deposit']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Min Withdrawal Amount</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_min_withdrawal']) ?>" name="params[moneybookers_min_withdrawal]" class="inputbox" />
								<?php echo $this->formErrors['moneybookers_min_withdrawal'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_min_withdrawal']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Merchant ID<br>Don't Change.</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_merchant_id']) ?>" name="params[moneybookers_merchant_id]" class="inputbox" />
								<?php echo $this->formErrors['moneybookers_merchant_id'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_merchant_id']) . '</div>'  : ''?>
							</td>
						</tr>
						<tr>
							<td class="key">Secret Word<br>Don't Change.</td>
							<td>
								<input type="text" value="<?= htmlspecialchars($this->formData['moneybookers_secret_word']) ?>" name="params[moneybookers_secret_word]" class="inputbox" />
								<?php echo $this->formErrors['moneybookers_secret_word'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_secret_word']) . '</div>'  : ''?>
							</td>
						</tr>
					</tbody>
					</table>
				</fieldset>
			</dd>
			<!-- MoneyBookers Configurations End -->
		</div>
	</dl>
	<input type="hidden" name="option" value="com_payment" />
	<input type="hidden" name="act" value="configuration" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="configuration" />
	
</form>