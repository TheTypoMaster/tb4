<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id='rego_banner' style="text-align:center">
<?php if ($this->openx_banner) : ?>
	<?php echo $this->openx_banner; ?>
<?php else : ?>
	<image src="<?php echo  JURI::base(). 'components/com_tournament/images/' . $this->banner ?>" />
<?php endif; ?>
</div>

<script language="javascript">
function displayForm(frm,me) {
			document.getElementById('basicReg').style.display = 'none';
			document.getElementById('regoFormWrap').style.display = 'none';
			document.getElementById('awin').style.background = 'url(/components/com_topbetta_user/assets/bg_regoButt.jpg) repeat-x 0 -30px';
			document.getElementById('aplay').style.background = 'url(/components/com_topbetta_user/assets/bg_regoButt.jpg) repeat-x 0 -30px';
			document.getElementById(frm).style.display = 'block';
			document.getElementById(me).style.background = '#0097E9 url(/components/com_topbetta_user/assets/bg_regoButt.jpg) repeat-x 0 0';
};
</script>
<link rel="stylesheet" href="/components/com_topbetta_user/assets/view.basicregister.css?v=3.2" type="text/css" />
<style>
div#basicReg { display:block;}
div#basicReg input {
font-size: 12px;
font-weight: bold;
color: #006DA7;
padding: 2px 5px;
width: 235px;
border: 1px solid #0097E9;
background: #F1F6FF;
margin: 2px 0 7px 0;
float:left;}

div#basicReg label {
clear:both;
text-align: right;
line-height: 21px;
padding: 0 5px;
margin: 2px 2px 0 0;
color: #333;
font-size: 12px;
font-weight: bold;
width:150px;
float:left;
}

div#basicReg legend {
border: 1px solid #CCC;
background: #F2F2F2;
color: #111;
font-size: 10px;
font-weight: bold;
padding: 2px 10px;
margin: 0 0 4px 0; }

div#basicReg fieldset {
border: 1px solid #CCC;
color: #333;
margin: 10px;
padding: 10px;
background: white; }

div#basicReg #saveUserBasicButton, a.regTab {
border: 1px solid #22588F;
background: url(/components/com_topbetta_user/assets/bg_regoButt.jpg) repeat-x 0 -30px;
margin: 0 auto 0 auto;
padding: 0 2px;
font-size: 15px;
font-weight: bold;
color: white;
text-transform: uppercase;
text-align: center;
width: 230px;
height: 30px;
float: ;
cursor: pointer; }

div#basicReg #saveUserBasicButton:hover, a.regTab:hover {
border: 1px solid #22588F;
background: #0097E9 url(/components/com_topbetta_user/assets/bg_regoButt.jpg) repeat-x 0 0;
}
a.regTab { padding:5px 10px 5px 10px; text-decoration:none; margin-top:15px;}
h1.rego { size:24px; }
<?php 
if (JRequest::getVar('task') == 'quick_register') { ?>
div#regoFormWrap { display:none;}
<? } ?>
</style>

