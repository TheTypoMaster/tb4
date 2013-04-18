<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php /** @todo Should this be routed */ ?>
		<?php if ( $this->params->get( 'show_logout_title' ) ) : ?>
			<div class="hdrBar"><div id="hdrBar_account"></div><?php echo $this->escape($this->params->get( 'header_logout' )); ?></div>
		<?php endif; ?>

			<?php if ( $this->params->get( 'description_logout' ) ) : ?>
			<div class="desc">
				You are currently logged in to the private area of this site.<br />Please use the form above if you would like to logout.
			</div>
			<?php endif; ?>
