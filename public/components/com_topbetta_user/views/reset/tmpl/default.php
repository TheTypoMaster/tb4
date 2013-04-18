<?php defined('_JEXEC') or die; ?>

<div class="resetForm">
	<div class="resetFormInr">
		<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
			<div class="hdrBar"><div id="hdrBar_account"></div><?php echo $this->escape($this->params->get('page_title')); ?></div>
		<?php endif; ?>
		<form action="<?php echo JRoute::_( 'index.php?option=com_topbetta_user&task=requestreset' ); ?>" method="post" class="josForm">
			<div class="desc"><?php echo JText::_('RESET_PASSWORD_REQUEST_DESCRIPTION'); ?></div>
			<div class="frm">
				<label for="email" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
				<input id="email" name="email" type="text" class="required validate-email" value="<?php echo isset($this->formData['username']) ? $this->escape($this->formData['username']) : '' ?>" />
				<button type="submit" class="sbut validate"><?php echo JText::_('Submit'); ?></button>
			</div>
			<?php if(isset($this->formErrors['email'])) echo '<div class="error">' . $this->escape($this->formErrors['email']) . '</div>'; ?>
		
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<div class="clr"></div>
	</div>
</div>