<div class="moduletable">
  <div class="formcontent">
  <p>&nbsp;</p>
  <p align="center"><a class="regTab" id="awin" href="javascript:displayForm('regoFormWrap','awin')" >Win real cash</a> OR <a id="aplay" class="regTab" href="javascript:displayForm('basicReg','aplay')" >Play for FREE</a></p>
  <p>&nbsp;</p>
    <div class="formcontentInr">
		<div id="basicReg" style="display:none;" >
        <script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.quick-signup-submit').click(function() {
	
					if((jQuery('#first_name').val()).trim()=='' || (jQuery('#first_name').val()).trim().length < 3)
					{
						alert('Please enter your first name. It must contain at least 3 characters.');
						 return false;
					}
					else if((jQuery('#last_name').val()).trim()=='' || (jQuery('#last_name').val()).trim().length < 3)
					{
						alert('Please enter your last name. It must contain at least 3 characters.');
						 return false;
					}
					else if((jQuery('#qsemail').val()).trim()=='' || !validate((jQuery('#qsemail').val()).trim()) )
					{
						alert('Please enter a valid email address');
						 return false;
					}
					else if((jQuery('#qsemail').val()).trim() !== (jQuery('#qsemail2').val()).trim())
					{
						alert('Confirm email address does not match with the email address');
						 return false;
					}
					else if ((jQuery('#qspassword').val()).trim() == '' || (jQuery('#qspassword').val()).trim().length < 6) {
						alert('Password can not be empty. It must contain at least 6 characters.');
						return false;
					}
					else if((jQuery('#qspassword').val()).trim() !== (jQuery('#qspassword2').val()).trim())
					{
						alert('Confirm email password does not match with the password');
						return false;
					}
					else if((jQuery('#qspassword').val()).trim() !== (jQuery('#qspassword2').val()).trim())
					{
						alert('Confirm email password does not match with the password');
						return false;
					}
					else if(!jQuery('#privacy').is(':checked'))
					{
						alert('Please agree to the privacy policy.');
						return false;
					}
					else if(!jQuery('#terms').is(':checked'))
					{
						alert('Please agree to the terms and conditions');
						return false;
					}
				});
			
			
			function validate(email) {
			   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			   if(reg.test(email) === false) {
				  //alert('Invalid Email Address');
				  return false;
			   } else return true;		   
			}
			});
		</script>
        	<form action="/user/register" name="quick-signup" method="post">
            <div class="Mlft">
            <fieldset>
              <legend>Personal Information</legend>
              <div>
            	<label for="first_name">First Name<span class="ast">*</span> </label>
                <input name="first_name" id="first_name" size="40" value="<?php echo isset($formData['first_name']) ? $formData['first_name'] : '' ?>" maxlength="50" type="text" class="<?php echo isset($sessFormErrors['first_name']) ? ' quick-signup-error' : ''?>" />
               
              </div>
              <div style="clear:both;"></div>
              <div>
                <label for="last_name">Last Name<span class="ast">*</span> </label>
                <input name="last_name" id="last_name" size="40" value="<?php echo isset($formData['last_name']) ? $formData['last_name'] : '' ?>" maxlength="50" type="text" class="<?php echo isset($sessFormErrors['last_name']) ? ' quick-signup-error' : ''?>" />
               
                </div>
                <div>
                <label>Mobile</label>
                	<input type="text" class="<?php echo isset($sessFormErrors['email']) ? ' quick-signup-error' : ''?>" name="mobile" id="qsmobile" value="<?php echo isset($formData['mobile']) ? $formData['mobile'] : '' ?>" />
                
                </div>
                <div>
                <label>Email<span class="ast">*</span></label>
                	<input type="text" class="<?php echo isset($sessFormErrors['email']) ? ' quick-signup-error' : ''?>" name="email" id="qsemail" value="<?php echo isset($formData['email']) ? $formData['email'] : '' ?>" />
                
                </div>
                <div>
                <label>Confirm Email<span class="ast">*</span></label>
                	<input type="text" class="<?php echo isset($sessFormErrors['email2']) ? ' quick-signup-error' : ''?>" name="email2" id="qsemail2" value="<?php echo isset($formData['email2']) ? $formData['email2'] : '' ?>" />
                
                </div>
                </fieldset>
                 <div style="clear:both;"></div>
                <fieldset>
              <div>
                <label>Password<span class="ast">*</span></label> <input type="password" class="<?php echo isset($sessFormErrors['password']) ? ' quick-signup-error' : ''?>" name="password" id="qspassword" value="<?php echo isset($formData['password']) ? $formData['password'] : '' ?>" onkeyup="chkPass(this.value);" />
                </div>
                <div>
                <label>Confirm Password<span class="ast">*</span></label> <input type="password" class="quick-signup-input" name="password2" id="qspassword2" value="<?php echo isset($formData['password2']) ? $formData['password2'] : '' ?>"/>
                </div>
                <div class="lgry">
                <label for="promo">Password Complexity</label>
                <div id="complexity">Too Short</div>
                <div class="hidebox">
                  <div class="hideboxtxt">Hide</div>
                  <input type="checkbox" id="mask" name="mask" value="1" checked="checked" onclick="togPwdMask();" />
                </div>
              </div>

              <div class="lgry">
                <label for="howuhear">Password Strength</label>
                            <div id="scorebar">0%</div>
              </div>
              <div class="lgry">
                <label for="minreq">Minimum Requirements</label>
                <div class="pwmsg" name="minreq" id="minreq">
                  Must contain at least 1 number.
                </div>
              </div>
              
            </fieldset>
                
                
             </div>  
             <div class="Mrgt">
              <fieldset>
              <legend>Agreement</legend>
              <div id="chkPriv" class="radiolbl">
                <span class="ast-agree">*</span><input class="chk" id="privacy" name="privacy" type="checkbox" <?php echo (isset($this->formData['privacy']) && $this->formData['privacy']) ? ' checked = "checked"' : ''?> /> <?php if(isset($this->sessFormErrors['privacy'])) echo '<div class="error rego-input-error">' . $this->escape($this->sessFormErrors['privacy']) . '</div>'?>
                <label class="chklbl" for="privacy">I have read and agree to the <a href="/terms-and-conditions" target="_blank">privacy policy</a> of TopBetta.</label>
                <?php if(isset($this->formErrors['privacy'])) echo '<div class="error">' . $this->escape($this->formErrors['privacy']) . '</div>'?>
              </div>
              <div id="chkTerms" class="radiolbl">
                <span class="ast-agree">*</span><input class="chk" id="terms" name="terms" type="checkbox" <?php echo (isset($this->formData['terms']) && $this->formData['terms']) ? ' checked = "checked"' : ''?> />
                <label class="chklbl" for="terms">I acknowledge that i am over 18 and i have read and agree<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to the <a href="/terms-and-conditions" target="_blank" >terms and conditions</a> of TopBetta.</label>
                <?php if(isset($this->formErrors['terms'])) echo '<div class="error">' . $this->escape($this->formErrors['terms']) . '</div>'?>
              </div>
              <div id="optin" class="radiolbl">
