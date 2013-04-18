<!--
window.addEvent('domready', function() {
	$('competition_option').addEvent('change',function() {
		updateEventList();
	});
	
	$('tournament_type_option').addEvent('change',function() {
		updateEventList();
	});
});

function updateEventList()
{
	var competition_id = $('competition_option').getProperty('value');
	var jackpot = $('tournament_type_option').getProperty('value');
	var url = '/index.php?option=com_tournament&controller=tournamentracing&task=list_tournaments&format=raw';
	
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