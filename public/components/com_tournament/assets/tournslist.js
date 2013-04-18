<!--
window.addEvent('domready', function() {
	$$('.register_link').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			url = this.href;
			
			params = {
				'width' : 600,
				'height' : 200,
				'dynamic_size' : true,
				'div_to_reset' : 'ticketWrap'
			};
			
			loadLightbox(url, params);
	  	});
	});
});

var confirmToggle = function() {
	if ($('playButt').disabled) {
		$('playButt').disabled = false;
	    $('confirmChk').setStyle('background-position', '0 -28px');
	} else {
		$('playButt').disabled = true;
	    $('confirmChk').setStyle('background-position', '0 0');
	}
};
	
var submitTicket = function(element) {
	element.setAttribute('disabled', true);
	element.form.submit();
};
-->