<?php
  $checked = true;
  if( is_array($this->formData) )
  {
    $checked = $this->formData['optbox'];
  }
?>
                  <input class="chk" id="optbox" type="checkbox" name="optbox" <?php echo $checked ? 'checked="checked"' : '' ?> />
                  <label class="chklbl" for="optbox">I would like to receive the latest news and offers from TopBetta.</label>
                </div>
            </fieldset>
            	<div class="regoButt">
                <input type="submit" style="margin-left:110px;" name="q-signup-submit" id="saveUserBasicButton" class="quick-signup-submit" value="Start!"/>
                </div>
            </div>
                 <div style="clear:both;"></div>
                <input type="hidden" name="source" value="<?php if($this->escape($this->formErrors['source'])) {
			  													echo $this->escape($this->formErrors['source']); 
		  													}
															else {
																echo htmlspecialchars($_SERVER['HTTP_REFERER']);
															} ?>" />
                <input type="hidden" name="option" value="com_topbetta_user" />
                <input type="hidden" name="task" value="quick_register" />
                <input type="hidden" name="from_url" value="<?php echo $this->escape(JURI::current()) ?>" />
                <input type="hidden" name="quick_registration_code" value="<?php echo $this->escape($quick_registration_code) ?>" />
            
            </form>
        </div>
      <div id="regoFormWrap">
        <form id="regoForm" name="regoForm" action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user' ); ?>" method="post">

          <div class="Mlft">
            <fieldset>
              <legend>Personal Information</legend>
              <div>
                <label for="username">Username<span class="ast">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo $this->escape($this->formData['username']) ?>" maxlength="30" />
                <?php if(isset($this->formErrors['username'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['username']) . '</div>'?>
              </div>

              <div>
                <label for="title">Title<span class="ast">*</span></label>
                <select name="title" id="title" class="titl">
                  <option value=""></option>
<?php
  foreach( $this->options['title'] as $k => $v )
  {
    $selected = ($k == $this->formData['title'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                </select>
                <?php if(isset($this->formErrors['title'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['title']) . '</div>'?>
              </div>

              <div>
                <label for="first_name">First Name<span class="ast">*</span></label>
                <input name="first_name" id="first_name" size="40" value="<?php echo $this->escape($this->formData['first_name']) ?>" maxlength="50" type="text" />
                <?php if(isset($this->formErrors['first_name'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['first_name']) . '</div>'?>
              </div>

              <div>
                <label for="last_name">Last Name<span class="ast">*</span></label>
                <input name="last_name" id="last_name" maxlength="50" value="<?php echo $this->escape($this->formData['last_name']) ?>" type="text" />
                <?php if(isset($this->formErrors['last_name'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['last_name']) . '</div>'?>
              </div>

              <div>
                <label>Date of Birth<span class="ast">*</span></label>
                          <select id="dobd" class="dobd" name="dob_day">
                            <option value="">-Day-</option>
<?php
  foreach( $this->options['day'] as $k => $v )
  {
    $selected = ($k == $this->formData['dob_day'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                          </select>
                          <select id="dobm" class="dobm" name="dob_month">
                              <option value="">&nbsp;-- Month --&nbsp;</option>
<?php
  foreach( $this->options['month'] as $k => $v )
  {
    $selected = ($k == $this->formData['dob_month'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                          </select>

                          <select id="doby" class="doby" name="dob_year">
                              <option value="">- Year -&nbsp;</option>
<?php
  foreach( $this->options['year'] as $k => $v )
  {
    $selected = ($k == $this->formData['dob_year'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                </select>
                <?php if(isset($this->formErrors['dob'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['dob']) . '</div>'?>
              </div>
            </fieldset>

            <fieldset>
              <legend>Phone - Email Details</legend>

              <div>
                <label for="mobile">Mobile Number<span class="ast">*</span></label>
                <input name="mobile" type="text" maxlength="15" value="<?php echo $this->escape($this->formData['mobile']) ?>" id="mobile" />
                <?php if(isset($this->formErrors['mobile'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['mobile']) . '</div>'?>
              </div>

              <div>
                <label for="phone">Home Number</label>
                <input class="phone" name="phone" type="text" maxlength="15" value="<?php echo $this->escape($this->formData['phone']) ?>" id="phone" />
                <?php if(isset($this->formErrors['phone'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['phone']) . '</div>'?>
              </div>

              <div>
                <label for="email">Email Address<span class="ast">*</span></label>
                <input type="text" id="email" maxlength="100" name="email" value="<?php echo $this->escape($this->formData['email']) ?>" maxlength="100" />
<?php
	if(isset($this->formErrors['email']))
	{
		if( isset($this->formErrors['email_activation']) && $this->formErrors['email_activation'] )
		{
			echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['email']) . ' <a href="' . JRoute::_("/user/resend-verification/" . htmlspecialchars(rawurlencode(rawurlencode($this->formData['email'])))) . '">Resend your Verification Email</a></div>';
		}
		else if(isset($this->formErrors['email_activation']) && !$this->formErrors['email_activation'] )
		{
			echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['email']) . ' <a href="' . JRoute::_("/user/reset") . '">Request a new password</a></div>';
		}
		else
		{
			echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['email']) . '</div>';
		}
	}
?>
              </div>

              <div>
                <label for="email2">Confirm Email Address<span class="ast">*</span></label>
                <input type="text" id="email2" name="email2" value="<?php echo $this->escape($this->formData['email2']) ?>" maxlength="100" />
                <?php if(isset($this->formErrors['email2'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['email2']) . '</div>'?>
              </div>
            </fieldset>

            <fieldset>
              <div>
                <label for="password">Password<span class="ast">*</span></label>
                <input type="password" id="password" maxlength="30" name="password" value="<?php echo $this->escape($this->formData['password']) ?>" autocomplete="off" onkeyup="chkPass(this.value);" />
                <input type="text" id="passwordTxt" name="passwordTxt" value="" autocomplete="off" onkeyup="chkPass(this.value);" class="dispnone" />
                <?php if(isset($this->formErrors['password'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['password']) . '</div>'?>
              </div>

              <div>
                <label for="password2">Confirm Password<span class="ast">*</span></label>
                <input type="password" id="password2" maxlength="30" name="password2" value="<?php echo $this->escape($this->formData['password2']) ?>" />
                <input type="text" id="password2Txt" name="password2Txt" value="" autocomplete="off" onkeyup="chkPass(this.value);" class="dispnone"/>
                <?php if(isset($this->formErrors['password2'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['password2']) . '</div>'?>
              </div>

              <div class="lgry">
                <label for="promo">Password Complexity</label>
                <div id="complexity">Too Short</div>
                <div class="hidebox">
                  <div class="hideboxtxt">Hide</div>
                  <input type="checkbox" id="mask" name="mask" value="1" checked="checked" onclick="togPwdMask();" />
                </div>
              </div>

              <div class="lgry">
                <label for="howuhear">Password Strength</label>
                            <div id="scorebar">0%</div>
              </div>
              <div class="lgry">
                <label for="minreq">Minimum Requirements</label>
                <div class="pwmsg" name="minreq" id="minreq">
                  Must contain at least 1 number.
                </div>
              </div>
            </fieldset>
            
            <div class="regoButt"> 
              <div class="butt_lbl">Fields marked with an asterisk (<span class="ast3">*</span>)<br />are required to be filled in.</div> 
               
            </div>
          </div>

           <div class="Mrgt">
            <fieldset>
              <legend>Home Address</legend>
              <div>
                <label for="address">Street Address<span class="ast">*</span></label>
                <input type="text" id="address" name="street" value="<?php echo $this->escape($this->formData['street']) ?>" maxlength="100" />
                <?php if(isset($this->formErrors['street'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['street']) . '</div>'?>
              </div>
              <div>
                <label for="suburb">Suburb / City<span class="ast">*</span></label>
                <input type="text" id="suburb" name="city" value="<?php echo $this->escape($this->formData['city']) ?>" maxlength="50" />
                <?php if(isset($this->formErrors['city'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['city']) . '</div>'?>
              </div>

              <div>
                <label for="state">State<span class="ast">*</span></label>
                <select name="state" id="state">
                  <option value=""> &nbsp;&nbsp;--------- Please Select --------&nbsp;&nbsp; </option>
<?php
  foreach( $this->options['state'] as $k => $v )
  {
    $selected = ($k == $this->formData['state'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                </select>
                <?php if(isset($this->formErrors['state'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['state']) . '</div>'?>
              </div>

              <div>
                <label for="country">Country<span class="ast">*</span></label>
                <select name="country" id="country">
<?php
  foreach( $this->country_list as $k => $v )
  {
    $selected = ($k == $this->formData['country'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v['name']) . '</option>';
  }
?>
                </select>
                <?php if(isset($this->formErrors['country'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['country']) . '</div>'?>
              </div>

              <div>
                <label for="pcode">Postal Code<span class="ast">*</span></label>
                <input type="text" id="pcode" name="postcode" value="<?php echo $this->escape($this->formData['postcode']) ?>" maxlength="10" />
                <?php if(isset($this->formErrors['postcode'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['postcode']) . '</div>'?>
              </div>
            </fieldset>
            <fieldset class="gry">
              <legend>Optional Information</legend>
              <div>
                <label for="ref_id">Promotion code</label>
                <input type="text" id="promo_code" name="promo_code" value="<?php echo $this->escape($this->formData['promo_cdoe']) ?>" maxlength="11" />
                <?php if(isset($this->formErrors['promo_code'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['promo_code']) . '</div>'?>
              </div>
              <div>
                <label for="heard_about">How did you hear about us?</label>
                <select name="heard_about" id="heard_about">
                  <option value=""> &nbsp;--------- Please Select --------&nbsp; </option>
<?php
  foreach( $this->options['heard_about'] as $k => $v )
  {
    $selected = ($k == $this->formData['heard_about'] ? ' selected="selected"' : '');
    echo '<option value="' . $this->escape($k) .'"'. $selected . '>' . $this->escape($v) . '</option>';
  }
?>
                </select>
                <?php if(isset($this->formErrors['heard_about'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['heard_about']) . '</div>'?>
              </div>
              <div>
                <label for="details">Additional Information (#)</label>
                <textarea class="txtarea" name="heard_about_info" id="heard_about_info" rows="2"><?php echo $this->escape($this->formData['heard_about_info']) ?></textarea>
                <?php if(isset($this->formErrors['heard_about_info'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['heard_about_info']) . '</div>'?>
              </div>
            </fieldset>
            <fieldset>
              <legend>Agreement</legend>
              <div id="chkPriv" class="radiolbl">
                <span class="ast-agree">*</span><input class="chk" id="privacy" name="privacy" type="checkbox" <?php echo (isset($this->formData['privacy']) && $this->formData['privacy']) ? ' checked = "checked"' : ''?> />
                <label class="chklbl" for="privacy">I have read and agree to the <a href="/terms-and-conditions" target="_blank">privacy policy</a> of TopBetta.</label>
                <?php if(isset($this->formErrors['privacy'])) echo '<div class="error">' . $this->escape($this->formErrors['privacy']) . '</div>'?>
              </div>
              <div id="chkTerms" class="radiolbl">
                <span class="ast-agree">*</span><input class="chk" id="terms" name="terms" type="checkbox" <?php echo (isset($this->formData['terms']) && $this->formData['terms']) ? ' checked = "checked"' : ''?> />
                <label class="chklbl" for="terms">I acknowledge that i am over 18 and i have read and agree<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to the <a href="/terms-and-conditions" target="_blank" >terms and conditions</a> of TopBetta.</label>
                <?php if(isset($this->formErrors['terms'])) echo '<div class="error">' . $this->escape($this->formErrors['terms']) . '</div>'?>
              </div>
              <div id="optin" class="radiolbl">
<?php
  $checked = true;
  if( is_array($this->formData) )
  {
    $checked = $this->formData['optbox'];
  }
?>
                  <input class="chk" id="optbox" type="checkbox" name="optbox" <?php echo $checked ? 'checked="checked"' : '' ?> />
                  <label class="chklbl" for="optbox">I would like to receive the latest news and offers from TopBetta.</label>
                </div>
            </fieldset>
            <div id="formMsgWrap">
              <div id="formMsg">&nbsp;</div>
            </div>
            <div class="regoButt">
              <input id="regoButton" type="submit" value="SUBMIT NOW!" onclick="this.disabled=true; this.form.submit();" />
            </div>
          </div>
          <div class="clr"></div>
          <input type="hidden" name="task" value="register_save" />
          <input type="hidden" name="id" value="0" />
          <input type="hidden" name="gid" value="0" />

          <input type="hidden" name="source" value="<?php if($this->escape($this->formData['source'])) {
			  													echo $this->escape($this->formData['source']); 
		  													}
															else {
																echo htmlspecialchars($_SERVER['HTTP_REFERER']);
															} ?>" />
          <input type="hidden" name="banner_id" value="<?php echo $this->escape($this->formData['banner_id']) ?>" />
          <?php echo JHTML::_( 'form.token' ); ?>

<!-- ######################################################################################################### -->
          <div style="display: none;">
            <table id="tablePwdStatus" cellpadding="5" cellspacing="1" border="0">
              <tr>
                <th colspan="2">Additions</th>
                <th class="txtCenter">Type</th>
                <th class="txtCenter">Rate</th>
                <th class="txtCenter">Count</th>
                <th class="txtCenter">Bonus</th>
              </tr>
              <tr>
                <td width="1%"><div id="div_nLength" class="fail">&nbsp;</div></td>
                <td width="94%">Number of Characters</td>
                <td width="1%" class="txtCenter">Flat</td>
                <td width="1%" class="txtCenter italic">+(n*4)</td>
                <td width="1%"><div id="nLength" class="box">&nbsp;</div></td>
                <td width="1%"><div id="nLengthBonus" class="boxPlus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nAlphaUC" class="fail">&nbsp;</div></td>
                <td>Uppercase Letters</td>
                <td class="txtCenter">Cond/Incr</td>
                <td nowrap="nowrap" class="txtCenter italic">+((len-n)*2)</td>
                 <td><div id="nAlphaUC" class="box">&nbsp;</div></td>
                <td><div id="nAlphaUCBonus" class="boxPlus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nAlphaLC" class="fail">&nbsp;</div></td>
                <td>Lowercase Letters</td>
                <td class="txtCenter">Cond/Incr</td>
                <td class="txtCenter italic">+((len-n)*2)</td>
                <td><div id="nAlphaLC" class="box">&nbsp;</div></td>
                <td><div id="nAlphaLCBonus" class="boxPlus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nNumber" class="fail">&nbsp;</div></td>
                <td>Numbers</td>
                <td class="txtCenter">Cond</td>
                <td class="txtCenter italic">+(n*4)</td>
                <td><div id="nNumber" class="box">&nbsp;</div></td>
                <td><div id="nNumberBonus" class="boxPlus">&nbsp;</div></td>
               </tr>
              <tr>
                <td><div id="div_nSymbol" class="fail">&nbsp;</div></td>
                <td>Symbols</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">+(n*6)</td>
                <td><div id="nSymbol" class="box">&nbsp;</div></td>
                <td><div id="nSymbolBonus" class="boxPlus">&nbsp;</div></td>
               </tr>
              <tr>
                <td><div id="div_nMidChar" class="fail">&nbsp;</div></td>
                <td>Middle Numbers or Symbols</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">+(n*2)</td>
                <td><div id="nMidChar" class="box">&nbsp;</div></td>
                <td><div id="nMidCharBonus" class="boxPlus">&nbsp;</div></td>
               </tr>
              <tr>
                <td><div id="div_nRequirements" class="fail">&nbsp;</div></td>
                <td>Requirements</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">+(n*2)</td>
                <td><div id="nRequirements" class="box">&nbsp;</div></td>
                <td><div id="nRequirementsBonus" class="boxPlus">&nbsp;</div></td>
               </tr>
              <tr>
                <th colspan="6">Deductions</th>
              </tr>
              <tr>
                <td width="1%"><div id="div_nAlphasOnly" class="pass">&nbsp;</div></td>
                <td width="94%">Letters Only</td>
                <td width="1%" class="txtCenter">Flat</td>
                <td width="1%" class="txtCenter italic">-n</td>
                <td width="1%"><div id="nAlphasOnly" class="box">&nbsp;</div></td>
                <td width="1%"><div id="nAlphasOnlyBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nNumbersOnly" class="pass">&nbsp;</div></td>
                <td>Numbers Only</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-n</td>
                <td><div id="nNumbersOnly" class="box">&nbsp;</div></td>
                <td><div id="nNumbersOnlyBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nRepChar" class="pass">&nbsp;</div></td>
                <td>Repeat Characters (Case Insensitive)</td>
                <td class="txtCenter">Comp</td>
                <td nowrap="nowrap" class="txtCenter italic"> - </td>
                <td><div id="nRepChar" class="box">&nbsp;</div></td>
                <td><div id="nRepCharBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nConsecAlphaUC" class="pass">&nbsp;</div></td>
                <td>Consecutive Uppercase Letters</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*2)</td>
                <td><div id="nConsecAlphaUC" class="box">&nbsp;</div></td>
                <td><div id="nConsecAlphaUCBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nConsecAlphaLC" class="pass">&nbsp;</div></td>
                <td>Consecutive Lowercase Letters</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*2)</td>
                <td><div id="nConsecAlphaLC" class="box">&nbsp;</div></td>
                <td><div id="nConsecAlphaLCBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nConsecNumber" class="pass">&nbsp;</div></td>
                <td>Consecutive Numbers</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*2)</td>
                <td><div id="nConsecNumber" class="box">&nbsp;</div></td>
                <td><div id="nConsecNumberBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nSeqAlpha" class="pass">&nbsp;</div></td>
                <td>Sequential Letters (3+)</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*3)</td>
                <td><div id="nSeqAlpha" class="box">&nbsp;</div></td>
                <td><div id="nSeqAlphaBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nSeqNumber" class="pass">&nbsp;</div></td>
                <td>Sequential Numbers (3+)</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*3)</td>
                <td><div id="nSeqNumber" class="box">&nbsp;</div></td>
                <td><div id="nSeqNumberBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <td><div id="div_nSeqSymbol" class="pass">&nbsp;</div></td>
                <td>Sequential Symbols (3+)</td>
                <td class="txtCenter">Flat</td>
                <td class="txtCenter italic">-(n*3)</td>
                <td><div id="nSeqSymbol" class="box">&nbsp;</div></td>
                <td><div id="nSeqSymbolBonus" class="boxMinus">&nbsp;</div></td>
              </tr>
              <tr>
                <th colspan="6">Legend</th>
              </tr>
              <tr>
                <td colspan="6">
                  <ul id="listLegend">
                    <li><div class="exceed imgLegend">&nbsp;</div> <span class="bold">Exceptional:</span> Exceeds minimum standards. Additional bonuses are applied.</li>
                    <li><div class="pass imgLegend">&nbsp;</div> <span class="bold">Sufficient:</span> Meets minimum standards. Additional bonuses are applied.</li>
                    <li><div class="warn imgLegend">&nbsp;</div> <span class="bold">Warning:</span> Advisory against employing bad practices. Overall score is reduced.</li>
                    <li><div class="fail imgLegend">&nbsp;</div> <span class="bold">Failure:</span> Does not meet the minimum standards. Overall score is reduced.</li>
                  </ul>
                </td>
              </tr>
            </table>
             <table id="tablePwdNotes" cellpadding="5" cellspacing="1" border="0">
              <tr>
                <th>Quick Footnotes</th>
              </tr>
              <tr>
                <td>
                  &bull; <strong>Flat:</strong> Rates that add/remove in non-changing increments.<br />
                  &bull; <strong>Incr:</strong> Rates that add/remove in adjusting increments.<br />
                  &bull; <strong>Cond:</strong> Rates that add/remove depending on additional factors.<br />
                  &bull; <strong>Comp:</strong> Rates that are too complex to summarize. See source code for details.<br />
                  &bull; <strong>n:</strong> Refers to the total number of occurrences.<br />
                  &bull; <strong>len:</strong> Refers to the total password length.<br />
                  &bull; Additional bonus scores are given for increased character variety.<br />
                  &bull; Final score is a cumulative result of all bonuses minus deductions.<br />
                  &bull; Final score is capped with a minimum of 0 and a maximum of 100.<br />
                  &bull; Score and Complexity ratings are not conditional on meeting minimum requirements.<br />
                </td>
              </tr>
              <tr>
                <th>DISCLAIMER</th>
              </tr>
              <tr>
                <td>
                  <p>This application is designed to assess the strength of password strings.  The instantaneous visual feedback provides the user a means to improve the strength of their passwords, with a hard focus on breaking the typical bad habits of faulty password formulation.  Since no official weighting system exists, we created our own formulas to assess the overall strength of a given password.  Please note, that this application does not utilize the typical "days-to-crack" approach for strength determination.  We have found that particular system to be severely lacking and unreliable for real-world scenarios.  This application is neither perfect nor foolproof, and should only be utilized as a loose guide in determining methods for improving the password creation process. </p>
                </td>
              </tr>
            </table>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<?php echo $this->registration_tracking_code ?>
