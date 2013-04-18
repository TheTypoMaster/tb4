<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if($this->ajax_call) {
?>
	<script language="javascript">
	<?php echo $this->js_var ?>
	</script>
<?php } ?>


<div class="panel-header"><a class="refresh-odds-button marketItems" href="#" ref="" onclick="return false" id="refresh_odds"><img
	src="templates/topbetta/images/refresh-small.png" border="0" alt="" />
<span>REFRESH ODDS</span></a><span class="panel-header-text"><?php echo $this->escape($this->match->name) ?>:
<?php echo $this->escape($this->market->name) ?><?php echo $this->escape($this->bet_limit_text) ?></span></div>

<table class="make-bets-table">
	<tr>
		<th class="text-center">#</th>
		<th>SELECTIONS</th>
		<th class="text-center">ODDS</th>
		<th class="text-center">BET AMOUNT</th>
		<th class="text-center">TO WIN</th>
	</tr>
	<?php
		$i = 1;
		foreach($this->offer_list as $offer) {?>
	<tr>
		<td class="text-center"><?php echo $i ?></td>
		<td><?php echo $this->escape($offer->name)?></td>
		<td class="text-right">
			<span id="offers_<?php echo $offer->id ?>_odds"><?php echo $this->escape($offer->odds)?></span>
		</td>
		<td>
			<?php if($this->match->betting_open) { ?>
			<input class="sports-bet-input" ref="market_item_bet_<?php echo $this->market->id ?>" type="text" name="bets[<?php echo $offer->id?>]" id="offers_<?php echo $offer->id?>"  value="<?php echo isset($this->pending_bet_list[$offer->id]) ? $this->escape($this->pending_bet_list[$offer->id]) : '' ?>" />
			<?php } else {?>
			<input class="sports-bet-input disabled" ref="market_item_bet_<?php echo $this->market->id ?>" type="text" disabled="disabled" value="" />
			<?php }?>
		</td>
		<td class="to-win" id="offers_<?php echo $offer->id ?>_win"><?php echo $this->escape($offer->win)?></td>
	</tr>
	<?php
		$i++;
		}?>
</table>
<input id="from_market_id" name="from_market_id" type="hidden" value="<?php echo $this->escape($this->market->id) ?>" />