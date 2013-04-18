<!--
window.addEvent('domready', function() {

	if ($('has_limit').getAttribute('checked') == 'checked') {
		enableBetLimitInput();
	} else {
		disableBetLimitInput();
	}
	
	 $$('.limit_radio').each(function(el) {
		 el.addEvent('click', function(e) {
			 if (this.getAttribute('id') == 'no_limit') {
				 disableBetLimitInput();
			 }
			 if (this.getAttribute('id') == 'has_limit') {
				 enableBetLimitInput();
				 $('bet_limit').focus();
			 }
		 });
	 });
	
});

function disableBetLimitInput() {
	$('bet_limit').setAttribute('disabled', 'disabled');
	$('bet_limit').setStyle('background-color', '#D8D8D8');
}

function enableBetLimitInput() {
	$('bet_limit').removeAttribute('disabled');
	$('bet_limit').setStyle('background-color', '#F1F6FF');
}
-->