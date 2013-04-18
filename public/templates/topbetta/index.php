<?php
/**
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 *  @copyright	Copyright (C) 2005 - 2013 TopBetta. All rights reserved.
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');

// check if joomla is on the homepage
$menu = & JSite::getMenu();
if ($menu->getActive() == $menu->getDefault()) {
	$onfront = 1;
}


$exclude = array(
	'/includes/js/joomla.javascript.js',
	'/media/system/js/mootools.js'
);

foreach($exclude as $path) {
	if(isset($this->_scripts[$path])) {
		unset($this->_scripts[$path]);
	}
}


$config	=& JComponentHelper::getParams( 'com_tournament' );
$header_banner = $config->get('header_banner');
$version = '3.2';

jimport('mobileactive.client.geoip');
$show_number = false;
try{
	$client_geoip = new ClientGeoIP($_SERVER['REMOTE_ADDR']);
	
	if($client_geoip->getCountryCode() == 'AU'){
		$show_number = true;
	}
}
catch(Exception $e){
	trigger_error("Problem with GeoIP client ['{$e->getMessage()}']");
}
$config =& JFactory::getConfig();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
  <!-- Mimic Internet Explorer 8 -->
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" >
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="index, follow" />
  <meta name="keywords" content="<?php print $this->_metaTags['standard']['keywords']; ?>" />
  <meta name="description" content="<?php print $this->description; ?>" />
  <!--OPEN GRAPH-->
  <meta property="og:title" content="<?php echo $this->title; ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:image" content="<?php echo JURI::base() ?>templates/topbetta/images/fb_topbetta.jpg" />
  <meta property="fb:admins" content="839809446,1647396042,509344549" />
  <meta property="og:site_name" content="TopBetta" />
  <meta property="og:description" content="Licensed Online Sports Tournament Betting - for 18+ users only" />

  <title><?php echo $this->title; ?></title>
  <link href="/templates/<?php echo $this->template ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/template.css?v=<?php echo $version; ?>" type="text/css" />
  <link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/fbpopup.css" type="text/css" />
  <?php foreach($this->_styleSheets as $path => $data) { ?>
  <link rel="stylesheet" href="<?php echo $path; ?>?v=<?php echo $version; ?>" type="<?php echo $data['mime'];?>" />
  <?php }?>
  <?php if($onfront){ ?>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400' rel='stylesheet' type='text/css'>
  <?php } ?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript" src="templates/<?php echo $this->template ?>/js/jquery.reveal.js"></script>
  <?php 
	$session =& JFactory::getSession();
	$user =& JFactory::getUser();
	$LoggedInFromFb = $session->get( 'LoggedInFromFb' );
	
	//MC - quick hack for basic user upgrade popup
	require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
    $tb_model = new TopbettaUserModelTopbettaUser();	
	//if(isset($LoggedInFromFb) && $user->isTopBetta == 0 ){
		if(isset($LoggedInFromFb) && !$tb_model->isTopbettaUser($user->id) ){

		  $session->clear('LoggedInFromFb');
		  ?> 
		  <script type='text/javascript'>
			  $.noConflict();
			  ( function($) {
				$(document).ready(function(){
			        $('#modal').reveal({ 
						animation: 'fade',				
						animationspeed: 600,                       
						closeonbackgroundclick: true,              
						dismissmodalclass: 'close'   
					});
				
				});
			   } ) ( jQuery );
		 </script>
	 <?php       
		 
	}

 ?> 
  <script type="text/javascript" src="/includes/js/javascript.js"></script>
  <script type="text/javascript" src="/media/system/js/mootools.js"></script>
  <script type="text/javascript" src="/templates/topbetta/js/general.js"></script>
  <?php foreach($this->_scripts as $path => $type) { ?>
  <script type="<?php print $type; ?>" src="<?php print $path; ?>?v=<?php echo $version; ?>"></script>
  <?php } ?>
  <?php foreach($this->_script as $type => $data) { ?>
  <script type="<?php echo $type; ?>">
  <?php echo $data . "\n"; ?>
  </script> 
  <?php } ?>
  <script type="text/javascript">
	jfbc.login.logout_facebook = false;
	jfbc.base = 'https://www.topbetta.com/';
	jfbc.return_url = "<?php echo base64_encode(JURI::current()); ?>";
	jfbc.login.scope = 'email,publish_stream';
	jfbc.login.show_modal = '0';
  </script>   
  <script type="text/javascript" src="https://connect.facebook.net/en_US/all.js#xfbml=1"></script>
</head>

<body>
<div class="top-bar">
    <div class="top-bar-container">
    	<jdoc:include type="modules" name="login" style="none" />
    </div><!-- close top-bar-container -->
</div><!-- close top-bar -->
<div class="top-bar-shadow"></div>

<!-- main container start -->

<?php if($onfront){ ?>
<style>body{ background: #222222; }</style>
<div id="newHP">
	<jdoc:include type="message" />
	<a href="/"><div id="HPlogo"></div></a>
	<div class="HPbuttWrap">
		<a href="/tournament/racing">
			<div id="HPbutt1" class="HPbuttLeft">
				<span class="HPbuttLabel HPbuttTop">racing tournaments</span>
			</div>
		</a>
		<a href="/betting/racing">
			<div id="HPbutt2" class="HPbuttCntr">
				<span class="HPbuttLabel HPbuttTop">todays racing</span>
			</div>
		</a>
		<a href="/tournament/sports">
			<div id="HPbutt3" class="HPbuttRight">
				<span class="HPbuttLabel HPbuttTop">sports tournaments</span>
			</div>
		</a>
		<a href="/blog/" target="_blank">
			<div id="HPbutt4" class="HPbuttLeft">
				<span class="HPbuttLabel HPbuttBot">topbetta blog</span>
			</div>
		</a>
		<a>
			<div id="HPbutt5" class="HPbuttCntr">
				<span class="HPbuttLabelOff HPbuttBot">sports betting</span>
			</div>
		</a>
		<a href="https://www.toptippa.com.au/topbetta" target="_blank">
			<div id="HPbutt6" class="HPbuttRight">
				<span class="HPbuttLabel HPbuttBot">toptippa</span>
			</div>
		</a>
	</div>
	<div class="clear"></div>
</div>

<?php } else { ?>
<div id="fixed">
	<div id="header">
		<div class="logo"><a href="/">TopBetta - Licenced Betting Operator</a></div>
	    <div class="header-ad"><?php echo $header_banner ?></div>
	    <div class="header-social-links">
	    	<span class="header-links-social-title">Follow us:</span><br/>
	        <a href="http://www.facebook.com/pages/TopBetta/108278129245371" target="_blank"><img src="templates/topbetta/images/btn_facebook.gif" border="0" alt="Facebook"/></a>
	        <a href="http://twitter.com/topbetta" target="_blank"><img src="templates/topbetta/images/btn_twitter.gif" border="0" alt="Twitter"/></a>
	    </div><!-- close header-social-links -->
	    <div class="header-refer-friend"><a href="/user/refer-a-friend"><img src="templates/topbetta/images/btn_refer-a-friend.gif" border="0" alt="Refer a Friend"/></a></div>
	</div><!-- close header -->
	
	<div id="menu">
	 <div class="moduletable_menu">
	    <jdoc:include type="modules" name="menu" style="xhtml" />
	 </div>
	 <?php if($show_number): ?>
	 <div class="menu-contact"><a href="/contact-us"><img src="templates/topbetta/images/btn_contact-number.gif" border="0" alt="Contact Us: 1300 832 645"/></a></div>
	<?php else: ?>
	<div class="menu-contact"><a href="/contact-us"><img src="templates/topbetta/images/btn_contact.png" border="0" alt="Contact Us"/></a></div>
	<?php endif;?>
	</div><!-- close menu -->
	
	<!-- main Content area start -->
	<div class="content-wrap">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</div><!-- close content-wrap -->
</div><!-- fixed -->
<div class="container-shadow"></div>
<?php } ?>


<div class="global-footer">
	<div class="global-footer-links">
		<div id="social-networking">
			<table>
				<tr>
					<td class="twitter">
		              <div id="custom-tweet-button">
		                <a href="http://twitter.com/share?url=<?php echo urlencode(JURI::base()) ?>&text=<?php echo urlencode('Check out TopBetta.com') ?>" onClick="window.open('http://twitter.com/share?url=<?php echo urlencode(JURI::base()) ?>&text=<?php echo urlencode('Check out TopBetta.com') ?>', '','toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=500,height=500');return false;" target="_blank">Tweet</a>
		              </div>
					</td>
				</tr>
			</table>
    	</div><!-- close social-networking -->
		<div class="partner-logos">
			<span class="partner-links-title">Approved Betting Partner of:</span>
			<span class="footer-icon"><img src="templates/topbetta/images/logo-afl.gif" border="0" alt="AFL"/></span>
			<span class="footer-icon"><img src="templates/topbetta/images/logo-nrl.gif" border="0" alt="NRL"/></span>
			<span class="footer-icon"><img src="templates/topbetta/images/logo-aru.gif" border="0" alt="ARU"/></span>
		</div>
		<div class="footer-icons">
	    	<span class="footer-icon"><img src="templates/topbetta/images/footer-payment-icons.gif" border="0" alt=""/></span>
	        <span class="footer-icon"><img src="templates/topbetta/images/must-be-18.gif" border="0" alt="You Must be 18 +"/></span>
	        <span class="footer-icon"><img src="templates/topbetta/images/footer-secure-icon.png" border="0" alt="Secure Site"/></span>
	    </div>
	</div><!-- close global-footer-links -->

	<div class="footer-nav">
		<jdoc:include type="modules" name="credits" style="xhtml"/>
	</div><!-- close footer-nav -->

	<div class="footer-copyright">Licensed Australian Bookmaker. TopBetta is a fully owned & operated company within Australia.</div>
	<div class="footer-copyright">Copyright &copy; <?php echo date('Y') ?> TopBetta Pty Ltd. All rights reserved.</div>
	<div class="footer-copyright">All times shown on this website are in <?php echo $config->getValue('config.time_zone_long'); ?></div>

	<div class="footer-info">
		<div id="footer-seo-link">
  		<a href="/betting/racing">Horse Racing Betting,</a>
  		<a href="/betting/racing">Race Betting,</a>
  		<a href="/tournament/racing">Racing Tournament Betting,</a>
  		<a href="/tournament/sports/4">AFL Tournament Betting,</a>
  		<a href="/tournament/sports/5">NRL Tournament Betting,</a>
  		<a href="/tournament/sports/7">Football Tournament Betting,</a>
  		<a href="/tournament/sports/6">Rugby Union Tournament Betting</a><br>
  		<a href="http://www.horseracing.com.au/races/melbourne-cup/">Melbourne Cup 2013</a>
  		<a href="http://www.races.com.au/melbourne-cup/">Melbourne Cup</a>
  		</div>
	</div>
</div><!-- close global-footer -->

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8891235-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<jdoc:include type="modules" name="getsatisfaction" style="none" />


<?php 
$fblogin = $_GET['fblogin'];
if ($fblogin == true) : ?>
<div id="fb-login">
	<div id="fb-content">
		<img src="/images/melbourne_cup.png" style="margin: 30px 0px 30px 0px;">
		<div>
		      <a href="javascript:void(0)" onclick="jfbc.login.login_custom();"><img src="/images/facebook-login-large.png" style="margin-top: 20px;"></a>
	    </div>
	</div>
</div>
  <script type='text/javascript'>
	  ( function($) {
		$(document).ready(function(){
	        $('#fb-login').reveal({ 
				animation: 'fade',				
				animationspeed: 600,                       
				closeonbackgroundclick: true,              
				dismissmodalclass: 'close'   
			});
		
		});
	   } ) ( jQuery );
 </script>
 <?php endif; ?>
<!-- Modal popup for facebook login -->
<div id="modal">
	<div id="content">
        
		<div>
	    
		      <a href="#" class="button green close">PLAY NOW - For Free</a>
              <p>You can enter all of the FREE tournaments with a TopBetta basic account.</p>
	    </div>
		<div>

		      <a href="/user/upgrade" class="button green close">WIN MONEY</a>
			  <p>If you would like to win money or enter paid tournaments, you just need to fill out a few more details first.</p>
        </div>
		<img style="background-color:#2A2A2A;" src="/templates/topbetta/images/must-be-18.gif" alt="Must be 18+" />
	</div>
</div>
<!-- for login alert -->
<div id="loginAlert" style="display:none; padding:10px; background:#CCC; text-align:center;" >
	<p style="font-size:11px;">You are not logged in to TopBetta.</p> 
	<!--
	<p><a href="javascript:void(0)" onclick="jfbc.login.login_custom();"><img src="/images/facebook-login-btn.png" style="padding-top:5px;" ></a></p>
	<p>OR</p> 
	-->

	<?php // TODO: This should be moved out of the main template? ?>
	<form action="/" method="post" name="login" > 
	<input name="username" id="mod_login_username" type="text" class="input_login" alt="username" size="10" style="background-color: #fff;height:20px; width: 140px;font-size:14px;padding-left:5px;" placeholder="Username">
	<input type="password" id="mod_login_password" name="passwd" class="input_login" size="10" alt="password" style="background-color: #fff;height:20px; width: 140px;font-size:14px;padding-left:5px;" placeholder="Password">
	<input type="hidden" name="remember" id="mod_login_remember" class="input_chk" value="yes" alt="Remember Me" style="background-color: #fff;"/>
	<input type="submit" name="Submit" class="loginbutton" value="LOGIN" style="background-color: orange;padding:4px;color:#fff;border:1px solid #fff;border-radius:3px;">
	<br /><a href="/user/register">No Account Yet?.... Register NOW!</a>
	<input type="hidden" name="option" value="com_topbetta_user">
	<input type="hidden" name="task" value="login"> 
	<input type="hidden" name="return" value="<?php echo base64_encode(JURI::current()); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>		
	</form>
</div>
</body>
</html>

