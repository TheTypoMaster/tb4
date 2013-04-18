<?php
defined('_JEXEC') or die('Restricted access');

echo $this->ajax_js;
?>
<div class="col-left">
<?php if(!$this->is_tournament_list) :?>
<div id="sports-tourns-wrap" >
	<h3><?php print $this->panel_title; ?></h3>
	<div class="sports-tourns-filter-wrap">
		<form class="sports-tourns-filter-form">
			<?php foreach($this->filters as $filter => $options) :?>
	      	<select name="<?php echo $this->escape($filter) ?>" id="<?php echo $this->escape($filter) . '_option'  ?>" class="sport-filter">
				<?php foreach($options as $k => $v) {
						$selected = '';
						if (($this->escape($filter) == "sport" && $k == $this->sport_id) || ($this->escape($filter) == "competition" && $k == $this->competition_id) || ($this->escape($filter) == "tournament_type" && $k == 'jackpot' && $this->jackpot))
						{
							$selected = ' selected="selected"';
						}
				?>

	      		<option value="<?php echo $this->escape($k) ?>"<?=$selected?>><?php echo $this->escape($v) ?></option>
	      		<?php } ?>
	      	</select>
	      	<?php endforeach ?>
		</form>
	</div>
<?php endif ?>
	<div id="event_list">
	<?php if(!empty($this->title_list)) : ?>
        <?php foreach($this->title_list as $event_id => $data) : ?>
        <div class="toggWrap">
        	<div class="toggler atStart1 accordHead">
        		<div class="accordHeadL">
        			<div class="sports-img">
        				<img border="0" alt="" src="/components/com_tournament/images/<?php echo $data['image'] ?>.png" />
        			</div>
        			<div class="na">&nbsp;<?php echo $this->escape($data['competition_name']); ?> &mdash; <?php echo $this->escape($data['event_name'])?></div>
        		</div>
        		<div class="accordHeadR">
        			<div class="Aarrow">&nbsp;</div>
        			<div class="<?php echo $data['time_class']; ?>"><?php echo $data['time']; ?></div>
        		</div>
        	</div>

        	<div class="element atStart2">
        		<table class="sports-tourn-details" width="100%">
        			<tbody>
	        			<?php $jackpot_flag=null; ?>
	                    <?php foreach($this->sorted_list[$event_id] as $tournament) : ?>
	                    <?php if($jackpot_flag !== $tournament->jackpot_flag) : ?>
	                    <?php $jackpot_flag = $tournament->jackpot_flag; ?>
	                    <?php $type = $jackpot_flag  ? 'Jackpot Tournaments' : 'Cash Tournaments'; ?>
	        			<tr class="sectiontableheader">
	        				<th colspan="7"><?php echo $type ?></th>
	        			</tr>
	        			<?php endif ?>
	        			<tr class="sectiontableentry1 tournlink">
	        				<td class="na">
	        					<span class="sportstourn-details-title">ID</span><span class="sportstourn-details-info"><?php echo $tournament->id; ?></span>
	        				</td>
	        				<td class="na">
	        					<span class="sportstourn-details-title">Buy In</span><span class="sportstourn-details-info buy-in"><?php echo $tournament->value; ?></span>
	        				</td>
	        				<td class="na">
	        					<span class="sportstourn-details-title">Entrants</span><span class="sportstourn-details-info"><?php echo $tournament->entrants; ?></span>
	        				</td>
	        				<td class="na">
	        					<span class="sportstourn-details-title">Places</span><span class="sportstourn-details-info"><?php echo $tournament->places_paid; ?></span>
	        				</td>
	        				<td class="na">
	        					<span class="sportstourn-details-title">Pool</span><span class="sportstourn-details-info"><?php echo $tournament->display_pool; ?></span>
	        				</td>
	        				<td class="na">
	        					<a href="<?php echo $tournament->info_link_href; ?>" class="sportstourn-info-but">Details &gt;</a>
	        				</td>
	        				<td class="na">
	        					<a href="<?php echo $tournament->entry_link_href; ?>" class="sportstourn-reg-but <?php echo $tournament->entry_link_class; ?>"><?php echo $tournament->entry_link_text ?> &gt;</a>
	        				</td>
	        			</tr>
        				<?php endforeach ?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php endforeach ?>
        <?php else :?>
            New tournaments are loaded daily at 10am <?php echo $this->time_zone; ?>;
        <?php endif ?>
	</div>
<?php if(!$this->is_tournament_list) : ?>
</div>
</div>

<div class="col-right">
    <?php echo $this->right_col ?>
</div><!-- close col-right -->
<?php endif ?>