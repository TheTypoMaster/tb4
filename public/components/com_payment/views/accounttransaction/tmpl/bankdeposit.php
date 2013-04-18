<?php defined('_JEXEC') or die('Restricted access'); 

$session =& JFactory::getSession();
$user = $session->get('user');
//print_r($user);
?>

<div id="bettaWrap" >
	<div class="moduletable">
		<h3>Make a Deposit</h3>
		<div class="innerWrap">
			<div id="deposit">
				<div class="depnote">Please ensure your username (<span class="depdeat"><?php echo $this->escape($user->username); ?></span>) or TopBetta ID number (<span class="depdeat"><?php echo $this->escape($user->id); ?></span>) is quoted on deposit slip.</div>
			<!--
				<div class="bankdetailsWrap">
					<div class="bankimg"><img src="images/bank_logos/logo_westpac.jpg" width="220" height="70" border="0" alt="Westpac Banking Corporation"></div>
					<div class="bigarrow">
						<div class="bankdetailsH">Westpac Banking Corporation</div>
						<div class="bankdetails">BSB Number XXX-XXX<br />Account Number XXXXXXXXX</div>
					</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
				<div class="bankdetailsWrap">
					<div class="bankimg"><img src="images/bank_logos/logo_nab.jpg" width="220" height="70" border="0" alt="National Australia Bank Limited"></div>
					<div class="bigarrow">
						<div class="bankdetailsH">National Australia Bank Limited</div>
						<div class="bankdetails">BSB Number XXX-XXX<br />Account Number XXXXXXXXX</div>
					</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
			-->
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
</div>