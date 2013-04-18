<!--
window.addEvent('domready', function() {
	$('sport_option').addEvent('change',function() {
		var sport_id = $('sport_option').getProperty('value');
		loadOptions('/index.php?option=com_tournament&task=ajaxcall&type=competition&sport_id='+sport_id, 'competition_option', 'Select Competition ...', 'updateEventList();');
	});
	
	$('competition_option').addEvent('change',function() {
		var competition_id = $('competition_option').getProperty('value');
		loadOptions('/index.php?option=com_tournament&task=ajaxcall&type=sport&competition_id='+competition_id, 'sport_option', 'Select Sport ...', 'updateEventList();');
	});
	
	$('tournament_type_option').addEvent('change',function() {
		updateEventList();
	});
});

function updateEventList() {

	var sport_id = $('sport_option').getProperty('value');
	var competition_id = $('competition_option').getProperty('value');
	var jackpot = $('tournament_type_option').getProperty('value');
	var url = '/index.php?option=com_tournament&controller=tournamentsportevent&task=list_tournaments&format=raw';
	
	if(sport_id) {
		url = url + '&sport_id='+sport_id;
	}
	if(competition_id) {
		url = url + '&competition_id='+competition_id;
	}
	if(jackpot == 'cash') {
		url = url + '&jackpot=0';
	}
	if(jackpot == 'jackpot') {
		url = url + '&jackpot=1';
	}
	
	new Ajax(url, {
		method: 'get',
		onRequest: function() { },
		update: $('event_list'),
		evalScripts: true
	}).request();
}

-->