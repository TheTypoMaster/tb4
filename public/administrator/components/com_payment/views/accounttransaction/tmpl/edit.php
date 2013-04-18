<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="col width-60">
	<fieldset class="adminform">
		<legend>Transaction Details</legend>
		<form id="form1" method="post" action=
			"<?php echo JRoute::_('/administrator/index.php?option=com_payment&c=account&layout=recipient&format=xml') ?>">
			<table class='admintable'>
				<tr>
					<td class='key'>Recipient</td>
					<td>
							<input name="recipient" type="text" id="recipient" value='<?php echo htmlspecialchars($this->formData['recipient']) ?>' />
							<div id="recipient_list"></div>
							<?php echo $this->formErrors['recipient'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['recipient']) . '</div>'  : ''?>
					</td>
				</tr>
			</table>
		</form>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class='admintable'>
				<tr>
					<td class='key'>Amount</td>
					<td>
						<input type='text' name='amount' value='<?php echo htmlspecialchars($this->formData['amount'] ) ?>' />
						<?php echo $this->formErrors['amount'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['amount']) . '</div>'  : ''?>
					</td>
				</tr>
				<tr>
					<td class='key'>Account Transaction Type</td>
					<td>
						<select name='transaction_type'>
						
							<option value=''>Select ...</option>
<?php
foreach ($this->options['transaction_type'] as $transactionId => $transactionName )
{
	$selected = ($transactionId == $this->formData['transaction_type'] ? 'selected =\'selected\'' : '');
?>
							<option <?php echo $selected ?> value='<?php echo htmlspecialchars($transactionId)?>'><?php echo htmlspecialchars($transactionName)?></option>
<?php
}
?>
						</select>
						<?php echo $this->formErrors['transaction_type'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['transaction_type']) . '</div>'  : ''?>
					</td>
				</tr>
				<tr>
					<td class='key'>Notes</td>
					<td>
						<textarea name='notes' style='width: 300px; height: 150px;' ><?php echo( htmlspecialchars($this->formData['notes']) ) ?></textarea>
						<?php echo $this->formErrors['notes'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['notes']) . '</div>'  : ''?>
					</td>
				</tr>
			</table>
			<div class="clr"></div>
			<input type="hidden" name="recipient" id="transaction_recipient" value="<?php echo htmlspecialchars($this->formData['recipient']); ?>" />
			<input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' ); ?>" />
			<input type="hidden" name="c" value="<?php echo JRequest::getVar( 'c' ); ?>" />
			<input type="hidden" name="id" value="<?php echo $this->request->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	</fieldset>
</div>
<?php
if(isset($this->formErrors['formError']))
{
	echo '<div class=\'error\'>' .  $this->formErrors['formError'] . '</div>';
}	
?>