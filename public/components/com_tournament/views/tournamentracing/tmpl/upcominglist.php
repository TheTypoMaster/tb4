<?php
defined('_JEXEC') or die('Restricted access');
?>
      <h3>Upcoming Events</h3>
      <div class="fixed350">
        <?php foreach($this->upcoming_list as $upcoming) { ?>
        <div id="mid_<?php echo $upcoming->meeting_id; ?>" title="<?php print $upcoming->link; ?>" class="upcoming divLink">
          <table class="tournInfo_1" width="100%">
            <tr class="ticketlink">
              <td class="up_type"><?php echo $upcoming->sport_name; ?></td>
              <td class="up_location"><?php echo $upcoming->meeting_name; ?></td>
              <td class="<?php echo $upcoming->status_class; ?>"><?php echo $upcoming->status ?></td>
            </tr>
          </table>
          <div class="clr"></div>
          <table class="tournInfo_2" width="100%">
            <tr class="ticketlink">
              <td class="up_tourns"><?php echo $upcoming->tournament_count; ?></td>
              <td class="up_poolhdr">Prize Pool(s)</td>
              <td class="up_pool"><?php echo $upcoming->prize_pool; ?></td>
            </tr>
          </table>
          <div class="clr"></div>
        </div>
        <?php } ?>
      </div>