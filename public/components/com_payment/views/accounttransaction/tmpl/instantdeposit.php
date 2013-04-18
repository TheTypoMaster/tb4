<?php defined('_JEXEC') or die('Restricted access'); 

// set the open state of the accordion
// default = -1 (all closed)
$accShowVar = JRequest::getVar( 'type', -1, 'get' );
switch ($accShowVar) {
	case 'card':
        $accShowVal =  0;
        break;
	case 'moneybookers';
	    $accShowVal =  1;
        break;
    case 'paypal':
        $accShowVal =  2;
        break;
    case -1:
        $accShowVal =  -1;
    	break;
    default:
        $accShowVal =  -1;
    	break;
}
?>

<script type="text/javascript">
		window.addEvent('domready', function(){
			var accordion = new Accordion('div.atStart1', 'div.atStart2', {
				show: <?php echo $accShowVal; ?>,
				opacity: true,
				alwaysHide: true,
				onActive: function(toggler, element){
					toggler.getElement('.Aarrow').setStyle('background-position', '0 -48px');
				},
				onBackground: function(toggler, element){
					toggler.getElement('.Aarrow').setStyle('background-position', '-50px -48px');
				}
			}, $('bettaWrap'));

		}); 
</script>

<?php
//check if the page is the after payment page
if( $this->isAfterPayment )
{
?>
<div id="bettaWrap" >
	<div class="moduletable">
		<div class="innerWrap" >
<?php
	if( $this->paymentInfo )
	{
		switch( $this->paymentInfo->type)
		{
			case 'paypal':
				if( 'VERIFIED' == $this->paymentInfo->ipn_response && 'Completed' == $this->paymentInfo->payment_status )
				{
?>
					Thanks! Your deposit has been successfully processed and the funds are now available in your Account Balance. <br />
					<br />
					<h4>Your payment information:</h4>
					<br />
					
					<table id="receipt" border='1'>
						<tr>
							<th>Paypal Transaction Id:</th>
							<td><?php echo htmlspecialchars($this->paymentInfo->txn_id) ?></td>
						</tr>
						<tr>
							<th>Amount:</th>
							<td><?php echo htmlspecialchars(sprintf('$%.2f', $this->paymentInfo->mc_gross)) ?></td>
						</tr>
					</table>
					<br />
					<br />
<?php				
				}
				else
				{
?>
					Error! Your attempted deposit has failed. Please contact <a href="mailto:help@topbetta.com">help@topbetta.com</a> for assistance.
					<br />
					<br />
<?php					
				}
				break;
				
				
			case 'eway':
				if( $this->paymentInfo->err )
				{
?>
					Error! Your attempted deposit has failed. Please contact <a href="mailto:help@topbetta.com">help@topbetta.com</a> for assistance. <br />
					<br />
					Error message:<?php echo htmlspecialchars($this->paymentInfo->err) ?>
					<br />
					<br />
<?php
				}
				else
				{
?>
					Thanks! Your deposit has been successfully processed and the funds are now available in your Account Balance. <br />
					<br />
					<h4>Your payment information:</h4>
					<br />
					<table id="receipt" border='1'>
						<tr>
							<th>Transaction Id:</th>
							<td><?php echo htmlspecialchars($this->paymentInfo->txn_id) ?></td>
						</tr>
						<tr>
							<th>Bank authorisation number:</th>
							<td><?php echo htmlspecialchars($this->paymentInfo->bank_auth_code) ?></td>
						</tr>
						<tr>
							<th>Invoice Number:</th>
							<td><?php echo htmlspecialchars($this->paymentInfo->invoice_number) ?></td>
						</tr>
						<tr>
							<th>Amount:</th>
							<td><?php echo htmlspecialchars(sprintf('$%.2f', $this->paymentInfo->amount)) ?></td>
						</tr>
					</table>
					<br />
					<br />
<?php
				}
				break;
				case 'moneybookers':
					?>
					<h4>Thanks! Your deposit has been successfully processed.</h4>
					<br />Funds will be available in your Account Balance after we receive the payment.<br />
					<br />
					<?php
				break;	 
		}
		
	}
	else
	{
?>
		Payment does not exist!
		<br />
		<br />
<?php
	}
?>
		</div>
	</div>
</div>
<?php
}
else
{
?>
<div id="bettaWrap" >
	<div class="moduletable">
		<h3>Make A Deposit</h3>
		<div class="innerWrap" >			
			<div class="toggler atStart1 accordHead">
				<div class="Alogo"><div class="Lcard">&nbsp;</div></div>
				<div class="Atitle">Credit Card &mdash; Instant Deposit</div>
				<div class="Aarrow">&nbsp;</div>
			</div>
			<div class="element atStart2">
				<div class="accordBody">
					<?php echo isset($this->formErrors['eway_system']) ? (htmlspecialchars($this->formErrors['eway_system']) . '<br />') : '' ?>
					<form class="acconutFrm" method="post" action="<?php echo $this->ewayPostUrl ?>">
						<div class="accForm accFormS">
							<h5>Pay via Credit Card</h5>
							<label for="eway_expiry_month">Name on card</label>
							<input class="w208" type='text' name='eway_name' value='<?php echo isset($this->formData['eway_name']) ? htmlspecialchars($this->formData['eway_name']): '' ?>' />
							<?php echo isset($this->formErrors['eway_name']) ? ('<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_name']) . '</div>') : '' ?>
							<label for="eway_expiry_month">Card number</label>
							<input class="w208" type='password' name='eway_card_number' />
							<?php echo isset($this->formErrors['eway_card_number']) ? ('<div class=\'error\'>' . htmlspecialchars($this->formErrors['eway_card_number']) . '</div>') : '' ?>


						</div>
						<div class="accInfo accInfoS">
							<h5>&nbsp;</h5>
							<label for="eway_expiry_month">Expiry Date</label>
							<select name="eway_expiry_month">
