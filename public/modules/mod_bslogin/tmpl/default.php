<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>    
<script type="text/javascript">
  window.addEvent('domready', function() {
	  
	  $('displayMyBalance').addEvent('click', function() {	
		  new Ajax('/index.php?option=com_betting&task=showAllBalanceForTopMenu&format=raw', {
					method: 'post',
					data: {
					},
					onComplete: function(response) {
							$('displayAccountBalance').innerHTML = response;
					}
				}).request();	
		$('displayAccountBalance').style.background = 'none';						
		});
		
		$('displayTournTickets').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showOpenTournamentsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayOpenTournaments').innerHTML = response;
				}
			}).request();
							
		});
		$('open-tournament-link').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showOpenTournamentsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayOpenTournaments').innerHTML = response;
				}
			}).request();
							
		});
		
		$('recent-tournament-link').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showRecentTournamentsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayRecentTournaments').innerHTML = response;
				}
			}).request();
							
		});
		$('displayMyBets').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showUnresultedBetsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayUnresultedBets').innerHTML = response;
				}
			}).request();
							
		});
		$('open-bet-link').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showUnresultedBetsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayUnresultedBets').innerHTML = response;
				}
			}).request();
							
		});
		$('recent-bet-link').addEvent('click', function() {	
			
			new Ajax('/index.php?option=com_betting&task=showRecentBetsForTopMenu&format=raw', {
				method: 'post',
				data: {

				},
				onComplete: function(response) {
						$('displayRecentBets').innerHTML = response;
				}
			}).request();
							
		});
  });
