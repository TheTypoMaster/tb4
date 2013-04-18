window.addEvent('domready', function(){
	triggerEmailForm();
	
	$('send_email').addEvent('click', function() {
		triggerEmailForm();
	});	

	$('radio_approve_request_yes').addEvent('click', function() {
		populateContent('approval');
	});

	$('radio_approve_request_no').addEvent('click', function() {
		populateContent('denial');
	});
	
	
	$('approval_template').addEvent('click', function() {
		populateContent('approval');
	});

	$('denial_template').addEvent('click', function() {
		populateContent('denial')
	});
});

function triggerEmailForm()
{
	var send_email_ticked = $('send_email').getValue();

	if( send_email_ticked )
	{
		$('notifying_email').removeClass('hide');
	}
	else
	{
		$('notifying_email').addClass('hide');
	}
}

function populateContent( type )
{
	if( 'approval' == type )
	{
		var email_subject = $('approval_subject').getText().trim();
		var email_body = $('approval_body').getText().trim();
	
		$('notifying_email_subject').value = email_subject;
		$('notifying_email_body').value = email_body;
	}
	
	if( 'denial' == type )
	{
		var email_subject = $('denial_subject').getText().trim();
		var email_body = $('denial_body').getText().trim();

		$('notifying_email_subject').value = email_subject;
		$('notifying_email_body').value = email_body;
	}
}