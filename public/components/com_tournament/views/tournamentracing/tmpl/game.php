<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<div id="bettaWrap" > 
	<h3 class="tournaments"><?php echo $this->tournament->title ?>
		<span class="info-button <?php echo $this->race->paid_flag ?'pos2' : 'pos3'; ?>">
		<a href="/tournament/racing">ALL RACES</a>
		</span>
		
		<?php if (!$this->race->paid_flag) : ?>
		<span class="info-button pos2 tourn-info-listen-now">
		<a href="http://www.sport927.com.au/sport927.asx">LISTEN NOW&nbsp;<img src="/components/com_tournament/images/icon-listen.png" width="11" height="11"></a>
		</span>
		<?php endif; ?>
		
		<span class="info-button pos1">
			<a href="/tournament/details/<?php echo $this->tournament->id ?>">TOURNAMENT DETAILS</a>
		</span>        
	</h3>
 	<div class="clr"></div> 
	<div id="raceHeadr"> 
		<div class="raceDetails<?php echo $this->race->betting_open ? '' : ' wideHdr'; ?>"> 
			<div class="raceDetailsTitleNums"> 
				<div class="raceDetailsTitle <?php echo $this->meeting->icon ?>"><?php echo $this->race->title ?> <?php echo (!empty($this->race->distance)) ? '('. $this->race->distance.')' : ''; ?></div> 
				<div class="raceDetailsNums"> 
				<?php foreach($this->meeting->event_list as $number => $detail) : ?>
				<?php if ($number != 0) : ?>				
					<div class="eventNumber<?php echo $detail['class_suffix'] . $detail['class']; ?>">
						<a href="/tournament/racing/game/<?php echo $this->tournament->id; ?>/<?php echo $number; ?>">
						<?php echo $number; ?>
						</a>
					</div>
				<?php endif; ?>						
				<?php endforeach; ?>
				</div> 
			</div> 
			<div id="raceDetailsPanel"> 
				<div id="jumpStr" class="raceDetailsJump"><span id="jumpLabel"><?php echo $this->race->start_label; ?></span><span id="cntdwnVal"><?php echo $this->race->counter; ?></span></div> 
				<div class="raceDetailsTime"><?php echo $this->race->time; ?> <?php echo $this->time_zone; ?></div> 
				<div class="raceDetailsWeather">Weather: <?php echo $this->race->weather; ?>&nbsp;&nbsp;|&nbsp;&nbsp;Track: <?php echo $this->race->track_condition; ?></div> 
			</div> 
		</div>
	</div> 

	<?php if ($this->race->betting_open) : ?>
	<div id="betBoxB"> 
		<div class="betBoxInrB">ENTER YOUR BET AMOUNT
			<div class="betValueB"> 
				<input type="text" id="betValueB" name="betValueB" value="" /> 
				<input type="hidden" id="betOrigin" name="betOrigin" value="tournament" />
           </div> 
			<div class="betButtB">
				<a <?php echo $this->bet_link_id ? 'id="' . $this->bet_link_id . '"' : '' ?> href="<?php echo $this->escape($this->bet_link) ?>" class="<?php echo $this->bet_link_class ?>">SUBMIT BETS</a> 
			</div> 
		</div> 
	</div>
	<?php endif; ?>
	<div class="clr"></div>
	
	<?php if ($this->display_result_list) : ?>
	<div class="resultsPanel">
	
	<div class="panel-header">
		<a href="#" id="results" onclick="return false;">
			<h3><span class="panel-header-text">RESULT STATUS : <?php echo $this->escape($this->race->status) ?></span><span id="results_arrow" class="arrow-up">&nbsp;</span></h3>
		</a>
	</div>
	<div id="results_content">
		<table class="results-table-wrap">
			<tr>
				<td width="<?php echo ($this->display_result_list['has_exotics'] ? '60%' : '100%') ?>">
					<table class="results-table">
					<tbody>
						<tr>
							<th class="bdrRight">Result</th>
							<th class="bdrRight bld">No.</th>
							<th class="bdrRight bld">Runner</th>
							<th class="bdrRight">Win <?php echo $this->dividend_label ?></th>
							<th class="w60">Place <?php echo $this->dividend_label ?></th>
						</tr>
						<?php foreach($this->display_result_list['rank'] as $result) : ?>
						<tr>
							<td class="rltCntr bdrRight"><?php echo $result['position']; ?></td>
							<td class="rltCntr bdrRight bld"><?php echo $result['number']; ?></td>
							<td class="rltLeft bdrRight bld"><?php echo $result['name']; ?></td>
							<td class="bdrRight rltCntr"><?php echo $result['win_' . $this->display_result_list['dividend_field']]; ?></td>
							<td class="rltCntr"><?php echo $result['place_' . $this->display_result_list['dividend_field']]; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					</table>
				</td>
				<?php if ($this->display_result_list['has_exotics']) : ?>
				<td width="40%">
					<table class="results-table">
					<tbody>
						<tr>
							<th class="bdrRight">Exotic Combos</th>
							<th class="w60">Exotic Dividend</th>
						</tr>
						<?php foreach($this->display_result_list['exotic'] as $type => $result) : ?>
						<?php if (!empty($result) && is_array($result)) :?>
						<?php foreach ($result as $combos => $dividend) : ?>
						<tr>
							<td class="rltLeft bdrRight"><?php echo ucwords($type) . ': ' .  $combos; ?></td>
							<td class="rltCntr"><?php echo $dividend; ?></td>
						</tr>
						<?php endforeach; ?>
						<?php endif; ?>
						<?php endforeach; ?>
					</tbody>
					</table>
				</td>
				<?php endif; ?>
			</tr>
		</table>
	</div>
	</div>
	<?php endif; ?>
	
	<div class="clr"></div>
	<?php if (!empty($this->display_bet_list)) : ?>
	<div class="selectionsPanel">
	<div class="panel-header">
		<a class="print-ticket-button" href="#" id="print_racing_bets">PRINT MY BETS</a>
		<a href="#" id="myselections" onclick="return false;">
			<h3><span class="panel-header-text">MY RACE BETS</span><span id="myselections_arrow" class="arrow-up">&nbsp;</span></h3>
		</a>
	</div>
	<div id="myselections_content">
		<div class="selectionsPanel1">
			<table width="100%" id='racing_bets'>
			<tbody>
				<tr>
					<th class="w60">TICKET No.</th>
					<th class="rltLeft">SELECTIONS</th>
					<th class="w60">BET TYPE</th>
					<th class="w70">BET AMOUNT</th>
					<th class="w70">BET TOTAL</th>
					<th class="w60">FLEXI %</th>
					<th class="w40">ODDS</th>
					<th class="w70"><?php echo strtoupper($this->race->paid_status); ?></th>
					<th class="w60">RESULT</th>
				</tr>
				<?php $class = 1; ?>
				<?php foreach($this->display_bet_list as $bet) : ?>
				<tr class="mbrow<?php echo $class . $bet['row_class']; ?>">
					<td class="na"><?php echo $bet['id']; ?></td>
					<td class="rltLeft selwdth runnerName"><?php echo $bet['selection']; ?></td>
					<td class="upperCase betType"><?php echo $bet['bet_type']; ?></td>
					<td class="rltCntr betAmount"><?php echo $bet['bet_amount']; ?></td>
					<td class="rltCntr betAmount"><?php echo $bet['total_amount']; ?></td>
					<td class="rltCntr betAmount"><?php echo $bet['flexi']; ?></td>
					<td class="rltCntr"><?php echo JHTML::tooltip($bet['odds_tooltip'], '', '', $bet['odds']); ?></td>
					<td class="rltCntr <?php echo $bet['class']; ?>"><?php echo $bet['paid_amount']; ?></td>
					<td class="<?php echo $bet['class']; ?>"><?php echo $bet['result']; ?></td>
				</tr>
	          <?php $class = ($class == 2) ? 1 : 2; ?>
	          <?php endforeach; ?>
				<tr>
					<td class="totals">&nbsp;</td>
					<td class="totals">&nbsp;</td>
					<td class="totals">&nbsp;</td>
					<td class="totals subbie">TOTAL:</td>
					<td class="totals rltCntr"><?php echo $this->race->total_bet_amount; ?></td>
					<td class="totals">&nbsp;</td>
					<td class="totals">&nbsp;</td>
					<td class="totals rltCntr"><?php echo $this->race->total_paid_amount; ?></td>
					<td class="totals <?php echo $this->race->total_win_class ?>"><?php echo $this->race->total_win; ?></td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
	</div>
	<?php endif; ?>
	<div class="clr"></div>
	<?php $class = ($this->race->tournament_betting_open) ? '' : ' wideHdr'; ?>
	<div id="bucksbar">
		<div class="tournDetails<?php print $class; ?>">
			<table id="mybucks">
				<tr>
					<td>
						<div id="mybuckstext">MY AVAILABLE BUCKS:</div>
					</td>
					<td>
						<div id="mybucksnumber"><?php print $this->tournament->available_currency; ?></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="myturnover">
							(<span title="This is the remaining amount needed to be bet to qualify for this tournament'" class="editlinktip hasTip"><?php echo $this->tournament->turnover_currency ?></span>)
						</div>
					</td>
				</tr>
			</table>
			<div class="tourn-detials-note">
				<p><strong>Note:</strong> Exotic bets are not currently supported for tournaments. Coming soon!</p>
			</div>
		</div>
		<?php if($this->race->tournament_betting_open) : ?>
		<div id="betBoxG">
			<div class="betBoxInrG">ENTER YOUR TOURNAMENT BET
				<div class="betValueG">
					<input type="text" id="TournBetValueG" name="betValueG" value="" />
				</div>
				<div class="betButtG">
					<a <?php echo $this->bet_tournament_link_id ? 'id="' . $this->bet_tournament_link_id . '"' : '' ?> href="<?php echo $this->escape($this->bet_tournament_link) ?>" class="<?php echo $this->bet_link_class ?>">SUBMIT BETS</a>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="clr"></div>
	<?php if (!empty($this->tournament_bet_list)) : ?>
	<div class="selectionsPanel">
		<div class="panel-header">
			<a class="print-ticket-button" href="#" id="print_racing_tournament_bets">PRINT MY BETS</a>
			<a href="#" id="mytournamentselections" onclick="return false;">
				<h3 class="tournaments"><span class="panel-header-text">My Tournament Bets</span><span id="mytournamentselections_arrow" class="arrow-up">&nbsp;</span></h3>
			</a>
		</div>
		<div id="mytournamentselections_content">
			<div class="selectionsPanel1">
				<table width="100%" id="racing_tournament_bets">
					<tbody>
						<tr>
							<th class="w60">TICKET No.</th>
							<th class="rltLeft">SELECTIONS</th>
							<th class="w60">BET TYPE</th>
							<th class="w60">AMOUNT</th>
							<th class="w40"><?php print strtoupper(empty($this->dividend_label) ? 'odds' : $this->dividend_label); ?></th>
							<th class="w70"><?php print strtoupper($this->race->paid_status); ?></th>
							<th class="w60">RESULT</th>
						</tr>
						<?php $class = 1; ?>
						<?php foreach($this->tournament_bet_list as $bet) : ?>
						<tr class="mbrow<?php print $class . $bet->row_class; ?>">
							<td class="na"><?php print $bet->id; ?></td>
							<td class="rltLeft selwdth runnerName"><?php print $bet->runner_number; ?>. <?php print $bet->selection_name; ?></td>
							<td class="upperCase betType"><?php print $bet->bet_type; ?></td>
							<td class="rltCntr betAmount"><?php print $bet->display_bet; ?></td>
							<td class="rltCntr"><?php print empty($bet->odds_tooltip) ? $bet->display_dividend : JHTML::tooltip($bet->odds_tooltip, '', '', $bet->display_dividend); ?></td>
							<td class="rltCntr <?php print $bet->class; ?>"><?php print $bet->paid; ?></td>
							<td class="<?php print $bet->class; ?>"><?php print $bet->result; ?></td>
						</tr>
						<?php $class = ($class == 2) ? 1 : 2; ?>
						<?php endforeach; ?>
						<tr>
							<td class="totals">&nbsp;</td>
							<td class="totals">&nbsp;</td>
							<td class="totals subbie">TOTAL:</td>
							<td class="totals rltCntr"><?php print $this->race->total_tournament_bet; ?></td>
							<td class="totals">&nbsp;</td>
							<td class="totals rltCntr"><?php print $this->race->total_tournament_win; ?></td>
							<td class="totals <?php echo $this->race->tournament_net_win_class; ?>"><?php echo $this->race->tournament_net_win ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="clr"></div>
	<?php endif; ?>

	<?php if ($this->race->betting_open) :?>
	<div id="btButts" class="betTypeButts"> 
		<table class="buttTbl" width="100%">
			<tbody>
			<tr>
	        	<td class="win"><div class="winButtImg"><div id="winButtID" class="typeButts" style="background-position: 0px -66px;">WIN</div></div></td>
	        	<td class="place"><div class="placeButtImg"><div id="placeButtID" class="typeButts">PLACE</div></div></td>
				<td class="eachway"><div class="eachwayButtImg"><div id="eachwayButtID" class="typeButts">EACH WAY</div></div></td>
				<td class="quinella"><div class="typeButtsImg"><div id="quinellaButtID" class="typeButts">QUINELLA</div></div></td>
				<td class="exacta"><div class="typeButtsImg"><div id="exactaButtID" class="typeButts">EXACTA</div></div></td>
				<td class="trifecta"><div class="typeButtsImg"><div id="trifectaButtID" class="typeButts">TRIFECTA</div></div></td>
				<td class="firstfour"><div class="typeButtsImg"><div id="firstfourButtID" class="typeButts">FIRST FOUR</div></div></td>
				<td class="refresh-odds"><div class="typeButtsImg"><div id="refreshButtID" class="typeRefresh">REFRESH <span id="bet-refresh-countdown"></span></div></div></td>
      		</tr>
			</tbody>
		</table>
	</div> 
	<div class="clr"></div>
	<?php endif; ?>
	
	<?php include JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'views' . DS . 'betting' . DS . 'tmpl' . DS . 'runnerlist.php'; ?>
  <div class="clr"></div> 
</div> 
