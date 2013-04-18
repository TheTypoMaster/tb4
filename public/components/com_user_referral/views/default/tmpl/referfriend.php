<div id="bettaWrap" >
	<div class="moduletable">
		<h3>Refer a Friend</h3>
		<div class="innerWrap">
			<form action="index.php" method="POST" id="refer_friend">
    			<div id="refer-friend">
    				<img src="/components/com_user_referral/images/referafriend_banner.gif" with="427" height="127" />
    				<h4>Tell your friends about TopBetta and be rewarded!</h4>
    				<p>
    					As a TopBetta member you can Refer A Friend and earn yourself <?php echo $this->escape($this->referral_payment)?> Tournament Dollars for every friend who registers at TopBetta.
    					Conditions apply - See <a href="/terms-and-conditions#referafriend">terms and conditions</a>.
    				</p>
<?php
if( $this->is_registered)
{
?>
    				<h4>How do l refer a friend?</h4>
    				<p>Firstly your <strong>REFERRAL ID</strong> is: <strong><?php echo $this->escape($this->userid)?></strong></p>
    				<p>Ok let’s get started...</p>
                	<p>There are two options to refer a friend:</p>

                	<div class="option">
                		<img src="/components/com_user_referral/images/option1.png" with="138" height="44" />
                		<p>Tell your friend to enter your REFERRAL ID when they register at TopBetta.com. It’s that easy!</p>
    					<p>Firstly your <strong>REFERRAL ID</strong> is: <strong><?php echo $this->escape($this->userid)?></strong></p>
                	</div>

    				<div class="option">
                		<img src="/components/com_user_referral/images/option2.png" with="138" height="44" />
                           <p>Send your friend an email that will explain how to register for FREE at Topbetta.</p>
                        	<table cellspacing="12" cellpadding="12" id="referral-table">
                            	<tr>
                            		<th>Your friend’s email address:</th>
                            		<td>
                            			<input type="text" name="friend_email" value="<?php echo $this->escape($this->formData['friend_email']) ?>" />
                            			<?php if($this->formErrors['friend_email']) echo '<div class="error">' . $this->escape($this->formErrors['friend_email']) . '</div>'?>
                            		</td>
                            	</tr>
                            	<tr>
                            		<th>Subject:</th>
                            		<td>
                            			<input type="text" name="subject" value="<?php echo $this->escape($this->formData['subject']) ?>" />
                            			<?php if($this->formErrors['subject']) echo '<div class="error">' . $this->escape($this->formErrors['subject']) . '</div>'?>
                            		</td>
                            	</tr>
                            	<tr>
                            		<th>Add your message:</th>
                            		<td>
                            			<textarea name="message" rows="8" cols="30"><?php echo $this->escape($this->formData['message']) ?></textarea>
                            			<?php if($this->formErrors['message']) echo '<div class="error">' . $this->escape($this->formErrors['message']) . '</div>'?>
                            		</td>
                            	</tr>
                        	</table>
                        	<p>The following information will be included in your email.</p>
                        	
                        	<div id="referral-email-txt"><?php echo $this->referral_email_txt ?></div>

                            <p class="middle"><input type="submit" value="" name="submit" id="submit" class="button" /></p>
                            <input type="hidden" name="option" value="com_user_referral" />
                            <input type="hidden" name="task" value="send_referral_email" />
                            <input type="hidden" name="itemid" value="<?php echo $this->escape($this->itemid) ?>" />
                	</div>
<?php
}
else
{
?>
    				<p>You must be a registered member to refer a friend.<p>
    				<p><input type="button" value="REGISTER NOW" id="regoButton" onclick="window.location='/user/register'"></p>
<?php
}
?>
    			</div>
			</form>
		</div>
	</div>
</div>