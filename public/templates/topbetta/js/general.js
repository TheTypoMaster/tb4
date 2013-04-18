<!--
window.addEvent('domready', function() {
	
	if($('private-tourn-search')) {
		initialisePrivateTournamentSearchBox();
		$('private-tourn-search').addEvent('focus', function() {
									 if('TOURNAMENT CODE' == this.getProperty('value')) {
									 	this.setProperty('value', '')
									 		.setStyles({
									 			'color': '#000',
									 			'text-align': 'left'
									 		});
									 }
								 })
								 .addEvent('blur', function() {
									 if('' == this.getProperty('value')) {
										 initialisePrivateTournamentSearchBox();
									 }
								 });
	}

	if($('uc-login')) {
		var params = {
			'trigger': 'a#displayLogin',
			'trigger_content': 'div#uc-login',
			'padding_top': '10px',
			'padding_bottom': '10px'
		};
		topheaderAccordion(params);

		$('login-close').addEvent('click', function() {
			$('uc-login').setStyle('display', 'none');
			$('button-arrow').setStyle('background-position', '0px 0px');
		});

		if($('already_member')) {
			$('already_member').addEvent('click', function() {
				$('button-arrow').setStyle('background-position', '0px -10px');
				$('uc-login').setStyles({
					'padding-top': '10px',
					'border-top': '4px solid rgb(236, 124, 16)',
					'padding-bottom': '10px',
					'border-bottom': '4px solid rgb(236, 124, 16)',
					overflow: 'hidden',
					visibility: 'visible',
					opacity: 1,
					height: '252px',
					display: 'block'
				});
			});
		}
	}
	
	if($('uc-useraccount')) {
		var params = {
			'trigger': 'a#displayUserAccount',
			'trigger_content': 'div#uc-useraccount',
			'arrow': 'button-arrow-blue',
			'color': '#01A9EA',
			'height': '350px',
			'on_active_close' : [['uc-my-bets', 'button-arrow-bets'],['uc-tourn-tickets', 'button-arrow-tickets']]
		};
		topheaderAccordion(params);
		$('uc-useraccount-close').addEvent('click', function() {
			$('uc-useraccount').setStyle('display', 'none');
			$('button-arrow-blue').setStyle('background-position', '0px 0px');
		});

		$('recent-tournament-link').addEvent('click', function() {
			$('open-tournament-link').removeClass('active');
			$('recent-tournament-link').addClass('active');
			$('tournament_open').setStyle('display', 'none');
			$('tournament_recent').setStyle('display', 'block');
		});

		$('open-tournament-link').addEvent('click', function() {
			$('open-tournament-link').addClass('active');
			$('recent-tournament-link').removeClass('active');
			$('tournament_open').setStyle('display', 'block');
			$('tournament_recent').setStyle('display', 'none');
		});

		tournamentLinks('open');
		tournamentLinks('recent');
		
		$('recent-bet-link').addEvent('click', function() {
			$('open-bet-link').removeClass('active');
			$('recent-bet-link').addClass('active');
			$('bet_open').setStyle('display', 'none');
			$('bet_recent').setStyle('display', 'block');
		});

		$('open-bet-link').addEvent('click', function() {
			$('open-bet-link').addClass('active');
			$('recent-bet-link').removeClass('active');
			$('bet_open').setStyle('display', 'block');
			$('bet_recent').setStyle('display', 'none');
		});
		
		betLinks('open');
		betLinks('recent');
		
		$('logout').addEvent('click', function() {
			$('logout-form').submit();
		});
	}

	if($('uc-tourn-tickets')) {
		var params = {
			'trigger': 'a#displayTournTickets',
			'trigger_content': 'div#uc-tourn-tickets',
			'arrow': 'button-arrow-tickets',
			'height': 449,
			'on_active_close' : [['uc-my-bets', 'button-arrow-bets'],['uc-useraccount', 'button-arrow-blue']]
		};
		topheaderAccordion(params);
		$('uc-tourn-tickets-close').addEvent('click', function() {
			closePopupList('uc-tourn-tickets', 'button-arrow-tickets');
		});

		$$('.ticket-pending').each(function(e) {
			e.setProperty('title','You must bet all your initial BettaBucks to qualify for a prize in this tournament.');
			e.setProperty('class','ticket-pending tickettipz');
		});

		var Tips4 = new Tips($$('.ticket-pending'), {
			className: 'ticket'
		});
	}
	
	if($('uc-my-bets')) {
		var params = {
			'trigger': 'a#displayMyBets',
			'trigger_content': 'div#uc-my-bets',
			'arrow': 'button-arrow-bets',
			'height': 449,
			'on_active_close' : [['uc-tourn-tickets', 'button-arrow-tickets'],['uc-useraccount', 'button-arrow-blue']]
		};
		topheaderAccordion(params);
		$('uc-my-bets-close').addEvent('click', function() {
			closePopupList('uc-my-bets', 'button-arrow-bets');
		});

		$$('.ticket-pending').each(function(e) {
			e.setProperty('title','You must bet all your initial BettaBucks to qualify for a prize in this tournament.');
			e.setProperty('class','ticket-pending tickettipz');
		});

		var Tips4 = new Tips($$('.ticket-pending'), {
			className: 'ticket'
		});
	}
	
	if($('next_to_jump_button')) {
		var next_to_jump = new Accordion('div#next_to_jump_button', 'div#next_to_jump', {
			display: -1,
			opacity: true,
			alwaysHide: true,
			duration: 250
		});
		$('next_to_jump').setStyles({
			'display':'inline'
		});
		
		if (detectBrowser() == 'ie') {
			$('next_to_jump').setStyles({
				'overflow': 'visible'
			});
		}
	}
	
	$$('.guest_link').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			loginAlert();
	  	});
	});
});

