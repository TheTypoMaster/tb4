<?php
defined('_JEXEC') or die('Restricted access');

echo $this->ajax_js;
?>
<div id="bettaWrap" >
  <div class="col-left">
  <?php if(!$this->is_tournament_list) {?>
    <div class="moduletable">
      <div id="sports-tourns-wrap">
		<h3><?php print $this->panel_title; ?></h3>
		<div class="sports-tourns-filter-wrap">
		<?php if('racing' == $this->tournament_type) {?>
			<form class="sports-tourns-filter-form">
      	<?php foreach($this->filters as $filter => $options) { ?>
	      		<select name="<?php echo $this->escape($filter) ?>" id="<?php echo $this->escape($filter) . '_option' ?>" class="sport-filter">
					<?php foreach($options as $k => $v) {?>
	      			<option value="<?php echo $this->escape($k) ?>"<?php echo ($k == $this->selected_filter ? ' selected="selected"' : '')?>><?php echo $this->escape($v) ?></option>
	      			<?php } ?>
	      		</select>
      			<?php } ?>
      		</form>
      	<?php } else {?>
			<form class="sports-tourns-filter-form">
				<?php foreach($this->filters as $filter => $options) :?>
		      	<select name="<?php echo $this->escape($filter) ?>" id="<?php echo $this->escape($filter) . '_option'  ?>" class="sport-filter">
					<?php foreach($options as $k => $v) {
							$selected = '';
							if (($filter == "sport" && $k == $this->sport_id) || ($filter == "competition" && $k == $this->competition_id) || ($filter == "tournament_type" && $k == 'jackpot' && $this->jackpot))
							{
								$selected = ' selected="selected"';
							}
					?>

		      		<option value="<?php echo $this->escape($k) ?>"<?=$selected?>><?php echo $this->escape($v) ?></option>
		      		<?php } ?>
		      	</select>
		      	<?php endforeach ?>
			</form>
      	<?php }?>
      	</div>
<?php }?>
      	<div id="event_list">
      <?php if(!empty($this->title_list)) { ?>
        <?php foreach($this->title_list as $meeting_id => $data) { ?>
        <div class="toggWrap">
          <div class="toggler atStart1 accordHead">
            <div class="accordHeadL">
              <div class="sports-img">
              	<img border="0" alt="" src="/components/com_tournament/images/<?php echo $data['image'] ?>.png" />
              </div>
              <div class="<?php echo $this->accord_title_class ?>">&nbsp;<?php echo $this->escape($data['competition_name']); ?> &mdash; <?php echo $this->escape($data['event_name'])?></div>
            </div>
            <div class="accordHeadR">
              <div class="Aarrow">&nbsp;</div>
              <?php if(isset($data['track'])) {?>
              <div class="track"> | Track: <?php echo $data['track']; ?></div>
              <?php }?>
              <?php if(isset($data['weather'])) {?>
              <div class="weather">Weather: <?php echo $data['weather']; ?></div>
              <?php }?>
              <div class="<?php echo $data['time_class']; ?>"><?php echo $data['time']; ?></div>
            </div>
          </div>
          <div class="element atStart2">
			<div class="accListItm">
	            <table class="sports-tourn-details" width="100%">
	            <tbody>
	              <tr class="sectiontableheader">
	                <th>ID</th>
	                <th>Buy In</th>
	                <th>Entrants</th>
	                <th>Type</th>
	                <th>Places</th>
	                <th>Pool</th>
	                <th></th>
	                <th></th>
	              </tr>
	              <?php
	                    foreach($this->sorted_list[$meeting_id] as $tournament) {
	              ?>
	              <tr id="<?php echo $tournament->id; ?>" class="sectiontableentry1 tournlink">
	                <td class="na"><?php echo $tournament->id; ?></td>
	                <td class="buy-in"><?php echo $tournament->value; ?></td>
	                <td class="na"><?php echo $tournament->entrants; ?></td>
	                <td class="na"><?php echo $tournament->gameplay; ?></td>
	                <td class="na"><?php echo $tournament->places_paid; ?></td>
	                <td class="na"><?php echo $tournament->display_pool; echo ($tournament->free_credit_flag==1) ? ' (Free credit)' : ''; ?></td>
	                <td class="sportstourn-info-but"><a href="<?php print $tournament->info_link_href; ?>">Details &gt;</a></td>
	                <td class="sportstourn-reg-but"><a href="<?php print $tournament->entry_link_href; ?>" class="<?php echo $tournament->entry_link_class; ?>"><?php echo $tournament->entry_link_text ?> &gt;</a></td>
	              </tr>
	              <?php
	                    }
	              ?>
	            </tbody></table>
            </div>
          </div>

        </div>
        <?php
              }
          } else {
            print 'New tournaments are loaded daily at 10am ' . $this->time_zone;
          }
        ?>
        </div>
<?php if(!$this->is_tournament_list) {?>
      </div>
    </div>
  </div>

	<div class="col-right">
    	<?php echo $this->right_col ?>
	</div><!-- close col-right -->
</div>
<?php } ?>
