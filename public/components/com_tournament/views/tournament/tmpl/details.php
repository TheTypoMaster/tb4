<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$title_prefix = '';
if ($this->tournament->private_flag > 0) :
	$title_prefix 	 = "Private Tournament - ";
	$tournament_type = "Cash - Private";
	if (!empty($this->private_tournament->password)) :
		$tournament_type .= " - Password-protected";
	endif;
else :
	$tournament_type = $this->tournament->gameplay;
endif;

if ($this->tournament_owner > 0 && !$this->tournament->ended) : ?>

<div class="head-green-wrap">
<span class="promote-tourn-links">
<a href="tournament/emailfriend/<?=$this->tournament->id?>" id="btnEmailFriend"><img src="templates/topbetta/images/btn_promote-email.png"></a>
<a onclick="window.open('https://ssl.facebook.com/share.php?u=<?=urldecode($this->private_tournament_url)?>&amp;t=<?=urlencode($this->tournament->name)?>&amp;src=sp','PromoteTournament','menubar=no,width=430,height=380,toolbar=no');"><img src="templates/topbetta/images/btn_promote-facebook.png" /></a>
<a onclick="window.open('http://twitter.com/share?url=<?=urldecode($this->shorten_url)?>&amp;counturl=<?=urldecode($this->shorten_url)?>&amp;text=<?=urlencode("I've just created a tournament on Topbetta! Go here to sign up. Must be 18 to enter.")?>','PromoteTournament','menubar=no,width=550,height=280,toolbar=no');"><img src="templates/topbetta/images/btn_promote-twitter.png" /></a>
</span>
<span class="head-green-title">PROMOTE YOUR TOURNAMENT:<span class="head-green-small">Don't forget to mention your REFERRAL ID: <?=$this->tournament_owner?> &nbsp; <a href="/user/refer-a-friend">(more info)</a></span></span>

<div class="clear"></div>
</div>

