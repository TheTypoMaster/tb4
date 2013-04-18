<?php
defined('_JEXEC') or die('Restricted access');
?>
        <h3>My Tournament Tickets</h3>
        <div class="fixed350">
        <?php foreach($this->ticket_list as $tournament_id => $ticket) { ?>
            <div id="tix<?php $tournament_id; ?>" title="<?php print $ticket->link; ?>" class="tixlinks divLink">
              <table class="tournInfo_1" width="100%">
                <tr class="sectiontableheader">
                  <td class="up_type"><?php print $ticket->sport_name; ?></td>
                  <td class="up_location"><?php echo $ticket->meeting_name; ?></td>
                  <td class="<?php echo $ticket->status_class; ?>"><?php echo $ticket->status; ?></td>
                </tr>
              </table>
              <div class="clr"></div>
              <table class="tournInfo_2" width="100%">
                <tr class="sectiontableheader">
                  <td class="up_value"><?php echo $ticket->value; ?></td>
                  <td class="up_buckhdr">Available Bucks:</td>
                  <td class="up_buck"><?php echo $ticket->currency; ?></td>
                </tr>
              </table>
              <div class="clr"></div>
            </div>
          <?php } ?>
      </div>