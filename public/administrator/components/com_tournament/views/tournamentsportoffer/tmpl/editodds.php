<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportoffer";
$commonFrmControls = "
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentsportoffer' />
";
?>
<!-- Event List -->
<form name="adminForm" action="<?=$formAction?>" method="get" id="filterFrm">

	<div id="editcell">
		<table class="adminlist" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th><?php echo JText::_('Bet Type'); ?></th>
					<th><?php echo JText::_('Bet Selection'); ?></th>
					<th><?php echo JText::_('Odds'); ?></th>
					<th><?php echo JText::_('Odds Override'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(!empty($this->offer_data)){
				$i = 1;
				foreach($this->offer_data as $offer) { ?>
					<tr class="<?php echo $i % 2 == 0 ? 'row1' : 'row0'?>">

						<td>
						<?php echo JText::_($offer->market_type); ?>
						</td>
						<td>
							<?php echo JText::_($offer->name); ?>
						</td>
						<td><?php echo JText::sprintf('%01.2f', $offer->win_odds); ?></td>
						<td><input name="offer_price[<?php echo $offer->selection_price_id; ?>]" type="text" value="<?php echo is_null($offer->override_odds) ? '' : JText::sprintf('%01.2f', $offer->override_odds); ?>" /></td>
					</tr>
			<?php
					$i++;
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<?=$commonFrmControls?>
	<input type="hidden" name="task" value="saveOdds" />
	<input type="hidden" name="match_id" value="<?php echo $this->match_id; ?>" />
	<input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
</form>
