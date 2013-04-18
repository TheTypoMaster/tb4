<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Racing Tournaments Manager'), 'generic.png');
JToolBarHelper::preferences('com_ucbetman');
?>

<table class="ucbetmanjoomcss">
	<tbody>
		<tr>
         	<td width="58%" valign="top">
				<div id="cpanel">
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_ucbetman&amp;option=com_ucbetman&controller=race_wizard">
							<img src="/administrator/components/com_ucbetman/images/tbrt.png" alt="Racing Tournaments">
							<span>Create/Edit Racing Tournaments</span>
							</a>
						</div>
					</div>
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_ucbetman&amp;option=com_ucbetman&controller=user_manager">
							<img src="/administrator/components/com_ucbetman/images/tbum.png" alt="User Manager">
							<span>BettaSports User Manager</span>
							</a>
						</div>
					</div>
					
					<div style="float: left;">
						<div class="icon">
							<a href="index.php?option=com_ucbetman&amp;option=com_ucbetman&controller=tourn_info">
							<img src="/administrator/components/com_ucbetman/images/tbti.png" alt="Tournament Information">
							<span>Racing Tournament Manager / Information</span>
							</a>
						</div>
					</div>

				</div>
			</td>
		</tr>
	</tbody>
</table>