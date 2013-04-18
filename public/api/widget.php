<?php

/**
 * TopBetta API Widget
 *
 * This has been put together very quickly to demonstrate the ability
 * of what a widget can achieve. It needs to be cleaned up a lot before
 * production use.
 *
 * Author: mic@topbetta.com
 */
error_reporting(0);

/* BootStrap the Joomla core */
include '../api-bootstrap.php';

/* can't continue without Joomla core */
defined('_JEXEC') or die('Restricted access');

/* include all our req classes */
include 'classes/user.php';
include 'classes/betting.php';

// grab our GET paramaters from the URL
$iframe = array();
$iframe['tourn_id'] = 43562;
$iframe['widget'] = $_GET['widget'];
$iframe['width'] = $_GET['width'];
$iframe['height'] = $_GET['height'];
$iframe['ext_css'] = ($_GET['css']) ? $_GET['css'] : FALSE;
//$iframe['redirect_url'] = $_GET['redirect_url'];
$iframe['redirect_url'] = "https://www.topbetta.com.au/tournament/details/".$iframe['tourn_id'];
$iframe['postback_url'] = $_GET['postback_url'];
$iframe['postback_field'] = $_GET['postback_field'];
$iframe['transaction_id'] = $_GET['transaction_id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>TopBetta Widget</title>
<?php if ($iframe['ext_css']) : ?>
<link href="<?php echo $iframe['ext_css']; ?>" rel="stylesheet" type="text/css">	
<?php endif ?>
</head>

<body>	
<div id="widget_wrapper">

<?
//echo "IFRAME<pre>";
//var_dump($iframe);
//echo "</pre>";

switch($iframe['widget']) {

	case 'login' :
		loginWidget($iframe);
		break;

	case 'signup' :
		signupWidget($iframe);
		break;

	case 'logincheck' :
		loginCheck();
		break;
}

function loginCheck() {
	$user_login = new Api_User();
	var_dump($user_login -> getUserDetails(TRUE));
}

function loginWidget($iframe) {
	// Output a plain form that will be styled via the ext_css file
	$login = FALSE;
	$logged_in = FALSE;
	if ($_POST) {
		// first make sure they are not already logged in
		$user_login = new Api_User();
		$login_check = $user_login -> getUserDetails(TRUE);

		if ($login_check['status'] == 200) {
			$logged_in = TRUE;
		}
		if (!$logged_in) {
			$login = $user_login -> doUserLoginExternal(TRUE);
			if ($login['status'] == 200) {
				$logged_in = TRUE;
			}
		}
		if ($logged_in) {
			//purchase the tournament ticket
			$ticket = new Api_Betting();
			$ticket_status = $ticket->saveTournamentTicket(TRUE, $iframe['tourn_id']);
			echo "<script>";
			//echo "if (parent.frames.length > 0) {";
			echo "top.location.replace(\"" . $iframe['redirect_url'] . "\")";
			//echo "}";
			echo "</script>";
			echo '<a href="https://www.topbetta.com.au/" target="_top">Take me to TopBetta</a>';
		}
	}
	if (!$logged_in) {
		echo '
		<form action="" id="loginForm" class="crud" enctype="multipart/form-data" method="post" accept-charset="utf-8">
				
		    <div class="form_inputs">
		
		    <ul>
		        <li class="">
		            <label for="username">Username</label>
		            <div class="input"><input type="text" name="username" value="" class="width-15">
		</div>
		        </li>	
		        
		        <li class="even">
		            <label for="password">Password</label>
		            <div class="input"><input type="password" name="password" value="" class="width-15">
		</div>
		        </li>            
		        
		        <li class="even">
		            <input type="submit" id="submit" value="Login">
		        </li>
		       
		    </ul>			
		    </div>
			</form>	
		';
		if ($login['status'] != 200) {
			echo "<div class=\"error\">{$login['error_msg']}</div>";
		}
	}
}

function signupWidget($iframe) {
	$logged_in = FALSE;
	if ($_POST) {
		$user_register_basic = new Api_User();
		$register = $user_register_basic -> doUserRegisterBasicExternal(TRUE);
		if ($register['status'] == 200) {
			//do post back to url if required
			if ($iframe['transaction_id']) {
				postBacktoURL("http://tm.adsmetric.com/aff_lsr?transaction_id=" . $iframe['transaction_id'] . "&adv_sub=" . $_POST['email']);
			}

			// first make sure they are not already logged in
			$user_login = new Api_User();
			$login_check = $user_login -> getUserDetails(TRUE);

			if ($login_check['status'] == 200) {
				$logged_in = TRUE;
			}
			if (!$logged_in) {
				$login_details = array('username' => $register['username'], 'password' => $_POST['password']);
				$login = $user_register_basic -> doUserLoginExternal(TRUE, $login_details);
				if ($login['status'] == 200) {
					$logged_in = TRUE;
				}
			}
			if ($logged_in) {
				//purchase the tournament ticket
				$ticket = new Api_Betting();
				$ticket_status = $ticket->saveTournamentTicket(TRUE, $iframe['tourn_id']);				
				echo "<script>";
				//echo "if (parent.frames.length > 0) {";
				echo "top.location.replace(\"" . $iframe['redirect_url'] . "\")";
				//echo "}";
				echo "</script>";
				echo '<a href="https://www.topbetta.com.au/" target="_top">Take me to TopBetta</a>';
			}

		}
	}
	if (!$logged_in) {
		echo '
		<form action="" id="regForm" class="crud" enctype="multipart/form-data" method="post" accept-charset="utf-8">
				
		    <div class="form_inputs">
		
		    <ul>
		        <li class="">
		            <label for="name">First Name</label>
		            <div class="input"><input type="text" name="first_name" value="'.$_POST['first_name'].'" class="width-15">
		</div>
		        </li>
		        
		        <li class="even">
		            <label for="name">Last Name</label>
		            <div class="input"><input type="text" name="last_name" value="'.$_POST['last_name'].'" class="width-15">
		</div>
		        </li>
		
		        <li class="">
		            <label for="name">Email</label>
		            <div class="input"><input type="text" name="email" value="'.$_POST['email'].'" class="width-15">
		</div>
		        </li>	
		        
		        <li class="even">
		            <label for="name">Password</label>
		            <div class="input"><input type="password" name="password" value="" class="width-15">
		</div>
		        </li>     
		        
		        <li class="">
		            <label for="name">Mobile</label>
		            <div class="input"><input type="text" name="mobile" value="'.$_POST['mobile'].'" class="width-15">
		</div>
		        </li>    
		        
                <li class="even">
                <label class="marketing-login-agree" style="text-align:left;" for="terms"><input class="chk" id="terms" name="terms" type="checkbox"> I am over 18 and agree to the <a href="/terms-and-conditions" target="_blank">terms and conditions</a></label><br>
                <label class="marketing-login-agree" style="text-align:left;"><input class="quick-signup-check " type="checkbox" name="optbox" checked="checked"> I agree to receive marketing messages from TopBetta</label><br>
                </li>				
				   
		        
		        <li class="join-now-btn">
		            <input type="submit" id="submit" value="Join now for Free">
		        </li>
		       
		    </ul>
		    
		    
		    
		    </div>
				
				
			</form>	
		';
		if ($register['status'] != 200) {
			echo "<div class=\"error\">{$register['error_msg']}</div>";
		}
	}
}

function postBacktoURL($url) {
	$handle = fopen($url, "r");
}
?>
</div>
</body>
</html>
