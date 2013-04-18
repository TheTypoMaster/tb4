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

<div class="moduletable">
  <div class="formcontent">
    <div class="formcontentInr">

      <div id="regoFormWrap">
        <form id="regoForm" name="regoForm" action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user' ); ?>" method="post">

          <div class="Mlft">
            <fieldset>
              <legend>Personal Information</legend>
              <div>
                <label for="username">Username<span class="ast">*</span></label>
                <!-- <input type="text" id="username" name="username" value="<?php echo $this->escape($this->formData['username']) ?>" maxlength="30" /> 
				<?php if(isset($this->formErrors['username'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['username']) . '</div>'?>-->
				<input type="text" readonly="readonly" id="username" name="username" value="<?php echo $this->escape($this->user->username); ?>" maxlength="30" />
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
              <?php 
			  $name = explode(' ',$this->user->name);
			  ?>
              <div>
                <label for="first_name">First Name<span class="ast">*</span></label>
                <input name="first_name" readonly="readonly" id="first_name" size="40" value="<?php echo $this->escape($name[0]); ?>" maxlength="50" type="text" />
                <?php if(isset($this->formErrors['first_name'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['first_name']) . '</div>'?>
              </div>

              <div>
                <label for="last_name">Last Name<span class="ast">*</span></label>
                <input name="last_name" readonly="readonly" id="last_name" maxlength="50" value="<?php echo $this->escape($name[1]); ?>" type="text" />
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
              <legend>Phone Details</legend>

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

             <!--  <div>
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
	
$obUser = JFactory::getUser();
?>
              </div>

              <div>
                <label for="email2">Confirm Email Address<span class="ast">*</span></label>
                <input type="text" id="email2" name="email2" value="<?php echo $this->escape($this->formData['email2']) ?>" maxlength="100" />
                <?php if(isset($this->formErrors['email2'])) echo '<div class="error rego-input-error">' . $this->escape($this->formErrors['email2']) . '</div>'?>
              </div> -->
            </fieldset>

           <!--  <fieldset>
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
                  Must contain at least 1 uppercase letter<br />
                  and 1 number or symbol.
                </div>
              </div>
            </fieldset> -->
            
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
                <input type="text" id="promo_code" name="promo_code" value="<?php echo $this->escape($this->formData['promo_code']) ?>" maxlength="11" />
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
          <input type="hidden" name="task" value="upgrade_save" />
          <input type="hidden" name="id" value="0" />
          <input type="hidden" name="gid" value="0" />

          <input type="hidden" name="source" value="<?php echo $this->escape($this->formData['source']) ?>" />
          <input type="hidden" name="banner_id" value="<?php echo $this->escape($this->formData['banner_id']) ?>" />
          <?php //echo JHTML::_( 'form.token' ); ?>

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