<?php
	foreach( $this->options['month'] as $value => $label )
	{
?>
		<option value='<?php echo htmlspecialchars($value)?>'><?php echo htmlspecialchars($label)?></option>
<?php
	}
?>
							</select>
							Month 
							<select name="eway_expiry_year">
<?php
	foreach( $this->options['year'] as $value => $label )
	{
?>
				<option value='<?php echo htmlspecialchars($value)?>'><?php echo htmlspecialchars($label)?></option>
<?php
	}
?>
							</select>
							Year
							<br />
							<?php echo isset($this->formErrors['eway_expiry']) ? '<div class=\'error\'>' . (htmlspecialchars($this->formErrors['eway_expiry']) . '</div>') : '' ?>
							<label for="eway_cvc">CVC</label>
							<input class="w70" type='password' name='eway_cvc' />
								<a href="javascript:void(0)" onclick="window.open('index.php?option=com_payment&c=account&layout=cvc&tmpl=component','cvc','width=500,height=400')">
								What's this</a>
							<br />
							<?php echo isset($this->formErrors['eway_cvc']) ? '<div class=\'error\'>' . (htmlspecialchars($this->formErrors['eway_cvc']) . '</div>') : '' ?>
							<label for="eway_amount">Amount to Deposit &nbsp;(AUD $)</label>
							<input class="w238" name='eway_amount' value='<?php echo isset($this->formData['eway_amount']) ? htmlspecialchars($this->formData['eway_amount']) : ''?>' />
							<br />
							<?php echo isset($this->formErrors['eway_amount']) ? '<div class=\'error\'>' . (htmlspecialchars($this->formErrors['eway_amount']) . '</div>') : '' ?>
							<p>Please note: All deposits are processed in Australian Dollars</p>
							<input type="hidden" name="itemid" value="<?php echo htmlspecialchars($this->itemid) ?>" />
							<input type="submit" class="bigBluButt w260" value="Deposit to my Account" />
						</div>
						<div class="clr"></div>
					</form>					
				</div>
			</div>
			<div class="hrzspr"></div>
			<!--  Start of Moneybookers Form -->
		<div class="toggler atStart1 accordHead">
				<div class="Alogo"><div class="Lmoneybookers">&nbsp;</div></div> 
				<div class="Atitle">MoneyBookers &mdash; Instant Deposit</div>
				<div class="Aarrow">&nbsp;</div>
			</div>
			
			<div class="element atStart2">
				<div class="accordBody">
					<div class="accForm">
						<h5>Make a MoneyBookers Deposit</h5>
						<?php echo isset($this->formErrors['moneybookers_system']) ? (htmlspecialchars($this->formErrors['moneybookers_system']) . '<br />') : '' ?>
						<form class="acconutFrm" method="post" action="<?php echo $this->moneybookersPostUrl ?>">
							<label for="moneybookers_email">MoneyBookers Login / Email</label>
							<input type="text" name="moneybookers_email" value="<?php echo $this->userMoneybookersEmail ?>" />
							<?php echo isset($this->formErrors['moneybookers_email']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_email']) . '</div>' : '' ; ?>
							<label for="moneybookers_amount">Amount to Deposit &nbsp;(AUD $)</label>
							<input type="text" name="moneybookers_amount" value="<?php echo isset($this->formData['moneybookers_amount']) ? htmlspecialchars($this->formData['moneybookers_amount']) : ''; ?>" />
							<?php echo isset($this->formErrors['moneybookers_amount']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['moneybookers_amount']) . '</div>' : '' ; ?>
							<br />
							<p>Please note: All deposits are processed in Australian Dollars</p>
							<input type="submit" name="moneybookers_withdrawal" class="bigBluButt" value="Deposit to my Account" onclick="this.disabled=true; this.form.submit();" />
						</form>
					</div>
					<div class="accInfo">
            <h5>Sign up for MoneyBookers</h5>
            <div class="moneybookersLogo">&nbsp;</div>
            <p>Whether you are making payments, sending or receiving money online, with Skrill (Moneybookers) you only need an email address and a password. No need to carry all your payment details around and repeatedly type them in.<br />
            <br />
            <br />
            <a href="http://www.moneybookers.com/app/consumer.pl" target="_blank">Click here to learn more about MoneyBookers.</a></p>

            <!-- <div class="accInfo_paypal">
              <div class="plsnote">
                <div class="plsHdr">PLEASE NOTE:</div>
                MoneyBookers withdrawals are only available if you have MoneyBookers Account</a>.
              </div>
            </div>  -->
          </div>
					<div class="clr"></div>
				</div>
			</div>
			
			<!--  End of Moneybookers Form -->
			
			<div class="hrzspr"></div>
			
			<!--  Start of Paypal Form -->
			<div class="toggler atStart1 accordHead">
				<div class="Alogo"><div class="Lpaypal">&nbsp;</div></div>
				<div class="Atitle">PayPal &mdash; Instant Deposit</div>
				<div class="Aarrow">&nbsp;</div>
			</div>
			<div class="element atStart2">
				<div class="accordBody">
					<div class="accForm">
						<h5>Make a PayPal Deposit</h5>
						<?php echo isset($this->formErrors['paypal_system']) ? (htmlspecialchars($this->formErrors['paypal_system']) . '<br />') : '' ?>
						<form class="acconutFrm" method="post" action="<?php echo $this->paypalPostUrl ?>">
							<label for="paypal_email">PayPal Login / Email</label>
							<input type="text" name="paypal_email" value="<?php echo isset($this->formData['paypal_email']) ? htmlspecialchars($this->formData['paypal_email']) : ''; ?>" />
							<?php echo isset($this->formErrors['paypal_email']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_email']) . '</div>' : '' ; ?>
							<label for="paypal_amount">Amount to Deposit &nbsp;(AUD $)</label>
							<input type="text" name="paypal_amount" value="<?php echo isset($this->formData['paypal_amount']) ? htmlspecialchars($this->formData['paypal_amount']) : ''; ?>" />
							<?php echo isset($this->formErrors['paypal_amount']) ? '<div class=\'error\'>' . htmlspecialchars($this->formErrors['paypal_amount']) . '</div>' : '' ; ?>
							<br />
							<p>Please note: All deposits are processed in Australian Dollars</p>
							<input type="hidden" name="itemid" value="<?php echo htmlspecialchars($this->itemid) ?>" />
							<input type="submit" name="paypal_withdrawal" class="bigBluButt" value="Deposit to my Account" onclick="this.disabled=true; this.form.submit();" />
						</form>
					</div>
					<div class="accInfo">
						<h5>Sign up for PayPal</h5>
						<div class="paypalLogo">&nbsp;</div>
						<p>Paypal is the safer, easier way to pay and get paid online.<br />
						<br />
						The service allows anyone to pay in any way they prefer, including through credit cards, bank accounts, buyer credit or account balances, without sharing personal financial information.<br />
						<br />
						<a href="https://cms.paypal.com/au/cgi-bin/marketingweb?cmd=_render-content&content_ID=microsites_apac/How_does_PayPal_work&nav=1.0.0" target="_blank">Click here to learn more about PayPal.</a></p>
					</div>
					<div class="clr"></div>
				</div>
			</div>
			
			<!--  End of Paypal Form -->
			
			<div class="hrzspr"></div>
			<?php if($this->show_bankdeposit): ?>
			<div class="toggler atStart1 accordHead">
				<div class="Alogo"><div class="Lbankdeposit">&nbsp;</div></div>
				<div class="Atitle">Bank Deposit</div>
				<div class="Aarrow">&nbsp;</div>
			</div>
			<div class="element atStart2">
				<div class="accordBody">
					<div id="deposit">
						<div class="depnote">Please ensure your username (<span class="depdeat"><?php echo $this->escape($this->user->username); ?></span>) or TopBetta ID number (<span class="depdeat"><?php echo $this->escape($this->user->id); ?></span>) is quoted on deposit slip.</div>
						<div class="bankdetailsWrap">
							<div class="bankimg"><img src="images/bank_logos/logo_cba.jpg" width="220" height="70" border="0" alt="Commonwealth Bank of Australia"></div>
							<div class="bigarrow">
								<div class="bankdetailsH">Commonwealth Bank of Australia</div>
								<div class="bankdetails">BSB Number <b>062-950</b><br />Account Number <b>10080711</b></div>
							</div>
							<div class="clr"></div>
						</div>
						<div class="clr"></div>
					</div>
				</div>
			</div>
			<div class="hrzspr"></div>			
			
			<div class="toggler atStart1 accordHead">
				<div class="Alogo"><div class="Lbpay">&nbsp;</div></div>
				<div class="Atitle">BPAY</div>
				<div class="Aarrow">&nbsp;</div>
			</div>
			<div class="element atStart2">
				<div class="accordBody">
			      <div id="bpay">
			        <div id="bpaylogo">&nbsp;</div>
			        <div id="bpayrefwrap">
			          <div class="bpayref">Biller Code:&nbsp;135194<br />Ref:&nbsp;<?php echo $this->bpayRef; ?></div>
			        </div>
			        <div class="clr"></div>
			          <div class="bpaytxt1">Telephone & Internet Banking &#8212; BPAY&#174;</div>
			          <div class="bpaytxt2">Contact your bank, credit union or building society to make this payment from your cheque, savings, debit or credit card account. More info: www.bpay.com.au</div>
			        <div class="clr"></div>
			      </div>
			    </div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
}
?>

		