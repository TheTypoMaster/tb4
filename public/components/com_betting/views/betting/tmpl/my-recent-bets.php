<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<div class="tickets-tables">
									<?php if (empty($this->bets_recent)) :?>
										<div>There are no recent bets.</div>
									<?php else :?>
										<?php foreach ($this->bets_recent as $bet_id => $bet) :?>
                                        <div class="tournament-tickets-group">
	                                    	 <table class="tournament-tickets-table" onclick="javascript:window.location.href = '/betting/racing/meeting/<? echo $bet['meeting_id'] ?>/<?php echo $bet['race_number']; ?>';" onmouseover="this.style.cursor = 'pointer';" id="ticket_bet_<?php echo htmlspecialchars($bet_id); ?>" ref="racing">
	                                                <tr class="ticket-row-top" valign="middle">
	                                                    <td class="table-sports-icon" rowspan="2" valign="middle" width="64">
	                                                    <img src="templates/topbetta/images/<?php echo htmlspecialchars($bet['icon']); ?>" border="0" alt="<?php echo htmlspecialchars($bet['compeition_name']); ?>"/>
	                                                    <div style="display:none" id="ticket_bet_<?php echo htmlspecialchars($bet_id); ?>_meeting"><?php echo htmlspecialchars($bet['meeting_id']); ?></div>
	                                                    <div style="display:none" id="ticket_bet_<?php echo htmlspecialchars($bet_id); ?>_race"><?php echo htmlspecialchars($bet['race_number']); ?></div>
	                                                    </td>
	                                                    <td>Ticket No. <?php echo htmlspecialchars($bet_id); ?></td>
	                                                    <td>Bet time: <?php echo htmlspecialchars($bet['bet_time']); ?></td>
	                                                    
	                                                </tr>
	                                                <tr class="ticket-row-bot">                                                  
	                                                    <td colspan="2"><?php echo htmlspecialchars($bet['label']); ?></td>
	                                                </tr>
	                                                
	                                                <tr class="ticket-row-bot bet-result-<?php echo htmlspecialchars($bet['result']); ?>">                                                  
	                                                    <td colspan="2" class="bet-result-left">RESULT: <?php echo htmlspecialchars($bet['result']); ?></td>
	                                                    <td class="bet-result-right"><?php echo htmlspecialchars($bet['win_amount']); ?></td>
	                                                </tr>
	                                                
	                                            </table>
	                                    </div>
                                        <?php endforeach; ?>
									<?php endif; ?>
                                    </div>