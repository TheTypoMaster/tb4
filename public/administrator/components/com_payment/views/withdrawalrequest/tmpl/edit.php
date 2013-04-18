<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend>Request</legend>
			<table class='admintable'>
				<tr>
					<td class='key'>Requester</td>
					<td><?php echo $this->request->requester ?></td>
				</tr>
				<tr>
					<td class='key'>Amount</td>
					<td><?php echo htmlspecialchars(sprintf('$%.2f' , $this->request->amount / 100)) ?></td>
				</tr>
				<tr>
					<td class='key'>Withdrawal Type</td>
					<td>
						<?php echo htmlspecialchars($this->request->withdrawal_type) ?>
						<br />
						<?php if('paypal' == $this->request->withdrawal_type && $this->request->paypal_id)
						{
						echo ( ' - ' . htmlspecialchars($this->request->paypal_id) );
						}elseif('moneybookers' == $this->request->withdrawal_type && $this->request->moneybookers_id)
						{
						echo ( ' - ' . htmlspecialchars($this->request->moneybookers_id) );
						}
						?>
					</td>
				</tr>
				<tr>
					<td class='key'>Requested Date</td>
					<td><?php echo htmlspecialchars($this->request->requested_date ? $this->request->requested_date : 'N/A') ?></td>
				</tr>
				<tr>
					<td class='key'>Fulfilled Date</td>
					<td><?php echo htmlspecialchars($this->request->fulfilled_date ? $this->request->fulfilled_date : 'N/A') ?></td>
				</tr>
				<tr>
					<td class='key'>Fulfiller</td>
					<td><?php echo htmlspecialchars($this->request->fulfiller ? $this->request->fulfiller : 'N/A') ?></td>
				</tr>
<?php
if($this->request->approved_flag === null )
{
?>
				<tr>
					<td class='key'>Approve Request</td>
					<td>
						<input id='radio_approve_request_yes' type='radio' value='yes' name='approved_flag' <?php echo $this->formData['approved_flag'] == 'yes' ? 'checked =\'checked\'' : '' ?> />
						<label for='radio_approve_request_yes'>Approve this request</label>
						<br />
						<input id='radio_approve_request_no' type='radio' value='no' name='approved_flag' <?php echo $this->formData['approved_flag'] == 'no' ? 'checked =\'checked\'' : '' ?> />
						<label for='radio_approve_request_no'>Deny this request</label>
						<?php echo $this->formErrors['approved_flag'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['approved_flag']) . '</div>'  : ''?>
					</td>
				</tr>
				<tr>
					<td class='key'>Notes</td>
					<td>
						<textarea name='notes' style='width: 300px; height: 150px;'><?php echo( htmlspecialchars($this->formData['notes']) ) ?></textarea>
						<?php echo $this->formErrors['notes'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['notes']) . '</div>'  : ''?>
					</td>
				</tr>
<?php
	if( $this->request->requester_email )
	{
?>
				<tr>
					<td class='key'>Notifying Email</td>
					<td>
						<input id='send_email' type='checkbox' name='send_email' value='1'<?php echo $this->formData['send_email'] ? ' checked=\'checked\'' : '' ?>>
							<label for='send_email'>Send a notifying email</label>
						</input>
						<a name='notifying_email'></a>
						<div id='notifying_email' class='hide'>
							<div>To: <?php echo htmlspecialchars($this->request->requester . ' <' . $this->request->requester_email .'>')?></div>
							<div>From: <?php echo htmlspecialchars($this->senderName . ' <' . $this->senderEmail . '>') ?></div>
							<div>Email Subject: <input type='text' name='notifying_email_subject' value='<?php echo htmlspecialchars($this->formData['notifying_email_subject']) ?>' id='notifying_email_subject' />
						<?php echo $this->formErrors['notifying_email_subject'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['notifying_email_subject']) . '</div>'  : ''?></div>
							<div>
								Email Body:<br />
								<textarea name='notifying_email_body' style='width: 300px; height: 150px;' id='notifying_email_body'><?php echo( htmlspecialchars($this->formData['notifying_email_body']) ) ?></textarea>
								<?php echo $this->formErrors['notifying_email_body'] ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['notifying_email_body']) . '</div>'  : ''?>
							</div>
						</div>
						<div class='hide'>
							<div id='approval_subject'><?php echo htmlspecialchars($this->approvalEmailSubject) ?></div>
							<div id='approval_body'><?php echo htmlspecialchars($this->approvalEmailBody) ?></div>
							<div id='denial_subject'><?php echo htmlspecialchars($this->denialEmailSubject) ?></div>
							<div id='denial_body'><?php echo htmlspecialchars($this->denialEmailBody) ?></div>
						</div>
					</td>
				</tr>
<?php
	}
}
else
{
?>
				<tr>
					<td class='key'>Request Status</td>
					<td><?php echo($this->request->approved_flag ? 'Approved' : 'Denied') ?></td>
				</tr>
				<tr>
					<td class='key'>Notes</td>
					<td>
						<?php echo( htmlspecialchars($this->request->notes) ) ?>
				</tr>
<?php 
}
?>
			
			
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' ); ?>" />
	<input type="hidden" name="c" value="<?php echo JRequest::getVar( 'c' ); ?>" />
	<input type="hidden" name="id" value="<?php echo $this->request->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>
<?php
if(isset($this->formErrors['formError']))
{
	echo '<div class=\'error\'>' .  $this->formErrors['formError'] . '</div>';
}	
?>