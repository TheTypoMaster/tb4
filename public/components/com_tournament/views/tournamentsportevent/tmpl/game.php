<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

?>

<h3 class="tournaments"> 
	<?php echo $this->escape($this->tournament->title) ?>
    <span class="info-button pos1"> 
        <a href="<?php echo $this->escape($this->tournament->info_link) ?>">TOURNAMENT DETAILS</a> 
    </span> 
    <div class="clear"></div> 
</h3> 

<div class="sports-round-select">
<div class="sports-round-title"><span class="betting-closes"><span id="jumpLabel"><?php echo $this->escape($this->match->start_label)?></span> <span id="cntdwnVal"><?php echo $this->escape($this->match->counter)?></span></span>
<img src="/components/com_tournament/images/<?php echo $this->tournament->icon ?>.png" border="0" alt="" /><?php echo $this->escape($this->tournament->event_group_name)?>
- <?php echo $this->escape($this->match->name)?></div>

<?php if(!empty($this->match_list) && count($this->match_list) > 1) { ?>
<table class="sports-round-match-table" cellspacing="5">
<?php
	$i = 0;
	foreach($this->match_list as $match) {
		if(0 == $i%5) {
			echo '<tr>';
			$enclosed = false;
		}
		?>
		<td <?php echo $match->class ?>><a href="<?php echo $this->escape($match->link)?>"> <span class="round-match"><?php echo $this->escape($match->name)?></span>
		<?php echo $match->bet_total ?> </a></td>
		<?php
		if(4 == $i%5) {
			echo '</tr>';
			$enclosed = true;
		}
		$i++;
	}

	if(!$enclosed) {
		while(0 != $i%5) {
			echo '<td class="empty"><a>&nbsp;</a></td>';
			$i++;
		}
		echo '</tr>';
	}
?>
</table>
<?php } ?>
</div>
<!-- close sports-round-select -->

<?php if(!empty($this->result_list)) {?>
<div class="sports-resultsPanel">
<div class="panel-header"><a href="#" id="results" onclick="return false;"><span class="panel-header-text">Results
Status: <?php echo $this->match->status->name ?></span> <span id="results_arrow" class="arrow-down">&nbsp;</span></a></div>

<div id="results_content">
<table class="results-table-wrap">
	<tr>
<?php for($i=0; $i<3; $i++) {?>
		<td>
		<table class="results-table">
			<tr>
				<th>BET TYPE</th>
				<th>SELECTION</th>
			</tr>
			<?php foreach($this->result_display['col' . $i] as $result) {?>
			<tr>
				<td><?php echo $this->escape($result['market_name'])?></td>
				<td><?php echo $this->escape($result['offer_name'])?></td>
			</tr>
			<?php }?>
		</table>
		</td>
<?php } ?>
	</tr>
</table>
</div>

</div>
<!-- close results-panel -->
<?php }?>

<div class="clr"></div>
<?php if(!empty($this->bet_list) && is_array($this->bet_list)) {?>
<div class="selectionsPanel" id="bet_selections">
<div class="panel-header">
	<a class="print-ticket-button" href="#" id="print_sports_bets">PRINT MY BETS</a>
	
	<a class="bet-selection-accordion" id="myselections" href="#" onclick="return false;">
		<h3 class="tournaments"><span class="panel-header-text">MY BET SELECTIONS</span> <span class="arrow-down" id="myselections_arrow">&nbsp;</span></h3>
	</a>
</div>
<div id="myselections_content">
<table class="bets-table" id="sports_bets">
	<tr>
		<th>TICKET No.</th>
		<th>BET TYPE</th>
		<th>SELECTION(S)</th>
		<th>BET AMOUNT</th>
		<th>ODDS</th>
		<th><?php echo $this->match->paid_status ?></th>
		<th>RESULT</th>
	</tr>
	<?php foreach($this->bet_list as $bet) {?>
	<tr>
		<td><?php echo $this->escape($bet->tournament_ticket_id) ?></td>
		<td><?php echo $this->escape($bet->market_name) ?></td>
		<td><?php echo $this->escape($bet->offer_name) ?></td>
		<td><?php echo $this->escape($bet->display_bet) ?></td>
		<td><?php echo $bet->display_odds ?></td>
		<td class="<?php print $bet->class; ?>"><?php echo $bet->paid ?></td>
		<td class="<?php print $bet->class; ?>"><?php echo $bet->result ?></td>
	</tr>
	<?php }?>
	<tr class="bet-totals">
              <td></td>
              <td></td>
              <td>TOTAL:</td>
              <td><?php echo $this->match->total_bet ?></td>
              <td>&mdash;</td>
              <td><?php echo $this->match->total_win ?></td>
              <td class="<?php echo $this->match->net_win_class?>"><?php echo $this->match->net_win ?></td>
	</tr>
</table>
</div>

</div>
<!-- close sports-bets-panel -->
<?php }?>

