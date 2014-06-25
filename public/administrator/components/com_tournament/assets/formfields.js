<!--
window.addEvent('domready', function() {
	$('tournament_sport_id').addEvent('change', function(event) {
		$('event_group_id').empty();
		optionElement = new Element('option');
		optionElement.inject($('event_group_id'));
		optionElement.setProperty('value', -1);
		optionElement.appendText('Select an Event Group');
		if (this.value == 1 || this.value == 2 || this.value == 3) {
			$$('.race_only').each(function(e) {
				e.setStyle('display', '');
			});
			$('advanced_settings').setStyle('display', '');
		} else {
			$$('.race_only').each(function(e) {
				e.setStyle('display', 'none');
			});
			$('advanced_settings').setStyle('display', '');
		}
		
		showHideFutureTournamentDate();
	});
	
	$('future_meeting_venue').addEvent('change', function(event) {
		showHideFutureTournamentDate();
	});
	
	if ($('is_racing_sport').getValue()) {
		$$('.race_only').each(function(e) {
			e.setStyle('display', '');
		});
		$('advanced_settings').setStyle('display', '');
	} else {
		$$('.race_only').each(function(e) {
			e.setStyle('display', 'none');
		});
		$('advanced_settings').setStyle('display', '');
	}
	
	showHideFutureTournamentDate();
	
});

function showHideFutureTournamentDate() {
	if ($('future_meeting_venue').value == -1) {
		$('future_meeting_date').setStyle('display', 'none');
	} else {
		$('future_meeting_date').setStyle('display', '');
	}
}
-->