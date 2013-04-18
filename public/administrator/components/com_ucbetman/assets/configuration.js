window.addEvent('domready', function(){
	$('variable_list').addClass('hide');
	
	$('show_variable').addEvent('click', function() {
		$('variable_list').removeClass('hide');
	});
	
	$('variable_close').addEvent('click', function() {
		$('variable_list').addClass('hide');
	});
});