<?php if(!empty($this->market_list)) {?>
<div class="bet-types">
<table class="bet-types-table" cellspacing="3">
<?php $i = 0; ?>
<?php foreach($this->market_list as $market) {
	if(0 == $i%6) {
		echo '<tr>';
		$enclosed = false;
	}?>
	<td <?php echo $market->class ?> id="market_item_<?php echo $this->escape($market->id) ?>">
		<a href="<?php echo $this->escape($market->link)?>" class="marketItems" id="market_<?php echo $this->escape($market->id) ?>" ref="<?php echo $this->escape($market->id) ?>" onclick="return false;">
			<span class="round-match"><?php echo $this->escape($market->name) ?></span>
			<span id="market_item_bet_<?php echo $this->escape($market->id) ?>">-</span>
		</a>
	</td>
	<?php
	if(5 == $i%6) {
		echo '</tr>';
		$enclosed = true;
	}
	$i++;
}

if(!$enclosed) {
	while(0 != $i%6) {
		echo '<td class="empty"><a>&nbsp;</a></td>';
		$i++;
	}
	echo '</tr>';
}?>
</table>
</div>
<!-- close bet-types -->
<?php } ?>


<div class="tourn-bet-wrap-full">

<div class="sports_tournDetails">
<table id="mybucks">
	<tr>
		<td>
		<div id="mybuckstext">MY AVAILABLE BUCKS:</div>
		</td>
		<td>
		<div id="mybucksnumber"><?php echo $this->escape($this->tournament->available_currency)?></div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="myturnover">(<span class="editlinktip hasTip" title="This is the remaining amount needed to be bet to qualify for this tournament"><?php echo $this->escape($this->tournament->turnover_currency) ?></span>)</div>
		</td>
	</tr>
</table>
	<div class="sports_tournInfoButt">
	<?php if($this->match->betting_open) { ?>
			<a <?php echo $this->bet_link_id ? 'id="' . $this->bet_link_id . '"' : '' ?> href="<?php echo $this->escape($this->bet_link) ?>" class="<?php echo $this->bet_link_class ?>">SUBMIT BETS</a>
	<? }?>
	</div>
</div>

<form action="index.php" name="offerForm" id="offerForm" method="POST">
	<div class="sports-bets-options" id="offer_list">
	<div class="panel-header"><a class="refresh-odds-button marketItems" href="#" ref="<?php echo $this->market->id ?>" onclick="return false" id="refresh_odds"><img
		src="templates/topbetta/images/refresh-small.png" border="0" alt="" />
	<span>REFRESH ODDS</span></a><span class="panel-header-text"><?php echo $this->escape($this->match->name) ?>:
	<?php echo $this->escape($this->market->name) ?><?php echo $this->bet_limit_text ?></span></div>

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
				<input class="sports-bet-input" ref="market_item_bet_<?php echo $this->market->id ?>" type="text" name="bets[<?php echo $offer->id?>]" id="offers_<?php echo $offer->id?>"  value="<?php echo $this->escape($this->pending_bet_list[$offer->id]) ?>" />
				<?php } else {?>
				<input class="sports-bet-input disabled" ref="market_item_bet_<?php echo $this->market->id ?>" type="text" disabled="disabled" value="" />
				<?php }?>
			</td>
			<td class="to-win" id="offers_<?php echo $offer->id ?>_win">-</td>
		</tr>
		<?php
			$i++;
			}?>
	</table>

	<input name="from_market_id" id="from_market_id" type="hidden" value="<?php echo $this->escape($this->market->id) ?>" />
	</div>
	<input name="option" type="hidden" value="com_tournament" />
	<input name="controller" type="hidden" value="tournamentsportevent" />
	<input name="task" type="hidden" value="listoffers" />
	<input name="format" type="hidden" value="raw" />
	<input name="id" type="hidden" value="<?php echo $this->escape($this->tournament->id) ?>" />
	<input name="market_id" id="market_id" type="hidden" value="" id="market_id" />
	<input name="match_id" id="match_id" type="hidden" value="<?php echo $this->escape($this->match->id) ?>" id="match_id" />
</form>
<!-- close sports-bets-panel -->

<div class="sports-bet-message">All odds are current to the best of our
ability.</div>

</div>
<!-- close tourn-bet-wrap-full -->

<div class="clear">&nbsp;</div>
