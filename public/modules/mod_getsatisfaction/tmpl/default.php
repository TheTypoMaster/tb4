<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php 
if (isset($fastpass_logout)): 
		echo $fastpass_logout;
	endif;
if (isset($fastpass_redirect)): 
		echo $fastpass_redirect;
	endif;
if (!empty($widget)):
	echo $widget;
endif;
if ($start_logout) {
	echo $pop_under_js;
}
?>
<style type="text/css">
a#fdbk_tab {
	background-image: url("/templates/topbetta/images/feedback.png");
	top: auto !important;
	bottom: 0px;
	width: 85px !important;
	height: 85px !important;
	position: fixed;
}

a.fdbk_tab_right {
	width: 85px !important;
	height: 85px !important;
	background-position: top left;
}

a.fdbk_tab_right:hover {
	width: 85px !important;
	height: 85px !important;
	background-position: top right;
}

</style>