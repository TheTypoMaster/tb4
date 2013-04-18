<?php
/**
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 * @copyright	Copyright (C) 2005 - 2009 XXXXXXXXXXXXXXXXXXX. All rights reserved.
 
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 */
defined('_JEXEC') or die('Restricted access');

$cols = "fullpage";
if ($this->countModules('left + right') > 0) {
	if ($this->countModules('right') >= 1 && $this->countModules('left') <= 0) $cols="rightcol";
	elseif ($this->countModules('left') >= 1 && $this->countModules('right') <= 0) $cols="leftcol";
	else $cols="threecol";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
	<jdoc:include type="head" />
	<?php JHTML::_('behavior.modal'); ?>
	<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css"/>
</head>
<style>
	div#system-debug {
		display:none;
	}
</style>

<body>

<div id="fixed">

	<div id="header">
		<div id="dated"><?php echo date('jS F Y'); ?></div>
		<div id="topmod">
			<jdoc:include type="modules" name="login" style="none" />
		</div>
		<div class="logo">&nbsp;</div>
	</div>
	<div class="colwrap">
		<div class="colmask <?php echo $cols; ?>">
			<?php if($cols=='threecol') : ?>
			<div class="colmid">
			<?php endif; ?>
				<?php if($cols!='fullpage') : ?>
				<div class="colleft">
				<?php endif; ?>
					<div class="col1">
						<!-- Column 1 start -->
						<jdoc:include type="message" />
						<?php if($this->countModules('newsflash')) : ?>
							<jdoc:include type="modules" name="newsflash" style="xhtmlxtd" />
						<?php endif; ?>
						<jdoc:include type="component" />
						<!-- Column 1 end -->
					</div>
					<div class="col2">
						<!-- Column 2 start -->
						<?php if($this->countModules('left')) : ?>
							<jdoc:include type="modules" name="left" style="xhtml" />
						<?php endif; ?>
						<!-- Column 2 end -->
					</div>
					<div class="col3">
						<!-- Column 3 start -->
						<?php if($this->countModules('right')) : ?>
							<jdoc:include type="modules" name="right" style="xhtml" />
						<?php endif; ?>
						<!-- Column 3 end -->
					</div>
				<?php if($cols!='fullpage') : ?>
				</div>
				<?php endif; ?>
			<?php if($cols=='threecol') : ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="clr"></div>
	</div>
	<div class="contentFoot">&nbsp;</div>
	<div id="footer">
		<div class="footMini">&nbsp</div>
	</div>

</div>
<div class="clr"></div>

<br />
<br />
<br />
<br />
<jdoc:include type="modules" name="debug" />

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10435708-1");
pageTracker._trackPageview();
} catch(err) {}</script>

</body>
</html>