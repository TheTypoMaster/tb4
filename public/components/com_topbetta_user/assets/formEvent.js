window.addEvent('domready', function(){
	$('country').addEvent('change', function(){
		if($('country').value != 'AU'){
			$('state').value = 'other';
		} 
	});
});