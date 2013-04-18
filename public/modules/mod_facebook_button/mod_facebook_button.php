<?php
/**
* @version		$Id: mod_facebook_button.php 
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if(JFactory::getUser()->guest) : ?>
    <a href="javascript:void(0)" onclick="jfbc.login.login_custom();"><img src="/images/facebook-login-large.png" style="margin-bottom: 5px;"/></a>
<?php endif; ?>