</script>
<?php
if($type == 'logout') :
	?>
	<div class="top-bar">
			<div class="top-bar-container">
					<div class="tourn-tickets-button"><a id="displayTournTickets" href="#" onclick="return false;"<?php echo $ticket_button_class ?>>My Tournaments <span id="button-arrow-tickets">&nbsp;</span></a></div>
					<div id="uc-tourn-tickets">
						<div class="tourn-tickets-buttons">
								<a class="btn-open-tourn active" id="open-tournament-link" href="#" onclick="return false;">Open Tournaments</a>
									<a class="btn-recent-tourn" id="recent-tournament-link" href="#" onclick="return false;">Recent Tournaments</a>
							</div>
							<!-- open tournament ticket -->
							<div class="tournament-tickets-wrap" id="tournament_open">
								<div class="tournament-tickets-title"><span class="tourn-flyout-title">Your Open Tournament Tickets</span><span class="instruction">(Click to view)</span></div>
                                <div id="displayOpenTournaments" >Loading...</div>
							<!-- close tickets tables -->
							</div><!-- close tournament-tickets-wraps -->

								<!-- recent tournaments -->
							<div class="tournament-tickets-wrap" id="tournament_recent" style="display:none">
								<div class="tournament-tickets-title"><span class="tourn-flyout-title">Your Recent Tournament Tickets</span><span class="instruction">(Click to view)</span></div>
									<div id="displayRecentTournaments" >Loading...</div><!-- close tickets tables -->
							</div><!-- close tournament-tickets-wraps -->


							<div class="tournament-bottom-links">
										<a class="view-full-tourn-hist" href="/user/account/tournament-history" onclick="alert('Temporarily Unavailable'); return false;">View full tournament history</a> <a class="tourn-close-link" id="uc-tourn-tickets-close" href="#" onclick="return false;"> <img src="templates/topbetta/images/close-icon-orange.png" border="0" alt="close" title="close"/></a>
										<div class="clear"></div>
							</div>

				</div><!-- close uc-tourn-tickets -->
				
				<!-- uc-my-bets -->
				<div class="my-bets-button"><a id="displayMyBets" href="#" onclick="return false;"<?php echo $bet_button_class; ?>>My Bets <span id="button-arrow-bets">&nbsp;</span></a></div>
					<div id="uc-my-bets">
						<div class="bet-tickets-buttons">
								<a class="btn-open-bet active" id="open-bet-link" href="#" onclick="return false;">Open Bets</a>
								<a class="btn-recent-bet" id="recent-bet-link" href="#" onclick="return false;">Recent Bets</a>
							</div>

							<!-- open bet ticket -->
							<div class="tournament-tickets-wrap" id="bet_open">
								<div class="tournament-tickets-title"><span class="tourn-flyout-title">Your Unresulted Bet Tickets</span><span class="instruction">(Click to view)</span></div>
									<div id="displayUnresultedBets" >Loading...</div><!-- close tickets tables -->
							</div><!-- close tournament-tickets-wraps -->

							<!-- recent bets -->
							<div class="tournament-tickets-wrap" id="bet_recent" style="display:none">
								<div class="tournament-tickets-title"><span class="tourn-flyout-title">Your Recent Bet Tickets</span><span class="instruction">(Click to view)</span></div>
									<div id="displayRecentBets" >Loading...</div><!-- close tickets tables -->
							</div><!-- close tournament-tickets-wraps -->

							<div class="tournament-bottom-links">
								<a class="view-full-tourn-hist" href="/user/account/betting-history">View full betting history</a> <a class="tourn-close-link" id="uc-my-bets-close" href="#" onclick="return false;"> <img src="templates/topbetta/images/close-icon-orange.png" border="0" alt="close" title="close"/></a>
								<div class="clear"></div>
							</div>

				</div><!-- close uc-my-bets -->

					<div class="useracc-button"><a id="displayUserAccount" href="#" onclick="return false;">My Account <span id="button-arrow-blue">&nbsp;</span></a></div>
					<div id="uc-useraccount">

						<div class="useracc-title">
							<form action="/index.php" method="post" name="login" id="logout-form">
								<span class="user-name"><?php echo htmlspecialchars( $user->username . ' (' .  $user->name . ')') ?></span><a href="#" class="user-logout" onclick="return false;" id="logout">logout</a>
								<input type="hidden" name="option" value="com_topbetta_user" />
								<input type="hidden" name="task" value="logout" />
								<input type="hidden" name="return" value="<?php echo htmlspecialchars($return); ?>" />
							</form>
								<div class="clear"></div>
						</div>

						<div class="useracc-links">
							<div class="useracc-links-title">Betting</div>
								<ul>
									<li><a href="/user/account/betting-history"><img src="templates/topbetta/images/icn-accounts_betting-history.png" border="0" alt="Betting History"/>Betting History</a></li>
									<li><a href="/user/account/betting-limits"><img src="templates/topbetta/images/icn-accounts_betting-limit.png" border="0" alt="Betting Limits"/>Betting Limits</a></li>
								</ul>
						</div>
						<div class="useracc-links">
							<div class="useracc-links-title">Banking &amp; Funds</div>
							<ul>
								<li><a href="/user/account/deposit"><img src="templates/topbetta/images/icn-accounts_deposit.png" border="0" alt="Deposit"/>Deposit</a></li>
								<li><a href="/user/account/withdrawal-request"><img src="templates/topbetta/images/icn-accounts_withdrawl.png" border="0" alt="Withdraw"/>Withdraw</a></li>
								<li><a href="/user/account/transactions"><img src="templates/topbetta/images/icn-accounts_account-transaction.png" border="0" alt="Account Transactions"/>Account Transactions</a></li>
							</ul>
						</div>
						<div class="clear"></div>
						<div class="useracc-links">
							<div class="useracc-links-title">Tournaments</div>
							<ul>
									<li><a href="/user/account/tournament-transactions" onclick="alert('Temporarily Unavailable'); return false;" style="opacity:0.5;"><img src="templates/topbetta/images/icn-accounts_tournament-transaction.png" border="0" alt="Tournament Transactions"/>Tournament $ Transactions</a></li>
									<li><a href="/user/account/tournament-history" onclick="alert('Temporarily Unavailable'); return false;" style="opacity:0.5;"><img src="templates/topbetta/images/icn-accounts_tournament-history.png" border="0" alt="Tournament History"/>Tournament History</a></li>
							</ul>
						</div>
						<div class="useracc-links">
							<div class="useracc-links-title">Account</div>
							<ul>
									<li><a href="/user/account/settings"><img src="templates/topbetta/images/icn-accounts_account-settings.png" border="0" alt="Account Settings"/>Account Settings</a></li>
									<li><a href="user/exclude" onclick="return confirm('Clicking ok will block you from accessing the site for a period of 1 week, and log you out. Click cancel to stay.');"><img src="templates/topbetta/images/icn-accounts_exclude-yourself.png" border="0" alt="Exclude Yourself"/>Exclude Yourself</a></li>
									<li><a href="/user/refer-a-friend"><img src="templates/topbetta/images/icn-accounts_refer-friend.png" border="0" alt="Refer a Friend"/>Refer a Friend</a></li>
							</ul>
						</div>
						<div class="clear"></div>
						<div class="useracc-bottom-links">
							<a href="#" id="uc-useraccount-close" class="user-acc-close" onclick="return false;"><img src="templates/topbetta/images/close-icon.png" border="0" alt="close" title="close"/></a>
                        	<div class="clear"></div>
                        </div>
					</div> <!--close uc-useraccount -->
				<div id="displayMyBalance" onmouseover="this.style.cursor = 'pointer';" >
				<div id="displayAccountBalance" class="user-top-acc-info" ><span class="user-top-amount"><strong>My Balances</strong></span></div>
                </div>

			</div><!-- close top-bar-container -->
	</div><!-- close top-bar -->
