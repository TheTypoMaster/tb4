<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>

<?php
	if(isset($this->message)){
		$this->display('message');
	}
?>


<!-- MOVE LATER TO TMPL CSS --->
<style>
/* contact form styling */
div#regoForm {
	margin:10px;
}
div#regoForm fieldset {
	border: 1px solid #ccc;
	color: #333;
	margin:0 0 10px 0;
	padding:0 10px;
}
div#regoForm fieldset.nohdr {
	padding:5px 10px 0 10px;
}
div#regoForm legend {
	border: 1px solid #ccc;
	background:#f2f2f2;
	color: #111;
	font-size: 10px;
	font-weight: bold;
	padding:2px 10px;
	margin:0 0 4px 0;
}
div#regoForm div.Mrgt,
div#regoForm div.Mlft {
	width:445px;
}
div#regoForm div.Mlft{
	float:left;
}
div#regoForm div.Mrgt{
	float:right;
}
div#regoForm div {
	display:block;
}
div#regoForm div.radiolbl {
	width:394px;
	margin:5px 0 10px 0;
	padding:4px 5px;
	border-top:1px solid #0097e9;
	border-bottom:1px solid #0097e9;
}

div#regoForm label {
	float:left;
	width:140px;
	text-align:left;
	line-height: 21px;
	padding:0 5px;
	margin:0 2px 0 0;
	color: #fff;
	font-size: 12px;
	font-weight: bold;
	background:#0097e9;
}
div#regoForm .chklbl {
	float:none;
	text-align:left;
	padding:0 2px;
	margin:0 2px 0 0;
	color: #333;
	font-size: 12px;
	font-weight: bold;
	background:none;
}
div#regoForm input {
	font-size: 12px;
	font-weight: bold;
	color: #006da7;
	padding:2px 5px;
	width:240px;
	border: 1px solid #0097e9;
	background: #F7FFF0;
	margin:0 0 5px 0;
}
div#regoForm input.chk {
	padding:2px 5px;
	width:20px;
	border: 0px;
	background: none;
	margin:0 0 5px 0;
}
div#regoForm select{
	font-size: 12px;
	height: 21px;
	font-weight: bold;
	color: #006da7;
	border: 1px solid #0097e9;
	background: #F7FFF0;
	margin:0 0 5px 0;
}
div#regoForm input:hover {
	background:#ffffff;
}
div#regoForm input:focus {
	border: 4px solid #0097e9;
	background:#ffffff;
	margin:-2px -3px 3px -3px;
	padding:1px 5px;
}

</style>

<div class="moduletable">
	<h3>CREATE NEW ACCOUNT</h3>
	<div class="formcontent">
		<div class="formcontentInr">

			<div id="regoForm">
				<form action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" id="josForm" name="josForm" class="form-validate">
				
					<div class="Mlft">
						<fieldset class="lft">
							<legend>Personal Information</legend>
							<div>
								<label for="title">Title</label>
								<select name="title" id="title">
									<option value=""></option>
									<option value="Mr">Mr</option>
									<option value="Mrs">Mrs</option>
									<option value="Ms">Ms</option>
									<option value="Miss">Miss</option>
									<option value="Dr">Dr</option>
									<option value="Prof">Prof</option>
								</select>
							</div>
							<div>
								<label id="namemsg" for="name">First Name</label>
								<input name="name" id="name" size="40" value="" class="inputbox required" maxlength="50" type="text" /> *
							</div>
							<div>
								<label for="">Middle Name</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Last Name</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Date of Birth</label>
								<input name="###" type="text" id="###" /> *
							</div>
						</fieldset>
						
						<fieldset class="rgt">
							<legend>Phone - Fax - Email</legend>
							<div>
								<label for="">Mobile Number</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Home Number</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Work Number</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Fax</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Email Address</label>
								<input type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get( 'email' ));?>" class="inputbox required validate-email" maxlength="100" /> *
							</div>
							<div>
								<label for="">Alt. Email Address</label>
								<input name="###" type="text" id="###" />
							</div>
						</fieldset>
						
						<fieldset class="lft">
							<legend>Occupation</legend>
							<div>
								<label for="">Occupation</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Company</label>
								<input name="company" type="text" id="###" />
							</div>
						</fieldset>
						
						<fieldset class="rgt gry">
							<legend>Optional</legend>
							<div>
								<label for="">ReferalCode</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">SalesCode</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">MasterAccountPin</label>
								<input name="###" type="text" id="###" />
							</div>
							<div class="radiolbl">
								Would you like to apply for a credit account?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input class="chk" id="creditY" name="credit" value="true" checked="checked" type="radio">
								<label class="chklbl" for="credit">Yes</label>
								<input class="chk" id="creditN" name="credit" value="false" type="radio">
								<label class="chklbl" for="credit">No</label>
							</div>
							<div class="radiolbl">
								Would you like to receive SMS updates?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input class="chk" id="###" name="###" value="true" checked="checked" type="radio">
								<label class="chklbl" for="">Yes</label>
								<input class="chk" id="###" name="###" value="false" type="radio">
								<label class="chklbl" for="">No</label>
							</div>
						</fieldset>
					</div>
					
					<div class="Mrgt">
						<fieldset class="lft">
							<legend>Home Address</legend>
							<div>
								<label for="">Street Address</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Suburb</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">State</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Postal</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="countryList1">Country</label>
								<select name="countryList1" id="countryList1">
									<!-- INSERT COUNTRY CODES HERE -->
									<?php include 'countrylist.php'; ?>
								</select>
							</div>
						</fieldset>
						
						<fieldset class="rgt">
							<legend>Postal Address</legend>
							<div>
								<input id="addressSame" onclick="addressSame(this);" type="checkbox">
								(Same as Home Address)
							</div>
							<div>
								<label for="">Street Address</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Suburb</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">State</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Postal</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="countryList2">Country</label>
								<select name="countryList2" id="countryList2">
									<!-- INSERT COUNTRY CODES HERE -->
									<?php include 'countrylist.php'; ?>
								</select>
							</div>
						</fieldset>
						
						<fieldset class="rgt">
							<legend>Other Questions</legend>
							<div>
								<label for="">Currency</label>
								<select name="currency" id="currency">
									<option value="AUS">AUS</option>
								</select>
							</div>
							<div>
								<label for="">Preferred Odds</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">Normal Bet Size</label>
								<input name="###" type="text" id="###" />
							</div>
							<div>
								<label for="">How Hear About Us</label>
								<input name="###" type="text" id="###" />
							</div>
						</fieldset>
						<fieldset class="rgt">
							<legend>Security</legend>
							<div>
								<label for="">Username</label>
								<input type="text" id="username" name="username" value="<?php echo $this->escape($this->user->get( 'username' ));?>" class="inputbox required validate-username" maxlength="25" /> *
							</div>
							<div>
								<label for="">Password</label>
								<input class="inputbox required validate-password" type="password" id="password" name="password" value="" /> *
							</div>
							<div>
								<label for="">Confirm Password</label>
								<input class="inputbox required validate-passverify" type="password" id="password2" name="password2" value="" /> *
							</div>
						</fieldset>
					
						<?php echo JText::_( 'REGISTER_REQUIRED' ); ?>
						<button class="button validate" type="submit"><?php echo JText::_('Register'); ?></button>
					</div>
					<div class="clr"></div>
					<input type="hidden" name="task" value="register_save" />
					<input type="hidden" name="id" value="0" />
					<input type="hidden" name="gid" value="0" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</div>

		</div>
	</div>
</div>