function topheaderAccordion(params) {
	var trigger = params.trigger;
	var trigger_content = params.trigger_content;
	var arrow = $pick(params.arrow, 'button-arrow');
	var color = $pick(params.color, '#EC7C10');
	var height = $pick(params.height, '252');
	var padding_top = $pick(params.padding_top, '0px');
	var padding_bottom = $pick(params.padding_bottom, '0px');
	var on_active_close = $pick(params.on_active_close, null);

	var menu = new Accordion(trigger, trigger_content, {
		display: 61,
		fixedHeight: height,
		opacity: true,
		alwaysHide: true,
		duration: 250,
		onActive: function(toggler, element) {
			element.setStyles({
				'border': '4px solid '+ color,
				 'padding-top': padding_top,
				 'padding-bottom': padding_bottom,
				 'display': 'block'
			});
			$(arrow).setStyle('background-position', '0px -10px');
			
			if(on_active_close) {
				for (var i=0, len=on_active_close.length; i<len; ++i) {
					closePopupList(on_active_close[i][0], on_active_close[i][1]);
				}
			}
		},
		onBackground: function(toggler, element) {
			element.setStyles({
				'border-bottom': 'medium none',
				'border-top': 'medium none',
				'padding-top': '0',
				'padding-bottom': '0',
				'display': 'none'
			});
			$(arrow).setStyle('background-position', '0 0');
		}
		});

};

function tournamentLinks(type) {

	$$('#tournament_'+type+' .tournament-tickets-table').each(function(el) {
		el.addEvent('click', function(e) {
			var ticket_tournament = 'ticket_tournament_' + el.id;
			var tournament_id = $(ticket_tournament).getText();
			var tournament_type = el.getProperty('ref');

			if(type == 'open') {
				window.location.href = '/tournament/' + tournament_type + '/game/' + tournament_id;
			}
			if(type == 'recent') {
				window.location.href = '/tournament/details/' + tournament_id;
			}
		});

		el.addEvent('mouseenter', function(e) {
			el.setStyle('cursor', 'pointer');
		});
	});
}

function betLinks(type) {
	$$('#bet_' +type+' .tournament-tickets-table').each(function(el) {
		el.addEvent('click', function(e) {
			var meeting_id = $(el.id + '_meeting').getText();
			var race_number = $(el.id + '_race').getText();
			
			window.location.href =  '/betting/racing/meeting/' + meeting_id + '/' + race_number;
		});
	
		el.addEvent('mouseenter', function(e) {
			el.setStyle('cursor', 'pointer');
		});
	});
}

function closePopupList(div, arrow) {
	$(div).setStyle('display', 'none');
	$(arrow).setStyle('background-position', '0px 0px');
}

function initialisePrivateTournamentSearchBox() {
	$('private-tourn-search').setProperty('value', 'TOURNAMENT CODE')
							 .setStyles({
								 'color': 'gray',
								 'text-align': 'center' 
							 });
}

/**
 * Broswer & version detection with Javascript
 */
function detectBrowser(){
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) return 'ie';
	else if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)) return 'ff';
	else if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)) return 'op';
	else if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) return 'ch';
}
function detectBrowserVersion(){
	var version = new Number(RegExp.$1);
	return version;
}

function gameAccordion(type) {
	var toggler	= 'a#' + type;
	var element	= 'div#' + type + '_content';
	var arrow	= type + '_arrow';

	var accordion = new Accordion(toggler, element, {
		show: 0,
		opacity: true,
		alwaysHide: true,
		onActive: function(toggler, element){
			$(arrow).removeClass('arrow-down');
			$(arrow).addClass('arrow-up');
		},
		onBackground: function(toggler, element){
			$(arrow).removeClass('arrow-up');
			$(arrow).addClass('arrow-down');
		}
	});
}

function printBets(title, content_id) {
	if ($(content_id)) {
		content = $(content_id).innerHTML;
		var pwin=window.open('','content', 'width=966, height=300');
		
		new Ajax('/index.php?option=com_betting&task=printbet&format=raw', {
			method: 'post',
			data: {
					'title'		: title.capitalize(),
					'content'	: content
			},
			onComplete: function(response) {
					pwin.document.open();
					pwin.document.write(response);
					pwin.document.close();
			}
		}).request();

	}
}

function loginAlert(e) {
	
	SqueezeBox.initialize({});
	
	url = this.href + '#loginAlert';
	params = {'width' : 410,
			//'height' : 200,
			//'dynamic_size' : true,
			//'div_to_reset' : 'winnerAlert'
			};
	document.getElementById('loginAlert').style.display = 'block';
	loadLightbox(url, params);
	document.getElementById('loginAlert').style.display = 'none';
	//alert("You are not logged in to TopBetta.\n Please click the \"Login | Join\" button in the upper-right corner of the site to login or create an account.");
}
-->
