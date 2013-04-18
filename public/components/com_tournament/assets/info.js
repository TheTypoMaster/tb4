<!--

window.addEvent('domready', function() {
	if(register) {
		$('regoButt').addEvent('click', function(e) {
			url = $('regoButt').getProperty('href');
			new Event(e).stop();
			if(!password_required) {
				params = {
						'width' : 600,
						'height': 200,
						'dynamic_size' : true,
						'div_to_reset' : 'ticketWrap'
				}
				loadLightbox(url, params);
			} else if(detectBrowser() == 'ie') {
				params = {
						'width' : 391,
						'height': 391
				}
				loadLightbox(url, params);
			} else {
				params = {
						'width' : 391,
						'height': 289
				}
				loadLightbox(url, params);
			}
		});
	} else {
		$('regoButt').addEvent('click', function(e) {
			new Event(e).stop();
			if(!user_logged_in) {
				loginAlert();
			}
		});
	}

	if($('jackpotLink')){
		if($$('#jackpotLink').getLast() != null) {
			$('jackpotLink').addEvent('click', function(e) {
				new Event(e).stop();
				url = $('jackpotLink').getProperty('href');
				params = {
						'width' : 600,
						'height': 450
				}
				loadLightbox(url, params);
			});
		}
	}

	if(unregister) {
		$('unregoButt').addEvent('click', function(e) {
			if(!confirm('Are you sure you want to unregister?')) {
				new Event(e).stop();
			}
		});
	} else if($('unregoButt')) {
		$('unregoButt').addEvent('click', function(e) {
			new Event(e).stop();
		});
	}
	/**
	 * Email friend
	 */
	if($('btnEmailFriend')) {
		if($('btnEmailFriend').getProperty('href') != '#'){
			$('btnEmailFriend').addEvent('click', function(e) {
				url = $('btnEmailFriend').getProperty('href');
				new Event(e).stop();

				if(detectBrowser() == 'ie' && detectBrowserVersion()==6) {
					params = {
							'width' : 388,
							'height': 560
					}
					loadLightbox(url, params);
				} else {
					params = {
							'width' : 392,
							'height': 555,
							'dynamic_size' : true
					}
					loadLightbox(url, params);
				}
			});
		}
	}
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
/**
 * Match Password Ajax
 */
function matchTournamentPassword(){

	tournamentPassword 	= $('private-tourn-pass').getProperty('value');
	tournamentId 		= $('tourntament_id').getProperty('value');
	tournamentURL	 	= $('tourntament_url').getProperty('value');
	url = "/index.php?option=com_tournament&task=matchPassword&format=raw&given_password=" + tournamentPassword + "&id=" + tournamentId;

	new Ajax(url, {
		method: 'post',
		onRequest: function() { },
		onComplete: function(response) {
			if(response) {

				params = {
						'width' : 600,
						'height': 200,
						'dynamic_size' : true,
						'div_to_reset' : 'ticketWrap'
				}
				
				loadLightbox(response, params);
			}
			else{
				$('error-txt').style.visibility = "visible";
			}
		}
	}).request();
}
/**
 * Validate the Sledge box form
 */
function validateSledgeForm(){

	sledgeComment = trim($('tournament_sledge').value);
	sledgeComment = sledgeComment.replace('leave comment here...','');

	if(!sledgeComment){
		$('sledge-error').style.display = "block";
		return false;
	}
}
/**
 * Validate the Send Email form
 */
function validateEmailForm(){
	$('error-email').style.visibility = $('error-content').style.visibility = "hidden";
	var errFlag = 0;
	if(!$('tournament_friends_over_18_chk').getProperty('checked')){
		$('error-content').style.visibility = "visible";
		errFlag ++;
	}
	emails = trim($('tournament_private_emails').value);
	emails = emails.replace('Enter email addresses','');
	if(emails == '' && $('previous_tournament').value < 1){
		$('error-email').innerHTML = "Please add email recipients.";
		$('error-email').style.visibility = "visible";
		return false;
	}

	if(emails != ''){
		emails = emails.replace(',', '');
		emails = emails.replace(';', '');

		if(detectBrowser() == 'ie') splitTxt = '\r\n';
		else splitTxt = '\n';

		var arrEmails = emails.split(splitTxt);

		for(i=0; i < arrEmails.length; i++ ){
			if(!isValidEmail(arrEmails[i])){
				$('error-email').innerHTML = "One or more email address is not valid";
				$('error-email').style.visibility = "visible";
				return false;
			}
		}
	}
	if(errFlag > 0) return false;
	else return true;
}
function isValidEmail(str) {
	var pattern=/^([_a-zA-Z0-9-+]+)(\.[_a-zA-Z0-9-+]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
	return (pattern.test(str));
}
function trim(str) {
	var	str = str.replace(/^\s\s*/, ''),
		ws = /\s/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}
function toggleInitialTxt(ctrl, txt, typ){
		ctrlTxt = trim(ctrl.value);
		ctrlTxt = ctrlTxt.replace(txt,'');
		ctrl.value = ctrlTxt;
		if(ctrlTxt == '' && typ > 0) {ctrl.value = txt;}
}
-->