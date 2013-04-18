<?php
defined('_JEXEC') or die('Restricted access');
?>
<div id="bettaWrap" >
  <div class="col-left">
    <div class="moduletable">
      <div id="racing-tourns-wrap">
		<h3><?php print $this->panel_title; ?></h3>
		<div class="racing-tourns-filter-wrap">
      	<?php foreach($this->filters as $filter => $options) { ?>
      		<select name="<?php echo $this->escape($filter) ?>" id="<?php echo $this->escape($filter)  ?>">
				<?php foreach($options as $k => $v) {?>
      			<option value="<?php echo $k ?>"<?php echo ($k == $this->selected_filter ? ' selected="selected"' : '')?>><?php echo $this->escape($v) ?></option>
      			<?php } ?>
      		</select>
      	<?php } ?>
      	</div>
      <?php if(!empty($this->title_list)) { ?>
        <?php foreach($this->title_list as $meeting_id => $data) { ?>
        <div class="toggWrap">
          <div class="toggler atStart1 accordHead">
            <div class="accordHeadL">
              <div class="<?php echo $this->escape($data['class']); ?>">&nbsp;</div>
              <div class="na"><?php echo $data['title']; ?></div>
            </div>
            <div class="accordHeadR">
              <div class="Aarrow">&nbsp;</div>
              <div class="track"> | Track: <?php echo $data['track']; ?></div>
              <div class="weather">Weather: <?php echo $data['weather']; ?></div>
              <div class="<?php echo $data['time_class']; ?>"><?php echo $data['time']; ?></div>
            </div>
          </div>
          <div class="element atStart2">

            <table class="tournDetailsTbl" width="100%"><tbody>
              <tr class="sectiontableheader">
                <td class="na">ID</td>
                <td class="na">BUY IN</td>
                <td class="na">ENTRANTS</td>
                <td class="na">TYPE</td>
                <td class="na">PLACES PAID</td>
                <td class="na">PRIZE POOL</td>
                <td class="na"><?php echo $data['tab_meeting_id']; ?></td>
              </tr>
              <?php
                    $class = 1;
                    foreach($this->sorted_list[$meeting_id] as $tournament) {
              ?>
              <tr id="<?php echo $tournament->id; ?>" class="sectiontableentry<?php echo $class; ?> tournlink">
                <td class="na"><?php echo $tournament->id; ?></td>
                <td class="upr"><?php echo $tournament->value; ?></td>
                <td class="na"><?php echo $tournament->entrants; ?></td>
                <td class="na"><?php echo $tournament->gameplay; ?></td>
                <td class="na"><?php echo $tournament->places_paid; ?></td>
                <td class="na"><?php echo $tournament->display_pool; ?></td>
                <td class="entrButt"><a class="entrButt" href="<?php print $tournament->entry_link_href; ?>"><?php print $tournament->entry_link_text; ?></a></td>
              </tr>
              <?php
                      $class = ($class == 1) ? 2 : 1;
                    }
              ?>
            </tbody></table>
          </div>
        </div>
        <?php
              }
          } else {
            print 'New tournaments are loaded daily at 10am ' . $this->time_zone;
          }
        ?>
      </div>
    </div>
  </div>

	<div class="col-right">
    	<?php echo $this->right_col ?>
	</div><!-- close col-right -->
</div>
