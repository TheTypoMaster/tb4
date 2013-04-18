<!--
window.addEvent('domready', function() {
	$('confirmBets').addEvent('click', function(e){
		this.disabled = true;
		new Event(e).stop;
		
		$('atpBetForm').send({
			onRequest: function() {
				alert('requesting');
			},
			onSuccess: function() {
				alert('success');
			}
		});
	});
});
-->