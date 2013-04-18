<?php
/**
 * @version $Id: component.php 5173 2006-09-25 18:12:39Z Jinx $
 * @package Joomla
 * @subpackage Config
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 * 
 * php echo $lang->getName();  
 */
defined('_JEXEC') or die('Restricted access');

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

$nowTime = strtotime('now');
?>

<script language="javascript" type="text/javascript">
	
	window.addEvent('domready', function(){



		// catch clicks on the bet live buttons
		$$(".stop_bets").each(function(el){
			el.addEvent('click', function(listID){
				
				var answer = confirm("Do you want to stop bets for the selected race? " +listID)
				if (answer) {
					var redirURL = "index.php?option=com_ucbetman&controller=atp_wizard_detail&task=stopRaceBets&raceID="+listID;
					window.location = redirURL;
				}else{
					return;
				}
			}.pass(el.id)
			);
		});
		// catch click
		
		
		}); 
	
	function submitbutton(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert("<?php echo JText::_( 'atp_wizard item must have a name', true ); ?>");
			
		}
		else {
			submitform(pressbutton);
		}
	}	
		

	
		
		
	
</script>
<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
<div class="col50">
	<table class="noshow">
		<tr>
			<td width="50%">
					<fieldset class="adminform">
						<legend><a href="index.php?option=com_users&view=user&task=edit&cid[]=<?php echo $this->detail->id;?>" style="text-decoration: underline;">Joomla Core Details (click here to edit)</a></legend>	
						<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="tab_meeting_id">
									<?php echo JText::_( 'Joomla User ID' ); ?>:
								</label>
							</td>
							<td>
								<input disabled="disabled" class="text_area" type="text" name="user_id" id="user_id" size="32" maxlength="250" value="<?php echo $this->detail->id;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="name">
									<?php echo JText::_( 'User Name' ); ?>:
								</label>
							</td>
							<td>
								<input disabled="disabled" class="text_area" type="text" name="user_pin" id="user_pin" size="32" maxlength="250" value="<?php echo $this->detail->username;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="events">
									<?php echo JText::_( 'Email Address' ); ?>:
								</label>
							</td>
							<td>
								<input disabled="disabled" class="text_area" type="text" name="email" id="email" size="32" maxlength="250" value="<?php echo $this->detail->email;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="fname">
									<?php echo JText::_( 'Site Name' ); ?>:
								</label>
							</td>
							<td>
								<input disabled="disabled" class="text_area" type="text" name="fname" id="fname" size="32" maxlength="250" value="<?php echo $this->detail->name;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Personal Information</legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="title">
									<?php echo JText::_( 'Title' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_title" id="title" size="40" maxlength="10" value="<?php echo $this->detail->tb_title;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="namef">
									<?php echo JText::_( 'First Name' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_namef" id="namef" size="32" maxlength="250" value="<?php echo $this->detail->tb_namef;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="namel">
									<?php echo JText::_( 'Last Name' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_namel" id="namel" size="32" maxlength="250" value="<?php echo $this->detail->tb_namel;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="dob">
									<?php echo JText::_( 'Date of Birth' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_dob" id="dob" size="40" maxlength="12" value="<?php echo $this->detail->tb_dob;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Phone - Email Details</legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="mobile">
									<?php echo JText::_( 'Mobile Number' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_mobile" id="mobile" size="40" maxlength="250" value="<?php echo $this->detail->tb_mobile;?>" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<label for="phone">
									<?php echo JText::_( 'Phone Number' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_phone" id="phone" size="40" maxlength="250" value="<?php echo $this->detail->tb_phone;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Security Details</legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key">
								<label for="userpin">
									<?php echo JText::_( 'PIN' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="tb_pin" id="userpin" size="40" maxlength="250" value="<?php echo $this->detail->tb_pin;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td width="50%">
				<fieldset class="adminform">
					<legend>Address Details</legend>
					<table class="admintable">
					<tr>
						<td width="100" align="right" class="key">
							<label for="address">
								<?php echo JText::_( 'Street Address' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_address" id="address" size="40" maxlength="250" value="<?php echo $this->detail->tb_address;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="suburb">
								<?php echo JText::_( 'Suburb / City' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_suburb" id="suburb" size="40" maxlength="250" value="<?php echo $this->detail->tb_suburb;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="state">
								<?php echo JText::_( 'State' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_state" id="state" size="40" maxlength="250" value="<?php echo $this->detail->tb_state;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="country">
								<?php echo JText::_( 'Country' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_country" id="country" disabled="disabled" size="40" maxlength="250" value="<?php echo $this->detail->tb_country;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="pcode">
								<?php echo JText::_( 'Postal Code' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_pcode" id="pcode" size="40" maxlength="250" value="<?php echo $this->detail->tb_pcode;?>" />
						</td>
					</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Optional Information</legend>
					<table class="admintable">
					<tr>
						<td width="100" align="right" class="key">
							<label for="promo">
								<?php echo JText::_( 'Promotional Code' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_promo" id="promo" size="40" maxlength="250" value="<?php echo $this->detail->tb_promo;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="howuhear">
								<?php echo JText::_( 'How you heard?' ); ?>
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_howuhear" id="howuhear" size="40" maxlength="250" value="<?php echo $this->detail->tb_howuhear;?>" />
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="details">
								<?php echo JText::_( 'Additional Information' ); ?>:
							</label>
						</td>
						<td>
							<textarea name="tb_details" rows="3" cols="50" maxlength="150"><?php echo $this->detail->tb_details;?></textarea>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="optbox">
								<?php echo JText::_( 'Opt-in to Marketing' ); ?>:
							</label>
						</td>
						<td>
							<?php echo JHTML::_('select.booleanlist',  'tb_optbox', '', $this->detail->tb_optbox); ?>
						</td>
					</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Site Preferences</legend>
					<table class="admintable">
					<tr>
						<td width="100" align="right" class="key">
							<label for="sitepref">
								<?php echo JText::_( 'Home Page Preference' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="tb_sitepref" id="sitepref" size="40" maxlength="250" value="<?php echo $this->detail->tb_sitepref;?>" />
						</td>
					</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>Agreement</legend>
					<table class="admintable">
					<tr>
						<td width="100" align="right" class="key">
							<label for="privacy">
								<?php echo JText::_( 'I agree to privacy policy' ); ?>:
							</label>
						</td>
						<td>
							<?php echo JHTML::_('select.booleanlist',  'tb_privacy', '', $this->detail->tb_privacy); ?>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key">
							<label for="terms">
								<?php echo JText::_( 'I am over 18 years old' ); ?>:
							</label>
						</td>
						<td>
							<?php echo JHTML::_('select.booleanlist',  'tb_terms', '', $this->detail->tb_terms); ?>
						</td>
					</tr>
				
					
				</table>
				</fieldset>
			</td>
		</tr>
	</table>

</div>
<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="user_manager_detail" />
</form>



