<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js" lang="en"> 		   <![endif]--><head>
	
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

  	<title>TopBetta &raquo; Win a Hyundai i20</title>

	<meta name="description" content="TopBetta. Get a chance to win the the Hyundai i20">
	<meta name="keywords" content="">

	
	<!-- Set a base location for assets -->
	<base href=""/>
	<!-- End base -->

    
    <link href='css/style.css' rel='stylesheet' type='text/css'>
    <link href='css/jquery.tweet.css' rel='stylesheet' type='text/css'>
    <link href='css/facebox.css' rel='stylesheet' type='text/css'>
   
    <link href='css/jquery.countdown.css' rel='stylesheet' type='text/css'>
    
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Arimo' rel='stylesheet' type='text/css'>
    
  	<!-- End CSS-->



		
	<!-- Add some theme option variables for styling -->
	<style type="text/css">
		body { background: #0a0a0a url(img/bg_blackwhite.jpg) repeat no-repeat; }
		#facebox{
		color:#000!important;
		}
	</style>
</head>

<body>
	<!-- //start facebook SDK integration -->
	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
	    // init the FB JS SDK
	    FB.init({
	      appId      : '280206952107277', // App ID from the App Dashboard
	      channelUrl : '//topbetta.com/channel.html', // Channel File for x-domain communication
	      status     : true, // check the login status upon init?
	      cookie     : true, // set sessions cookies to allow your server to access the session?
	      xfbml      : true  // parse XFBML tags on this page?
	    });
	
	    // Additional initialization code such as adding Event Listeners goes here
        FB.getLoginStatus(function( response ) {
           console.log(response.status);
           //alert(response.status);
           if (!response.status || response.status == 'unknown') {
	           $('#facebook').html('<div style="float: right; border:1px solid #444; background: #111; width: 246px;"><div style="color: #ccc; background: #333; line-height: 24px; font-size: 12px; font-weight: bold; padding: 0 5px;">Find us on Facebook</div><div style="padding: 10px; font-size: 16px; line-height: 20px; color: #555;">Please login to Facebook to see this content.</div></div>');
           }
        });

	  };

	  // Load the SDK's source Asynchronously
	  // Note that the debug version is being actively developed and might 
	  // contain some type checks that are overly strict. 
	  // Please report such bugs using the bugs tool.
	  (function(d, debug){
	     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement('script'); js.id = id; js.async = true;
	     js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
	     ref.parentNode.insertBefore(js, ref);
	   }(document, /*debug*/ false));
	</script>
	<!-- //end facebook integration -->

	<!-- container -->
	<div class="container">	
    
    <header class="clear">
        <div id="logo">
            <a href="/">
                <img src="img/logo_topbetta.jpg" border="0" alt="TopBetta" />
            </a>
        </div>
    
        <div id="partners">
            <img src="img/logo_adelaide.jpg" border="0" alt="Adelaide" />
            <img src="img/logo_hyundai.jpg" border="0" alt="Hyundai" />
        </div>
    </header>