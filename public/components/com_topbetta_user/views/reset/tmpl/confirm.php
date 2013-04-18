<?php defined('_JEXEC') or die; ?>

<div class="resetForm">
	<div class="resetFormInr">
		<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
			<div class="hdrBar"><div id="hdrBar_account"></div>Confirm Your Account</div>
		<?php endif; ?>

			<form action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user&task=confirmreset' ); ?>" method="post" class="josForm form-validate">

				<div class="desc"><?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></div>
				<div class="frm">
					<label for="username" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('User Name'); ?>:</label>
					<div><input id="username" name="username" type="text" size="36" value="<?php echo $this->escape($this->formData['username']) ?>" /></div>
					<?php if($this->formErrors['username']) echo '<br /><br /><div class="error">' . $this->escape($this->formErrors['username']) . '</div>'?>
				</div>
				<div class="clr"></div>
				<div class="frm">
					<label for="token" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Token'); ?>:</label>
					<input id="token" name="token" type="text" size="36" value="<?php echo $this->escape($this->formData['token']) ?>" />
					<button type="submit" class="sbut validate"><?php echo JText::_('Submit'); ?></button>
				</div>
				<?php if($this->formErrors['token']) echo '<div class="error">' . $this->escape($this->formErrors['token']) . '</div>'?>
			
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		<div class="clr"></div>
	</div>
</div>

