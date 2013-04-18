<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<html>
	<head>
		<script src="/media/system/js/mootools.js" type="text/javascript"></script>
		<script type="text/javascript" src="/templates/topbetta/js/general.js"></script>
		<script src="components/com_tournament/assets/common.js" type="text/javascript"></script>
		<link type="text/css" href="/templates/topbetta/css/template.css" rel="stylesheet">
	</head>
	<body style="min-width: 0pt;">
	<div class="private-tournament-box-content">
		<div class="create-tourn-wrap">
		<p>Redirecting the page, please wait...</p>
		<p><a href="<?php echo $this->redirect_url?>" target="_top">Click here</a> if the page is not redirected</p>
		</div>
	</div>
	<?php echo $this->redirect_js ?>
	</body>
</html>







