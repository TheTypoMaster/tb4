<?php defined('_JEXEC') or die('Restricted access'); 
if ($this->fastpass_login):
	echo $this->fastpass_login; ?>
	<div class="message">You are now logged in, please wait for this window to close.</div>
<?php 	
elseif ($this->fastpass_logout):
?>
	<div class="message">You are logging out.</div>
<?php
else:
?>
<div class="login-wrap">
	<div class="login-head"><img src="components/com_topbetta_user/assets/top-betta_login-head.gif" alt="TopBetta - Live Racing & Sports Tournament Betting, Change the way you bet online!" border=""/></div>
    <div class="login-main">
<form action="<?php echo JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>" method="post" name="com-login" id="login-form">
 <div class="login-input">
	<label for="mod_login_username" class="uc-login-user">
		<span><?php echo JText::_('Username') ?></span>
		<input name="username" id="mod_login_username" type="text" class="input_login" alt="username" size="10" />
	</label>
	<label for="mod_login_password" class="uc-login-password">
		<span><?php echo JText::_('Password') ?></span>
		<input type="password" id="mod_login_password" name="passwd" class="input_login" size="10" alt="password" />
	</label>    
 <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
    <label for="mod_login_remember"><?php echo JText::_('Remember me') ?></label>
    <input type="checkbox" id="mod_login_remember" name="remember" class="inputbox" value="yes" alt="Remember Me" />
  <?php endif; ?>
  <input type="submit" name="Submit" class="login-button" value="<?php echo JText::_('LOGIN') ?>" />
</div><!-- close login-input -->
 <div class="uc-login-links">
    <a href="<?php echo JRoute::_( '/user/reset' ); ?>" target="_blank">
    <?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
    <a href="<?php echo JRoute::_( '/user/remind' ); ?>" target="_blank">
    <?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
 </div><!-- close uc-login-links -->
  <?php
  $usersConfig = &JComponentHelper::getParams( 'com_users' );
  if ($usersConfig->get('allowUserRegistration')) : ?>
  <div class="uc-login-createacc">
    <a href="<?php echo JRoute::_( '/user/register' ); ?>" target="_blank">
      <img src="components/com_topbetta_user/assets/btn-create-account.gif" border="0" alt="Create an account"/></a>
   </div><!-- close uc-login-links -->
  <?php endif; ?>
  <input type="hidden" name="getsatisfaction" value="true" />
  <input type="hidden" name="option" value="com_topbetta_user" />
  <input type="hidden" name="task" value="login" />
  <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
 </div><!-- close login-main -->
    <div class="clear"></div>
</div>
<?php  
endif;
?>
