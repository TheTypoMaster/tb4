<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportoffer";
$commonFrmControls = "
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentsportoffer' />
";
?>

<!-- Event List -->
<form name="adminForm" action="<?=$formAction?>" method="get" id="resultForm">

	<div id="editcell" style="width: 600px">
		<table class="adminlist" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th><?php echo JText::_('Bet Type'); ?></th>
					<th><?php echo JText::_('Bet Selection'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(!empty($this->market_list)):
				$disabled = $this->match_is_resulted ? ' disabled="disabled"' : '';
				$i = 1;
				foreach($this->market_list as $market_id => $market) :
				?>
					<tr class="<?php echo $i % 2 == 0 ? 'row1' : 'row0'?>">

						<td>
						<?php print JText::_($market->name); ?>
						</td>
						<td>
							<select name="offer_selection[<?php echo $market_id; ?>]" id="offer_selection[<?php echo $market_id; ?>]" class="ctrl" <?php echo $disabled;?>>
								<option value="-1">Select&hellip;</option>
								<?php foreach($market->offer_list as $id => $name):
										$selected = '';
										if(!is_null($market->paying_offer) && $market->paying_offer == $id):
											$selected = ' selected="selected"';
										endif;?>
									<option value="<?php echo $id; ?>"<?php echo $selected;?>><?php echo $name; ?></option>
								<?php endforeach;?>
								<?php $cancel_selected = $market->paying_offer === '0' ? ' selected="selected"' : '';?>
								<option value="0"<?php echo $cancel_selected;?>>Cancel and Refund</option>
							</select>
						</td>
					</tr>
			<?php
				$i++; 
				endforeach;
			endif;
			?>
			</tbody>
		</table>
	</div>
	<?=$commonFrmControls?>
	<input type="hidden" name="match_id" value="<?php echo $this->match_id; ?>" />
	<input type="hidden" name="task" value="saveResult" />
	<input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
</form>
<script language="javascript" type="text/javascript"><!--
function submitbutton(pressbutton){
	if(pressbutton == 'saveResult'){
		var pass_validation = true;
		var offers = $('resultForm').getElements('select');
		offers.each(function(item, index){
			if(item.selectedIndex < 0){
				pass_validation = false;
			}
		});
		if(!pass_validation){
			alert('Please select a result for each bet type.');
		} else {
			if(confirm('Are you sure?')){
				submitform(pressbutton);
			}
		}
	}else if(pressbutton == 'abandonMatch'){
		if(confirm('Are you sure you want to abandon this match?')){
			submitform(pressbutton);
		}
	}else{
		submitform(pressbutton);
	}
}
</script>