<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="resetForm">
	<div class="resetFormInr">
	  <?php if ($this->params->get( 'show_page_title', 1)) : ?>
		<div class="hdrBar"><div id="hdrBar_account"></div><?php echo $this->escape($this->params->get('page_title')); ?></div>
	  <?php endif; ?>
		<?php echo $this->loadTemplate($this->type); ?>		
		<div class="clr"></div>
	</div>
</div>
