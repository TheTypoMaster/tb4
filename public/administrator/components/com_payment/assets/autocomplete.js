window.addEvent('domready', function()
{
	var to = '';
	$('recipient').addEvent('keypress', function(e){
		if(e.keyCode == 13 )
		{
			return false;
		}
	});
	$('recipient').addEvent('keyup', function(e){
		if(e.keyCode == 13 )
		{
			return false;
		}
		
		clearTimeout(to);
		
		to = setTimeout( function () {
		$('form1').send({ update: $('recipient_list') });
		$('recipient_list').removeClass('hide');
		document.getElementById('transaction_recipient').value = document.getElementById('recipient').value;
		}, 1000);
		
		
	});
});

function update_recipient( content )
{
	document.getElementById('recipient').value = content;
	document.getElementById('transaction_recipient').value = content;
	$('recipient_list').addClass('hide');
}

function stopRKey(evt)
{
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

document.onkeypress = stopRKey;
