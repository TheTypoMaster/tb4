<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$event_group_id = null;
$tag_closed = true;
?>
<link type="text/css" href="/components/com_tournament/assets/tourninfo.default.css" rel="stylesheet">
<div class="upcoming-tournaments">
	<div class="upcoming-tournaments-head">Upcoming Tournaments</div>
	<?php if (empty($uc_tournament_list)) : ?>
  		<div>Tournament data is temporarily unavailable. Please check back later.</div>
	<?php else : ?>
    	<?php foreach($uc_tournament_list as $tournament) : ?>
    		<?php if ($event_group_id != $tournament->event_group_id) : ?>
    			<?php if(!$tag_closed) : ?>
	                </table>
	            </td>
	        </tr>
		</table>
	        	<?php $tag_closed = true; ?>
    			<?php endif;?>
		<table class="upcoming-tournaments-table">
			<tr>
	             <td class="upcoming-tournaments-info-head odd"><img src="<?php echo $tournament->image ?>" border="0" alt=""/> <?php echo $this->escape($tournament->competition_name)?> &mdash; <?php echo $this->escape($tournament->event_group_name)?></td>
	             <td class="tourn-start-time odd"><div class="<?php echo $tournament->time_class ?>"><?php echo $tournament->time ?></div></td>
	        </tr>
	        <tr>
	        	<td colspan="2">
	                <table class="upcoming-tournament-details">
	                   <tr class="upcoming-tournaments-th"> 
	                      <th>ID</th> 
	                      <th>Buy In</th> 
	                      <th>Entrants</th> 
	                      <th>Type</th> 
	                      <th>Places</th> 
	                      <th>Pool</th> 
	                      <th></th> 
	                      <th></th> 
		              </tr>
			<?php $tag_closed = false ?>
			<?php endif; ?>
		              <tr id="9821" class="upcoming-tournament-info odd"> 
		                <td class=""><?php echo $tournament->id ?></td> 
		                <td class=""><?php echo $tournament->value ?></td> 
		                <td class=""><?php echo $tournament->entrants ?></td> 
		                <td class=""><?php echo $tournament->type ?></td> 
		                <td class=""><?php echo $tournament->places_paid ?></td> 
		                <td class=""><?php echo $tournament->prize_pool ?></td> 
		                <td class="sportstourn-info-but"><a href="<?php echo $tournament->info_link_href ?>">Details &gt;</a></td> 
		                <td class="sportstourn-reg-but"><a href="<?php echo $tournament->entry_link_href ?>" class="<?php echo $tournament->entry_link_class ?>"<?php echo $tournament->is_light_box_link ? ' onClick="return false;"' : ''?>><?php echo $tournament->entry_link_text ?> &gt;</a></td> 
		              </tr> 
	     	<?php $event_group_id = $tournament->event_group_id; ?>
		<?php endforeach; ?>
		<?php if (!$tag_closed) : ?>
	                </table>
	            </td>
	        </tr>
		</table>
		<?php endif; ?>
	<?php endif; ?>
</div><!-- close upcoming-tournaments -->