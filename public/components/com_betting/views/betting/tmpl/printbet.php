<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>TopBetta - Bet Summary Printout</title>
		<link rel="stylesheet" href="/templates/topbetta/css/screen.css" type="text/css" />
		<link rel="stylesheet" href="/templates/topbetta/css/screen.css" type="text/css" media="screen print" />
	</head>
	<body>
		<div class="mainbdr">
			<div id="bettixPopWrap">
				<div class="bettixPanel">
					<div class="bettixHead">
						<div class="bettixHeadPrintLink"><a href="#" onclick="window.print();return false;">print bets</a></div>
						<div class="bettixHeadLogo"><img src="/templates/topbetta/images/print-ticket-logo.gif" width="181" height="57" border="0" alt=""/></div>
						<div class="bettixHeadTitle">TopBetta Bet Summary</div>
						<div class="bettixHeadRace"><?php echo $this->escape($this->title) ?></div>
					</div>
				</div>
				<div class="bettixPanel">
					<div class="bettixTpanel">
						<table width="100%">
							<?php echo $this->content; ?>
						</table>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</body>
</html>

