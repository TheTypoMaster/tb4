<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$winnertText = $winnertFbText = '';
?>
<div id='fb-root'></div>    
<script> 
      function postToFeed(msg) {
		//FB.init({appId: '431208330263551', status: false, cookie: true, xfbml: true, oauth: true, channelUrl: 'http://staging.mugbookie.com/components/com_jfbconnect/assets/jfbcchannel.php'});
        // calling the API ...
        var obj = {
          method: 'feed',
          link: '<?php echo JURI::current(); ?>',
          picture: '',
          name: 'Topbetta',
          caption: 'I am a TopBetta!!!',
          description: msg
        };

        function callback(response) {
          //document.getElementById('winnermsg').innerHTML = "Post ID: " + response['post_id'];
        }

        FB.ui(obj, callback);
      }
    
    </script>
<div class="tickets-tables">
									<?php if (empty($this->tickets_recent)) :?>
										<div>There are no recent tournament tickets.</div>
									<?php else :?>
										<?php foreach ($this->tickets_recent as $sport => $tickets) :?>
										<div class="tournament-tickets-group">
											<?php foreach ($tickets as $ticket) :?>
										<table class="tournament-tickets-table" onclick="javascript:window.location.href = '/tournament/details/<? echo $ticket['tournament_id'] ?>';" onmouseover="this.style.cursor = 'pointer';" id="<?php echo htmlspecialchars($ticket['ticket_id']) ?>" ref="<? echo $ticket['tournament_type'] ?>">
												<tr class="ticket-row-top" valign="middle">
														<td class="table-sports-icon" rowspan="2" valign="middle" width="64">
																<img src="templates/topbetta/images/<?php echo htmlspecialchars($ticket['icon']) ?>" border="0" alt="<?php echo htmlspecialchars($sport) ?>"/>
																<div style="display:none" id="<?php echo htmlspecialchars('ticket_tournament_' . $ticket['ticket_id']) ?>"><?php echo htmlspecialchars($ticket['tournament_id']) ?></div>
															</td>
															<td width="60"><?php echo htmlspecialchars($ticket['buy_in']) ?></td>
															<td colspan="2"><?php echo htmlspecialchars($ticket['tournament_name']) ?></td>
															<td class="<?php echo $ticket['bet_open_class']?>"><?php echo htmlspecialchars($ticket['bet_open_txt'])?></td>
													</tr>
													<tr class="ticket-row-bot">
														<td class="<?php echo $ticket['qualified_class']?>"><?php echo htmlspecialchars($ticket['qualified_txt'])?></td>
															<td width="90"><span class="tourn-light-text">Placing</span> <?php echo htmlspecialchars($ticket['leaderboard_rank'])?></td>
															<td width="90"><span class="tourn-light-text">My Prize</span></td>
															<td class="ticket-bettabucks-amount"><?php echo htmlspecialchars($ticket['prize'])?></td>
													</tr>
											</table>
											<?php endforeach ?>
									</div>
                                     <!-- winner alert -->
                                        <?php foreach ($tickets as $ticket) :?>
                                        	<?php if (trim($ticket['prize']) !== '$0.00' && $ticket['winner_alert_flag'] == 0) {  
											$winnertText .= "<table class=\"tournament-tickets-table\">
																<tr valign=\"middle\">
																	<td class=\"table-sports-icon\" rowspan=\"2\" valign=\"middle\" width=\"64\">
																	<img src=\"templates/topbetta/images/". htmlspecialchars($ticket['icon']) ."\" border=\"0\" alt=\"". htmlspecialchars($sport) ."\"/>
																	</td>
																	<td width=\"60\">". htmlspecialchars($ticket['buy_in']) ."</td>
																	<td colspan=\"2\">". htmlspecialchars($ticket['tournament_name']) . "</td>
																	<td class=\"". $ticket['bet_open_class'] . "\">". htmlspecialchars($ticket['bet_open_txt']) ."</td>
																</tr>
																<tr class=\"ticket-row-bot\">
																	<td class=\"" . $ticket['qualified_class'] . "\">". htmlspecialchars($ticket['qualified_txt']) ."</td>
																	<td width=\"90\"><span class=\"tourn-light-text\">Placing</span> ". htmlspecialchars($ticket['leaderboard_rank']) . "</td>
																	<td width=\"90\"><span class=\"tourn-light-text\">My Prize</span></td>
																	<td class=\"ticket-bettabucks-amount\">". htmlspecialchars($ticket['prize']) ."</td>
																</tr>
															</table>";
															
											 $winnertFbText .= "I just won ". htmlspecialchars($ticket['prize']) ." in the ".htmlspecialchars($ticket['tournament_name']). '\n\r'; 
											 $winnertFbText .=  " tournament at TopBetta" . '\n\r';
											// $winnertFbText .= 	"I am a TopBetta!!!" .'\n';
											 $winnertFbText .= 	"Come on now and bet with me at TopBetta. " .'\n\r';
											
											} ?>
                                        <?php endforeach ?>
                                        <!-- winner alert end -->
									<?php endforeach ?>
								<?php endif ?>
                                <script type="text/javascript">

  
										window.addEvent('domready', function() {
								
											SqueezeBox.initialize({});
								
											url = this.href + '#winnerAlert';
			
										params = {
											'width' : 600,
											//'height' : 200,
											//'dynamic_size' : true,
											//'div_to_reset' : 'winnerAlert'
										};
										

										<?php if(!empty($winnertText))  { ?> 
											document.getElementById('winnerAlert').style.display = 'block';
											loadLightbox(url, params); 
											document.getElementById('winnerAlert').style.display = 'none';
											<?php } 
										?>
										});
								  </script>
                                  <div id="winnerAlert" style="padding:10px; font-size:11px; display:none;" >
                                  <h1 style="font-size:18px; font-weight:bold;">Congratulations: You have just won the following tournament/s</h1>
                                  <?php echo $winnertText; ?>
                                  <p>&nbsp;</p>
                                  <p align="right"><a onclick="postToFeed('<?php echo $winnertFbText ?>'); return false;" title="Share on Facebook"><img src="templates/topbetta/images/FBshare.png" border="0" alt="Share on Facebook" title="Share on Facebook"/></a></p>
								  <?php
								  if(!$tb_user) { ?>
    							  <p id='winnermsg'></p>
                                  <p>&nbsp;</p>                
                                  <table class="tournament-tickets-table">
																<tr valign="middle">
																	<td align="center"><a href="/user/upgrade" style="font-size:14px; font-weight:bold; ba color:#fff; text-decoration:none;">Complete my full registration to allow me to enter paid tournaments</a>
                                                                    </td>
                                                                 </tr>
                                                                </table>
                                  <?php } ?>
                                  </div>
									</div><!-- close tickets tables -->