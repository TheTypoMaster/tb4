<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if ($user->guest) : ?>
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
    <div class="quick-signup-right">
    	<div class="right-col-imgheader"><img src="templates/topbetta/images/join-now-for-free.png" border="0" alt="Join Now For Free!"/></div>
        <div class="quick-signup-form">
        	<form class="quick-signup" action="/user/register" name="quick-signup" method="post">

            	<label for="first_name">FIRST NAME:
                <input name="first_name" id="first_name" size="40" value="<?php echo isset($formData['first_name']) ? $formData['first_name'] : '' ?>" maxlength="50" type="text" class="quick-signup-input <?php echo isset($sessFormErrors['first_name']) ? ' quick-signup-error' : ''?>" />
                </label>
                <label for="last_name">LAST NAME:
                <input name="last_name" id="last_name" size="40" value="<?php echo isset($formData['last_name']) ? $formData['last_name'] : '' ?>" maxlength="50" type="text" class="quick-signup-input <?php echo isset($sessFormErrors['last_name']) ? ' quick-signup-error' : ''?>" />
                </label>
                <label>
                	EMAIL:
                	<input type="text" class="quick-signup-input <?php echo isset($sessFormErrors['email']) ? ' quick-signup-error' : ''?>" name="email" id="qsemail" value="<?php echo isset($formData['email']) ? $formData['email'] : '' ?>" />
                </label>
                <label>
                	CONFIRM EMAIL:
                	<input type="text" class="quick-signup-input <?php echo isset($sessFormErrors['email2']) ? ' quick-signup-error' : ''?>" name="email2" id="qsemail2" value="<?php echo isset($formData['email2']) ? $formData['email2'] : '' ?>" />
                </label>
                <label>PASSWORD: <input type="password" class="quick-signup-input <?php echo isset($sessFormErrors['password']) ? ' quick-signup-error' : ''?>" name="password" id="qspassword" value="<?php echo isset($formData['password']) ? $formData['password'] : '' ?>" /></label>
                <label>CONFIRM PASSWORD: <input type="password" class="quick-signup-input" name="password2" id="qspassword2" value="<?php echo isset($formData['password2']) ? $formData['password2'] : '' ?>"/></label>
                <label class="marketing-login-agree" style="text-align:left;" for="privacy"><input class="chk" id="privacy" name="privacy" type="checkbox" <?php echo (isset($formData['privacy']) && $formData['privacy']) ? ' checked = "checked"' : ''?> /> I have read and agree to the <a style="color:#fff;" href="/terms-and-conditions" target="_blank">privacy policy</a> of TopBetta.</label>
                <label class="marketing-login-agree" style="text-align:left;" for="terms"><input class="chk" id="terms" name="terms" type="checkbox" <?php echo (isset($formData['terms']) && $formData['terms']) ? ' checked = "checked"' : ''?> /> I acknowledge that i am over 18 and i have read and agree to the <a style="color:#fff;" href="/terms-and-conditions" target="_blank" >terms and conditions</a> of TopBetta.</label>
                <label class="marketing-login-agree" style="text-align:left;"><input class="quick-signup-check " type="checkbox" name="optbox" <?php echo isset($formData['optbox']) ? 'checked="checked"' : '' ?> /> I agree to receive marketing messages from TopBetta</label>
                <div style="clear:both;"></div>
                <input type="submit" class="quick-signup-submit"  name="q-signup-submit" value="Start!"/>
                <input type="hidden" name="option" value="com_topbetta_user" />
                <input type="hidden" name="task" value="quick_register" />
                <input type="hidden" name="from_url" value="<?php echo $this->escape(JURI::current()) ?>" />
                <input type="hidden" name="quick_registration_code" value="<?php echo $this->escape($quick_registration_code) ?>" />
                <input type="hidden" name="source" value="<?php if($this->escape($this->formErrors['source'])) {
			  													echo $this->escape($this->formErrors['source']); 
		  													}
															else {
																echo htmlspecialchars($_SERVER['HTTP_REFERER']);
															} ?>" />
            </form>
        </div>

        <div class="quick-signup-links">
            <a href="/user/register" style="margin-top: -5px;"><img src="/templates/topbetta/images/btn_join-cash.png"></a>
            <a href="/user/reset" style="text-align: center;">Forgot your password?</a>
        </div>

    </div><!-- close quick-signup-right -->


<?php else :

$browser_info = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

if ( stristr($browser_info, "msie") ) :
	$resize = "x: 365 , y: 486";
else :
	$resize = "x: 390 , y: 492";
endif;

?>
    <div class="right-col-ad">
    	<a rel="{handler: 'iframe', size: { <?=$resize ?> }}" href="/index.php?option=com_tournament&task=privatetournament&format=raw" class="modal"><?php echo $right_banner ?></a>
    </div><!-- close right-col-ad -->
<?php endif; ?>
