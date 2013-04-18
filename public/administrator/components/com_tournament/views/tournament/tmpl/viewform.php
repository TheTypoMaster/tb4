<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
  fieldset {
    width: 30%;
  }
</style>
<div>
<fieldset><legend><?php echo $this->tournament_details_title ?>
&mdash; <?php echo JText::_($this->escape($this->tournament->name)); ?></legend>
<table class="admintable">
	<tr>
		<td class="key">Entry Fee</td>
		<td><?php echo $this->escape($this->tournament->value); ?></td>
	</tr>
	<tr>
		<td class="key">Tournament No.</td>
		<td><?php echo  $this->escape($this->tournament->id); ?></td>
	</tr>
	<tr>
		<td class="key">Type</td>
		<td><?php echo $this->tournament_type; ?></td>
	</tr>
	<?php if(!$this->is_racing_tournament): ?>
	<tr>
		<td class="key">Sport &amp; Competition</td>
		<td><?php echo $this->escape($this->tournament->sport_name) . ' &ndash; ' . $this->escape($this->tournament->competition_name); ?></td>
	</tr>
	<tr>
		<td class="key">Event</td>
		<td><?php echo $this->escape($this->tournament->event_group_name); ?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="key">Betting Closes</td>
		<td><?php echo $this->tournament->betting_close; ?></td>
	</tr>
	<tr>
		<td class="key">Registrations</td>
		<td><?php echo $this->tournament->entrants; ?></td>
	</tr>
	<tr>
		<td class="key">Places Paid</td>
		<td><?php echo $this->places_paid; ?></td>
	</tr>
	<tr>
		<td class="key">Prize Pool</td>
		<td><?php echo $this->prize_pool; ?></td>
	</tr>
</table>
</fieldset>
</div>
<?php if($this->tournament->private_flag > 0 && !empty($this->private_tournament)) : ?>
<div>
	<fieldset>
		<legend><?php echo JText::_( 'Private Tournament info'); ?></legend>
		<table class="admintable">
		  <tr>
		    <td class="key"><?php echo JText::_( 'Tournament Creator' ); ?></td>
		    <td><?php
		    	echo $this->escape($this->private_tournament->first_name ? $this->private_tournament->first_name . $this->private_tournament_creator->last_name : $this->private_tournament->name);
				echo ' (' . $this->escape($this->private_tournament->username) . ')';
				?></td>
		  </tr>
		  <tr>
		    <td class="key"><?php echo JText::_( 'Tournament Code' ); ?></td>
		    <td><?php echo $this->escape($this->private_tournament->display_identifier) ?></td>
		  </tr>
		  <tr>
		    <td class="key"><?php echo JText::_( 'Tournament Password' ); ?></td>
		    <td><?php echo $this->escape($this->private_tournament->password) ?></td>
		  </tr>
		  <tr>
		    <td class="key"><?php echo JText::_( 'Referral ID' ); ?></td>
		    <td><?php echo $this->escape($this->private_tournament->user_id) ?></td>
		  </tr>
		</table>
</fieldset>
</div>
<?php endif; ?>

<fieldset>
	<legend>
		<?php echo JText::_( 'Prize Pool Details'); ?>
		&mdash; <?php echo $this->escape($this->place_title); ?>
	</legend>
	<?php if(!empty($this->place_display)) : ?>
	<table id="prize-pool-details" class="adminlist" width="60%">
		<thead>
			<tr>
				<th width="30%"><?php echo JText::_( 'Place' ); ?></th>
				<th><?php echo JText::_( 'Prize' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php $class = 1; ?>
		<?php foreach ($this->place_display as $place => $prize) : ?>
			<tr>
				<td><?php echo $place; ?></td>
				<td>
				<?php if ($this->is_free_private_tournament) : ?>
					<?php echo '<div class="trophy">' . $prize .'</div>'; ?>
				<?php elseif (substr($prize,0,1)!=='$') : ?>
					<a href="<?php echo $this->parent_link; ?>"><?php echo $prize; ?></a>
				<?php else : ?>
					<?php echo $prize; ?>
				<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</fieldset>


<div>
	<fieldset>
		<legend>
			<?php echo JText::_( 'Entrants Details'); ?>
			&mdash; <?php echo $this->tournament->entrants . ' ' . JText::_( 'Entrant(s)' ) ?>
		</legend>
		<?php if ($this->entrants_export_link) :?>
		<div><a href="<?php echo $this->entrants_export_link; ?>">Download Entrant Details</a></div>
		<?php endif; ?>
		<?php if ($this->tournament->started) : ?>
		<table class="adminlist">
			<thead>
				<tr>
					<th><?php echo JText::_( 'Pos' ) ?></th>
					<th><?php echo JText::_( 'User' ) ?></th>
					<th><?php echo JText::_( 'Bucks' ) ?></th>
					<?php if (empty($this->tournament->paid_flag)) : ?>
					<th>&nbsp;</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php if (!empty($this->leaderboard)) : ?>
				<?php foreach($this->leaderboard as $leaderboard) : ?>
				<tr>
					<td class="pos"><?php print $leaderboard->rank; ?></td>
					<td class="plyr"><?php print $leaderboard->username; ?></td>
					<td class="bucks"><?php print $leaderboard->display_currency; ?></td>
					<?php if (empty($this->tournament->paid_flag)) : ?>
					<td><?php echo '<a onclick="return confirm(\'Are you sure you want to unregister ' . $this->escape($leaderboard->username) . '?\');" href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentracing&task=unregister&user={$leaderboard->id}&id={$this->tournament->id}") . '">' . JText::_( 'Unregister' ) . '</a>' ?></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			<?php elseif (!empty($this->tournament->paid_flag)) : ?>
				<tr>
					<td class="pos" colspan="3">
						No Qualified Entrants<br />
					<?php if ($this->tournament->buy_in > 0) : ?>
            			Prize Pool Contributions Refunded
					<?php endif; ?>
          			</td>
				</tr>
			<?php else : ?>
				<tr>
					<td class="pos" colspan="4">No Entrants Yet</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php else : ?>


		<table class="adminlist">
		  <thead>
		    <tr>
		      <th><?php echo JText::_( 'User' ) ?></th>
		      <th><?php echo JText::_( 'Location' ) ?></th>
		      <th>&nbsp;</th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php if (empty($this->player_list)) : ?>
		    <tr><td colspan="3">No Entrants Yet</td></tr>
		    <?php endif; ?>
		
			<?php foreach ($this->player_list as $entrant) : ?>
		    <tr>
		      <td class="na"><?php echo $this->escape($entrant->username); ?></td>
		      <td class="cntr"><?php echo $this->escape($entrant->city); ?></td>
		      <td><?php print '<a onclick="return confirm(\'Are you sure you want to unregister ' . $this->escape($entrant->username) . '?\');" href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentracing&task=unregister&user={$entrant->user_id}&id={$this->tournament->id}") . '">' . JText::_( 'Unregister' ) . '</a>' ?></td>
		    </tr>
		    <?php endforeach; ?>
		  </tbody>
		</table>
    	<?php endif; ?>
	</fieldset>
</div>