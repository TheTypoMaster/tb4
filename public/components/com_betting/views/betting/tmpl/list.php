<?php
defined('_JEXEC') or die('Restricted access');

$current_meeting_type 	= null;
$tag_closed				= true;
?>
     	<div class="betting-wrap">
			<h3><?php echo $this->header ?> <?php echo $this->time_zone; ?>
                <span class="info-button pos2 tourn-info-listen-now">
                <a href="http://www.sport927.com.au/sport927.asx">LISTEN NOW&nbsp;<img src="components/com_tournament/images/icon-listen.png" width="11" height="11" /></a>
                </span>
                <span class="info-button pos1"> 
                <?php echo $this->next_to_jump ?>
                </span>
            </h3>
			<?php if (empty($this->meeting_list)) : ?>
				<div class="data-unavail">Race data is temporarily unavailable. Please check back later.</div>
			<?php else : ?>
				<?php foreach ($this->meeting_race_list as $meeting_type => $meeting_race_list) : ?>
					<?php if ($meeting_type != $current_meeting_type ) :?>
						<?php if (!$tag_closed) :?>
							</table> 
						</div> 
						<div class="clr"></div>			
					</div>
				</div><!-- close toggWrap -->
							<?php $tag_closed = true; ?>
						<?php endif; ?>
            <div class="toggWrap">
          		<div class="toggler atStart1 accordHead">
		            <div class="accordHeadL">
		              <div class="sports-img"><img src="templates/topbetta/images/icn_<?php echo $this->escape($meeting_type) ?>_sml-white.png" border="0" alt=""/></div>
		              <div class="accord-title"><?php echo $this->escape($meeting_type) ?></div>
		            </div>
		            <div class="accordHeadR">
		              <div class="Aarrow">&nbsp;</div>
		            </div>
          		</div>
          		<div class="element atStart2">
					<div class="todaysRacesRow"> 
						<table class="todaysRacesTbl" width="100%">
                        	<tr class="todaysRacesHead">
                            	<td>Races</td>
                            	<?php for($i=1; $i<= $this->meeting_race_limit[$meeting_type]; $i++) : ?>
                                <td><?php echo $i ?></td>
                                <?php endfor; ?>
                            </tr>
						<?php $current_meeting_type = $meeting_type ?>
						<?php $tag_closed = false; ?>
					<?php endif; ?>

					<?php foreach ($meeting_race_list as $meeting_race) : ?>
								<tr class="todaysRacesMeetInfo"> 
									<td class="meetingName"><?php echo $this->escape($meeting_race['meeting_name']) ?></td>
									<?php for ($i=1; $i <= $this->meeting_race_limit[$meeting_type]; $i++) : ?>
									<td class="<?php echo isset($meeting_race['race_list'][$i]['class']) ? $meeting_race['race_list'][$i]['class'] : '' ?>">
										<?php if (isset($meeting_race['race_list'][$i])) : ?>
										<a class="bet_link" href="<?php echo $meeting_race['race_list'][$i]['link'] ?>" title="<?php echo $this->escape($meeting_race['race_list'][$i]['tips_title'])?> :: <?php echo nl2br($this->escape($meeting_race['race_list'][$i]['tips_body'])) ?>">
											<?php echo isset($meeting_race['race_list'][$i]['label']) ? $meeting_race['race_list'][$i]['label'] : '' ?>
										</a>
										<?php endif; ?>
									</td>
									<?php endfor; ?>
								</tr>
					<?php endforeach; ?>
					
					<?php if (!$tag_closed) :?>
							</table> 
						</div> 
						<div class="clr"></div>			
					</div>
				</div><!-- close toggWrap -->
						<?php $tag_closed = true; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
        </div><!-- close sports-tourns-wrap -->