<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<div class="tickets-tables">
									<?php if (empty($this->tickets_open)) :?>
										<div>There are no open tournament tickets.</div>
									<?php else :?>
										<?php foreach ($this->tickets_open as $sport => $tickets) :?>
										<div class="tournament-tickets-group">
											<?php foreach ($tickets as $ticket) :?>
											<table class="tournament-tickets-table" onclick="javascript:window.location.href = '/tournament/<? echo $ticket['tournament_type'] ?>/game/<? echo $ticket['tournament_id'] ?>';" onmouseover="this.style.cursor = 'pointer';" id="<?php echo htmlspecialchars($ticket['ticket_id']) ?>" ref="<? echo $ticket['tournament_type'] ?>">
												<tr class="ticket-row-top" valign="middle">
														<td class="table-sports-icon" rowspan="2" valign="middle" width="64">
																<img src="templates/topbetta/images/<?php echo htmlspecialchars($ticket['icon']) ?>" border="0" alt="<?php echo htmlspecialchars($sport) ?>"/>
																<div style="display:none" id="<?php echo htmlspecialchars('ticket_tournament_' . $ticket['ticket_id']) ?>"><?php echo $ticket['tournament_id'] ?></div>
															</td>
															<td width="60"><?php echo htmlspecialchars($ticket['buy_in']) ?></td>
															<td colspan="2"><?php echo htmlspecialchars($ticket['tournament_name']) ?></td>
															<td class="<?php echo htmlspecialchars($ticket['bet_open_class']) ?>"><?php echo htmlspecialchars($ticket['bet_open_txt'])?></td>
													</tr>
													<tr class="ticket-row-bot">
														<td class="<?php echo htmlspecialchars($ticket['qualified_class'])?>"><?php echo htmlspecialchars($ticket['qualified_txt'])?></td>
															<td width="90"><span class="tourn-light-text">Ranking</span> <?php echo htmlspecialchars($ticket['leaderboard_rank'])?></td>
															<td width="90"><span class="tourn-light-text">BettaBucks</span></td>
															<td class="ticket-bettabucks-amount"><?php echo htmlspecialchars($ticket['betta_bucks'])?></td>
													</tr>
											</table>
											<?php endforeach ?>
                                            
										</div>
									<?php endforeach ?>
								<?php endif ?>
									</div><!-- close tickets tables -->