<?php else : ?>
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var modlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>

	<a class="create-account-button" href="/user/register">Create an Account</a>
	
	<div id="uc-login-inline">

		<div class="fbCustom"><!--<a href="javascript:void(0)" onclick="jfbc.login.login_custom();"><img src="/images/facebook-login-btn.png" style="margin-top: 4px;"></a>--><div style="display:none;">{JFBCLogin}</div></div>

		<form action="/" method="post" name="login" id="login-form" >
			<?php echo $params->get('pretext'); ?>
					<ul style="width: 483px; margin-top: 6px;list-style-type: none; padding-left:495px;"><!-- Float : right removed -->
					<li style="float:left;"><input name="username" id="mod_login_username" type="text" class="input_login" alt="username" size="10" style="background-color: #fff;height:20px; width: 140px;font-size:14px;padding-left:5px;" placeholder="Username" /></li>

					<li style="float:left; margin-left: 10px;"><input type="password" id="mod_login_password" name="passwd" class="input_login" size="10" alt="password" style="background-color: #fff;height:20px; width: 140px;font-size:14px;padding-left:5px;" placeholder="Password" /></li>

					<li style="float:left;"><input type="hidden" name="remember" id="mod_login_remember" class="input_chk" value="yes" alt="Remember Me" style="background-color: #fff;"/></li>
				    <li style="float:left; margin-left: 10px;"><input type="submit" name="Submit" class="loginbutton" value="LOGIN" style="background-color: orange;padding:4px;color:#fff;border:1px solid #fff;border-radius:3px;"/></li>
				    <li style="float:left; margin-left: 10px;">
					  <ul style="list-style-type : none;" >
					    <li><a href="/user/reset" style="color: #555555;font-size: 10px;">Forgot your password?</a></li>
						<li><a href="/user/remind" style="color: #555555;font-size: 10px;">Forgot your username?</a></li>
					  </ul>
				    </li>
				</ul>
			<div class="uc-login-links" style="float:right;">
			<a href="<?php echo JRoute::_( 'user/reset' ); ?>">
			<?php //echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
			</div>

			<?php echo htmlspecialchars($params->get('posttext')); ?>
			<input type="hidden" name="option" value="com_topbetta_user" />
			<input type="hidden" name="task" value="login" /> 
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>			
		</form>
	</div><!-- close uc-login -->
<?php endif; ?>
