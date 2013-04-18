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
<span class="tournament_title">My Private Tournaments</span>
&nbsp;&nbsp;&nbsp;
<span class="tournament_title_link"><a href="/tournamenthistory">Tournaments I Entered</a></span>
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
		<th>ENTRANTS</th>
		<th>CODE</th>
	</tr>

	<?php foreach($this->tournament_list as $tournament) :?>
	<tr>
		<td><?php echo $this->escape($tournament->id) ?></td>
		<td><?php echo $this->escape($tournament->sport_name) ?> &mdash; <?php echo $this->escape($tournament->competition_name)?></td>
		<td><a
			href="/tournament/details/<?php echo $this->escape($tournament->id)?>"><?php echo $this->escape($tournament->tournament_name) ?></a></td>
		<td><?php echo $this->escape($tournament->created_date)?></td>
		<td><?php echo $this->escape($tournament->prize_pool_display)?></td>
		<td><?php echo $this->escape($tournament->entrants)?></td>
		<td>
			<?php echo $this->escape($tournament->display_identifier)?>
		</td>
	</tr>
	<?php endforeach ?>
</table>

	<?php
		echo preg_replace('/\/index.php\?option=com_tournament&amp;task=privatetournamenthistory(&amp;limitstart=){0,1}/s', '/user/account/private-tournament-history/', $this->pagination);
	?> <br />
<br />
	<?php else : ?>
<p>There are no tournaments to list.</p>
	<?php endif ?></div>
</div>
</div>
