<?php defined('_JEXEC') or die; ?>

<div class="resetForm">
	<div class="resetFormInr">
		<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
			<div class="hdrBar"><div id="hdrBar_account"></div>Have you not yet received activation email?<?php //echo $this->escape($this->params->get('page_title')); ?></div>
		<?php endif; ?>
		
		<form action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user&task=resend_verification' ); ?>" method="get">
			<div class="desc">Please enter the e-mail address associated with your User account. Your activation email will be e-mailed to the e-mail address on file.<?php //echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?></div>
			<div class="frm">
				<label for="email" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
				<input id="email" name="email" type="text" class="required validate-email" value="<?php echo isset($this->formData['email']) ? $this->escape($this->formData['email']) : '' ?>" />
				<button type="submit" class="sbut validate"><?php echo JText::_('Submit'); ?></button>
			</div>
			<?php if(isset($this->formErrors['email'])) echo '<div class="error">' . $this->escape($this->formErrors['email']) . '</div>' ?>
		
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<div class="clr"></div>
	</div>
</div>
