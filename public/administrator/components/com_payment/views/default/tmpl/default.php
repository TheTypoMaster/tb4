<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Payment'), 'generic.png');
?>

<table class="paymentjoomcss">
	<tbody>
		<tr>
         	<td width="58%" valign="top">
				<div id="cpanel">
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_payment&amp;c=withdrawal">
							<img src="/administrator/components/com_payment/images/payment.png" alt="Withdrawal Requests">
							<span>Withdrawal Requests</span>
							</a>
						</div>
					</div>
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_tournamentdollars">
							<img src="/administrator/components/com_tournamentdollars/images/tournament_transaction.png" alt="Tournament Transactions">
							<span>Tournament Dollars Transactions</span>
							</a>
						</div>
					</div>
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_payment&amp;c=account">
							<img src="/administrator/components/com_payment/images/account_transaction.png" alt="Account Transactions">
							<span>Account Transactions</span>
							</a>
						</div>
					</div>
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_payment&amp;task=configuration">
							<img src="/administrator/components/com_payment/images/configuration.png" alt="Configuration">
							<span>Configuration</span>
							</a>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>