<? endif; ?>
<div id="bettaWrap">
	<div class="tournContent">
		<div id="tourninfo">
			<h4><?php echo $title_prefix . $this->escape($this->tournament->name); ?></h4>
				<div id="tourninfoInr">
					<table class="tournInfoTbl" width="100%">
						<tbody>
							<tr class="sectiontableentry1">
								<td class="sub">Entry Fee</td>
								<td class="na"><?php echo $this->escape($this->tournament->value); ?></td>
							</tr>
							<tr class="sectiontableentry2">
								<td class="sub">Tournament <? echo $this->tournament->private_flag ? "Code" : "No."; ?></td>
								<td class="na"><?php echo  $this->tournament->private_flag ? $this->private_tournament->display_identifier : $this->escape($this->tournament->id); ?></td>
							</tr>
							<tr class="sectiontableentry1">
								<td class="sub">Type</td>
								<td class="na"><?php echo $tournament_type; ?></td>
							</tr>
							<?php if(!$this->is_racing_tournament): ?>
							<tr class="sectiontableentry2">
								<td class="sub">Sport &amp; Competition</td>
								<td class="na"><?php echo $this->escape($this->tournament->sport_name) . ' &ndash; ' . $this->escape($this->tournament->competition_name); ?></td>
							</tr>
							<tr class="sectiontableentry1">
								<td class="sub">Event</td>
								<td class="na"><?php echo $this->escape($this->tournament->event_group_name); ?></td>
							</tr>
							<?php endif; ?>
							<tr class="sectiontableentry2">
								<td class="sub">Betting Closes</td>
								<td class="na"><?php echo $this->tournament->betting_close; ?></td>
							</tr>
							<tr class="sectiontableentry1">
								<td class="sub">Registrations</td>
								<td class="na"><?php echo $this->tournament->entrants; ?></td>
							</tr>
							<tr class="sectiontableentry1">
								<td class="sub">Places Paid</td>
								<td class="na"><?php echo $this->places_paid; ?></td>
							</tr>
							<tr class="subheader">
								<td class="sub2">Prize Pool</td>
								<td class="na"><?php echo $this->prize_pool; echo ($this->free_credit_flag==1) ? ' (Free credit)' : ''; ?></td>
							</tr>
						</tbody>
					</table>

					<div class="moreInfo"><?php echo nl2br($this->escape($this->tournament->description)); ?></div>
			</div>
		<!-- Middle coloumn wrapper -->
		<div class="tourn-middle-col">
			<div id="prizepool">
			<h4>PRIZE POOL DETAILS</h4>

			<div class="poolInfo"><?php echo $this->escape($this->place_title); ?></div>
			<div id="prizepoolInr">
				<table class="prizepoolTbl" width="100%">
					<tbody>
						<tr class="sectiontableheader">
							<td class="cntr">Place</td>
							<td class="cntr">Prize</td>
						</tr>
						<?php
						$class = 1;
						foreach ($this->place_display as $place => $prize) :
						?>
						<tr class="sectiontableentry<?php echo $class; ?>">
							<td class="cntr"><?php echo $place; ?></td>
							<td class="na">
							<?php if ($this->is_free_private_tournament) : ?>
								<div class="trophy"><?=$prize ?></div>
							<?php elseif (substr($prize,0,1)!=='$') : ?>
								<a href="<?=$this->parent_link ?>"><?php echo $prize; ?></a>
							<?php else : echo $prize;
							endif;
							?>
							</td>
						</tr>
						<?php
						$class = ($class == 1) ? 2 : 1;
						endforeach;
						?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- The buttons -->

				<?php if (!empty($this->private_tournament_link) AND !$this->tournament->ended) : ?>
				<div id="privatetournButt" class="tournInfoButt">
				<?php

					$browser_info = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

					if (stristr($browser_info, "msie")) :
						$resize = "x: 365 , y: 486";
					else :
						$resize = "x: 390 , y: 492";
					endif;

				?>
				<a id="privatetournLink" rel="{handler: 'iframe', size: { <?=$resize?> }}" href="<?php echo $this->private_tournament_link; ?>" class="modal" onClick="return false">CREATE PRIVATE TOURNAMENT</a>
				</div>
				<?php endif;?>

				<?php if ($this->tournament->private_flag < 1 AND !empty($this->jackpot_link)) : ?>
				<div id="jackpotButt" class="tournInfoButt">
					<a id="jackpotLink" href="<?php echo $this->jackpot_link; ?>">JACKPOT MAP</a>
				</div>
				<?php endif;?>
	</div>
		<?php if ($this->display_sledge_box) : ?>
		<div id="tournament-sledge-box">
			<div id="sledge-head"><img src="/components/com_tournament/images/sledge-head.gif" border="0" alt="The Sledge"/></div>
			<div id="sledge-box">
			<?php foreach ($this->tournament_comment_list as $comment): ?>
					<div class="sledge-comment"><span class="sledge-user"><?=$comment->username?>: </span> <?=$comment->comment?></div>

			<?php endforeach?>
			</div>

			<? if ($this->allow_sledge_comment) :?>
			<div id="sledge-rules">Keep it clean! Offenders will be <span style="color:red;">red</span> carded.</div>
			<div id="sledge-comment-wrap">
			<form action="/index.php" onsubmit="return validateSledgeForm()" method="post">
				<input type="text" name="tournament_sledge" id="tournament_sledge" class="sledge-input" maxlength="400" value="leave comment here..." onclick="javascript:toggleInitialTxt(this,'leave comment here...')" onblur="javascript:toggleInitialTxt(this,'leave comment here...',1)"/>
				<input type="submit" name="btnTournamentSledge" id="btnTournamentSledge" class="sledge-submit" value="POST IT!"/>
				<input type="hidden" name="display_identifier" value="<?= isset($this->private_tournament->display_identifier) ? $this->private_tournament->display_identifier : '' ?>" />
				<input type="hidden" name="tournament_id" value="<?=$this->tournament->id?>" />
				<input type="hidden" name="component" value="com_tournament" />
				<input type="hidden" name="task" value="saveComment" />
			</form>
			<div id="sledge-error">Please put a comment before submit</div>
			</div>
		<?php endif; // -- end of $this->allow_sledge_comment ?>
		</div>
	<?php endif; // -- endo of $this->display_sledge_box ?>

	</div>
	<?php if ($this->tournament->started) : ?>
	<div id="tournentrants">
	<h4>Tournament Leaderboard</h4>
	<div id="tournentrantsInr">
	<table class="entrantsTbl" width="254">
		<tbody>
		<?php if (!empty($this->leaderboard_rank)) : ?>
			<tr>
				<td class="pos"><?php print $this->leaderboard_rank->rank; ?></td>
				<td class="plyr"><?php print $this->leaderboard_rank->username; ?></td>
				<td class="bucks"><?php print $this->leaderboard_rank->display_currency; ?></td>
			</tr>
			<?php endif; ?>
			<tr class="sectiontableheader">
				<td class="pos">Pos</td>
				<td class="cntr">User</td>
				<td class="cntr">Bucks</td>
			</tr>
			<?php if (!empty($this->leaderboard)) :
				foreach ($this->leaderboard as $leaderboard): ?>
			<tr>
				<td class="pos"><?php print $leaderboard->rank; ?></td>
				<td class="plyr"><?php print $leaderboard->username; ?></td>
				<td class="bucks"><?php print $leaderboard->display_currency; ?></td>
			</tr>
			<?php endforeach; ?>
			<?php if ($this->is_racing_tournament && !empty($this->next_race)): ?>
			<tr>
				<td class="next_race" colspan="3">Results following race: <?php echo $this->next_race-1; ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td class="disclaimer" colspan="3">
					Reminder: Only entrants who have
					bet their entire initial lot of Bettabucks and finish with a positive
					balance (Above $0) are eligible to win prizes.
				</td>
			</tr>
			<?php elseif (!empty($this->tournament_completed)) : ?>
			<tr>
				<td class="notes" colspan="3">No Qualified Entrants<br />
				<?php if ($this->tournament->buy_in > 0) : ?>
					Prize Pool Contributions <?php print (empty($this->tournament_completed) ? 'Will Be Refunded' : 'Refunded') ?>
				<?php endif; ?>
				</td>
			</tr>
			<?php else : ?>
			<tr>
				<td class="notes" colspan="3">No Entrants Yet</td>
			</tr>
			<?php endif;?>
		</tbody>
	</table>
	</div>
	</div>

		<?php else : ?>

	<div id="tournentrants">
	<h4>Registered Entrants</h4>
		<div id="tournentrantsInr">
			<table class="entrantsTbl" width="254">
				<tbody>
					<tr class="sectiontableheader">
						<td class="na">User</td>
						<td class="cntr">Location</td>
					</tr>
					<?php
					$class = 1;
					foreach ($this->player_list as $entrant) :
					?>
					<tr class="sectiontableentry<?php echo $this->escape($class); ?>">
						<td class="na"><?php echo $this->escape($entrant->username); ?></td>
						<td class="cntr"><?php echo $this->escape($entrant->city); ?></td>
					</tr>
					<?php
					$class = ($class == 1) ? 2 : 1;
					endforeach;
					?>
				</tbody>
			</table>
		</div>
	</div>
		<?php endif; ?>

	<div class="clr"></div>

	<div class="lftbuttwrap">
		<div class="unregoButt <?php echo $this->unregister_button_class;  ?>">
			<a id="unregoButt" href="<?php echo $this->unregister_button_link; ?>">UNREGISTER</a>
		</div>
		<div class="regoButt <?php echo $this->register_button_class;  ?>">
			<a id="regoButt" href="<?php echo $this->register_button_link; ?>">ENTER NOW</a>
		</div>
		<div class="gotoButt <?php echo $this->goto_button_class;  ?>">
			<a id="gotoButt" href="<?php echo $this->goto_button_link; ?>"><?php echo $this->tournament->bet_now_txt ?></a>
		</div>
	</div>
		<div id="lobbyButt" class="blueButt">
			<a href="<?php echo $this->lobby_link ?>">MAIN LOBBY</a>
		</div>
		<div class="clr"></div>
	</div>
</div>
