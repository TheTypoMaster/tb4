<?php defined('_JEXEC') or die('Restricted access'); ?>
		<?php if ( $this->params->get( 'show_login_title' ) ) : ?>
			<div class="hdrBar"><div id="hdrBar_account"></div><?php echo $this->params->get( 'header_login' ); ?></div>
		<?php endif; ?>

			<?php if ( $this->params->get( 'description_login' ) ) : ?>
			<div class="desc">
				Please use the login form above to gain access to the restricted areas of the site.
			</div>
			<?php endif; ?>

