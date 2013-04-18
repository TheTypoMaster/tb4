<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<div id="bettaWrap">
<div class="moduletable">
<h3>My Account</h3>
<div class="innerWrap">
<div class="hdrBar">
<div id="hdrBar_trans"></div>
<span class="tournament_title">Tournament History</span>
&nbsp;&nbsp;&nbsp;
<span class="tournament_title_link"><a href="/user/account/private-tournament-history">Tournaments I Created</a></span>
<div id="date_select"><span class="date_select_txt">DATE: </span><?php echo $this->current_date; ?></div>
</div>

<?php if($this->tournament_list) :?>

<table id="receipt" border="1" class="mytrans" width="100%">
	<tr>
		<th>ID</th>
		<th>SPORT</th>
		<th>NAME</th>
		<th>TIME</th>
		<th>TOTAL</th>
		<th>PLACE</th>
		<th>PRIZE</th>
	</tr>

	<?php foreach($this->tournament_list as $tournament) :?>
	<tr>
		<td><?php echo $this->escape($tournament->id) ?></td>
		<td><?php echo $this->escape($tournament->sport_name) ?> &mdash; <?php echo $this->escape($tournament->competition_name)?></td>
		<td><a
			href="/tournament/details/<?php echo $this->escape($tournament->id)?>"><?php echo $this->escape($tournament->tournament_name) ?></a></td>
		<td><?php echo $this->escape($tournament->created_date)?></td>
		<td><?php echo $this->escape($tournament->betta_bucks)?></td>
		<td><?php echo $tournament->leaderboard_rank ?></td>
		<td>
			<?php if(empty($tournament->ticket_awarded) && empty($tournament->prize)) :?>
			&mdash;
			<?php endif ?>
			<?php if($tournament->ticket_awarded) :?>
			<a href="/tournament/details/<?php echo $this->escape($tournament->ticket_awarded)?>"><?php echo $this->escape('1 Ticket (#' . $tournament->ticket_awarded .')')?></a>
			<?php if($tournament->prize) :?> + <?php endif ?>
			<?php endif ?>
			<?php echo $this->escape($tournament->prize)?>
		</td>
	</tr>
	<?php endforeach ?>
</table>

	<?php
		echo preg_replace('/\/index.php\?option=com_tournament&amp;task=tournamenthistory(&amp;limitstart=){0,1}/s', '/user/account/tournament-history/', $this->pagination);
	?> <br />
<br />
	<?php else : ?>
<p>There are no tournaments to list.</p>
	<?php endif ?></div>
</div>
</div>
