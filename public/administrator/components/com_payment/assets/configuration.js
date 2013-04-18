window.addEvent('domready', function(){
	$('variable_list').addClass('hide');
	$('paypal_item_suggestion').addClass('hide');
	
	$('show_variable').addEvent('click', function() {
		$('variable_list').removeClass('hide');
	});
	
	$('variable_close').addEvent('click', function() {
		$('variable_list').addClass('hide');
	});
	
	$('show_suggestion').addEvent('click', function() {
		$('paypal_item_suggestion').removeClass('hide');
	});
	
	$('paypal_item_suggestion_close').addEvent('click', function() {
		$('paypal_item_suggestion').addClass('hide');
	});
});