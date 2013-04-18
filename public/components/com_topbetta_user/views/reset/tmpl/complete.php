<?php
defined('_JEXEC') or die;

?>

<div class="resetForm">
	<div class="pwresetFormInr">
		<div class="hdrBar"><div id="hdrBar_account"></div>Reset your Password</div>
		<form action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user&task=completereset' ); ?>" method="post" class="josForm">
			<div class="frm">
				<p><?php echo JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>
				<div class="frmL">
					<div>
						<label for="password"><?php echo JText::_('Password'); ?>:</label>
						<input type="password" id="password" name="password" value="" autocomplete="off" onkeyup="chkPass(this.value);" />
						<input type="text" id="passwordTxt" name="passwordTxt" value="" autocomplete="off" onkeyup="chkPass(this.value);" class="dispnone" />
						<?php if($this->formErrors['password']) echo '<div class="error">' . $this->escape($this->formErrors['password']) . '</div>'?>
					</div>
					<div>
						<label for="password2"><?php echo JText::_('Confirm Password'); ?>:</label>
						<input id="password2" name="password2" type="password" class="required validate-password" autocomplete="off" onkeyup="chkPass(this.value);" />
						<input type="text" id="password2Txt" name="password2Txt" value="" class="dispnone"  autocomplete="off" onkeyup="chkPass(this.value);" />
						<?php if($this->formErrors['password2']) echo '<div class="error">' . $this->escape($this->formErrors['password2']) . '</div>'?>
					</div>
					<div class="clr"></div>
	
					<div class="lgry">
						<label for="promo">Password Complexity:</label>
						<div class="hidebox">
							<div class="hideboxtxt">Hide</div>
							<input type="checkbox" id="mask" name="mask" value="1" checked="checked" onclick="togPwdMask();" />
						</div>
						<div id="complexity">Too Short</div>
					</div>
	
					<div class="lgry">
						<label for="howuhear">Password Strength:</label>
							<button type="submit" class="validate"><?php echo JText::_('RESET'); ?></button>
							<input type="hidden" name="email" value="<?php echo $user->email; ?>" />
	                        <div id="scorebar">0%</div>
					</div>

				</div>
				<div class="frmR">
					<h4>Minimum Requirements</h4>
					<div class="pwmsg" name="details" id="details">
						&bull; Minimum 8 characters in length<br />
						&bull; Must contain at least 1 uppercase letter<br />
						&nbsp;&nbsp;&nbsp;&nbsp;and 1 number or symbol.
					</div>
				</div>				
			</div>


		
			<?php echo JHTML::_( 'form.token' ); ?>

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
		<div class="clr"></div>
	</div>
</div>