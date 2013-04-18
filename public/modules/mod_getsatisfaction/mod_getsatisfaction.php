<?php
/**
* @version		$Id$
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('fastpass.fastpass');

$do_login		= JRequest::getBool('gs_login', false);
$start_logout	= JRequest::getBool('fastpass_logout', false);
$do_logout		= JRequest::getBool('gs_logout', false);
$do_redirect	= JRequest::getBool('gs_redirect', false);
$tmpl			= JRequest::getVar('tmpl', false);

//FastPass::$domain = $params->get('community_domain');
	
$loginUser =& JFactory::getUser();

$key 	= $params->get('fastpass_key');
$secret = $params->get('fastpass_secret');

if ($tmpl === false) {
	FastPass::$domain = $params->get('community_domain');
	$fastpass_url = FastPass::url($key, $secret, $loginUser->email, $loginUser->username, $loginUser->id, true, array());
	
	
	$widget = str_replace('[%FAST_PASS_URL]', $fastpass_url, $params->get('widget_js'));

	if ($start_logout) {
		setcookie("fastpass_logout", '', time()-3600);
		
		$pop_under_js =<<<EOF
		<script type="text/javascript">
		function pop_under (theUrl) {
			options = 'top=9999,left=9999,toolbar=no,menubar=no,scrollbars=no,resizable=no,width=1,height=1';   // configuration for pop-under window (standard DOM)
			w=window.open(theUrl,"",options);
			w.blur();
			window.focus();
		}
		pop_under('/user/getsatisfaction/nonssl/logout');
		</script>
EOF;
	}
}

if ($tmpl = 'component') {
	
	if ($do_login) {
		//This is a nasty way to check what community domain should be used.
		//Hopefully get satisfaction can provide ssl for the 'community domain', so we don't need to do this hack.
		FastPass::$domain = $params->get('community_domain');
		$fastpass_script = FastPass::script($key, $secret, $loginUser->email, $loginUser->username, $loginUser->id, false, array('email' => $loginUser->email));
		
		//GSFN.safe_redirect only gets populated when js is run
		echo($fastpass_script);
		
		$reg = "/(http:\\/\\/community.topbetta.com\\/([^\"]*))/";
		if(preg_match($reg, $fastpass_script, $m )) {
			$js_url = $m[0];
			$js_content = file_get_contents($js_url);
			
			if (strpos('GSFN.safe_redirect', $js_content) === false ) {
				FastPass::$domain = 'getsatisfaction.com';
				$fastpass_script = FastPass::script($key, $secret, $loginUser->email, $loginUser->username, $loginUser->id, false, array('email' => $loginUser->email));
			}
		}
		
		echo($fastpass_script);
	}

	if ($do_logout) {
		$fastpass_logout = '<script src="/media/system/js/mootools.js" type="text/javascript"></script>';
		$fastpass_logout .= '<script type="text/javascript" src="http://community.topbetta.com/logout.js"></script> ';
		$fastpass_logout .= '<script type="text/javascript" src="http://getsatisfaction.com/logout.js"></script> ';
		$fastpass_logout .= '<script type="text/javascript">window.close();</script>';
	}
}

require(JModuleHelper::getLayoutPath('mod_getsatisfaction'));
