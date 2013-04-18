<?php
/**
 * @package        JFBConnect/JLinked
 * @copyright (C) 2011-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');


    
    $passwordName = 'passwd';
    $loginRememberText = JText::_('Remember me');
     //SC15
     //SC16

    if ($registerType == "communitybuilder")
        $passwordName = 'passwd';
    ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
    <?php if($params->get('user_intro')): ?>
        <div class="sc_login_desc pretext">
            <p><?php echo $params->get('user_intro'); ?></p>
        </div>
    <?php endif;

    //Show Joomla Username/Password/Login buttons
    if ($params->get('showLoginForm'))
    {
    ?>

    <fieldset class="input userdata">
        <p id="form-login-username">
            <label for="modlgn-username"><?php echo JText::_('MOD_SCLOGIN_USERNAME') ?></label>
            <input id="modlgn-username" type="text" name="username" class="inputbox" alt="username" size="18"/>
        </p>
        <p id="form-login-password">
            <label for="modlgn-passwd"><?php echo JText::_('MOD_SCLOGIN_PASSWORD') ?></label>
            <input id="modlgn-passwd" type="password" name="<?php echo $passwordName; ?>" class="inputbox" size="18"
                   alt="password"/>
        </p>
        <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
        <p id="form-login-remember">
            <label for="modlgn-remember"><?php echo $loginRememberText;?></label>
            <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me"/>
        </p>
        <?php endif; ?>
        <input type="submit" name="Submit" class="button" value="<?php echo JText::_('MOD_SCLOGIN_LOGIN') ?>"/>


    <?php if ($registerType != "communitybuilder")
    {
        
        echo '<input type="hidden" name="option" value="com_user"/>';
        echo '<input type="hidden" name="task" value="login"/>';
         //SC15
         //SC16
        echo '<input type="hidden" name="return" value="'. $jLoginUrl.'"/>';
    }
    else // Use Community Builder's login
    {
        include_once(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php');
        global $_CB_framework;
        echo '<input type="hidden" name="option" value="com_comprofiler" />' . "\n";
        echo '<input type="hidden" name="task" value="login" />' . "\n";
        echo '<input type="hidden" name="op2" value="login" />' . "\n";
        echo '<input type="hidden" name="lang" value="' . $_CB_framework->getCfg('lang') . '" />' . "\n";
        echo '<input type="hidden" name="force_session" value="1" />' . "\n"; // makes sure to create joomla 1.0.11+12 session/bugfix
        echo '<input type="hidden" name="return" value="B:'.$jLoginUrl.'"/>';
        echo cbGetSpoofInputTag('login');
    }
    echo JHTML::_('form.token'); ?>
    </fieldset>

<?php
    }

    $jfbcLogin = "";
    $loginButtonType = $params->get('loginButtonType');
    if($loginButtonType == "text_link")
    {
        $loginButtonLinkText = $params->get('loginButtonLinkText');
        $jfbcLogin = '<a href="javascript:void(0)" onclick="jfbc.login.login_custom();">'.$loginButtonLinkText.'</a>';
    }
    else if($loginButtonType == "image_link")
    {
        $loginButtonLinkImage = $params->get('loginButtonLinkImage');
        $jfbcLogin = '<a href="javascript:void(0)" onclick="jfbc.login.login_custom();"><img src="'.$loginButtonLinkImage.'" /></a>';
    }
    else //if($loginButtonType == 'javascript')
    {
        $jfbcLogin = $helper->getJFBConnectLoginButton();
    }

    if ($jfbcLogin != "")
        echo $jfbcLogin;

    $jlinkedLogin = $helper->getJLinkedLoginButton();
    if ($jlinkedLogin != "")
        echo $jlinkedLogin;

    // Show the register & forgot links
    if ($params->get('showRegisterLink') || $params->get('showForgotUsername') || $params->get('showForgotPassword'))
        echo "<ul>";

    if ($params->get('showRegisterLink'))
        echo '<li><a href="' . $registerLink . '">' . JText::_('MOD_SCLOGIN_REGISTER_FOR_THIS_SITE') . '</a></li>';

    if ($params->get('register_type') == "communitybuilder" && ($params->get('showForgotUsername') || $params->get('showForgotPassword')))
        echo '<li><a href="' . $forgotLink . '">' . JText::_('MOD_SCLOGIN_FORGOT_LOGIN') . '</a></li>';
    else
    {
        if ($params->get('showForgotUsername'))
            echo '<li><a href="' . $forgotUsernameLink . '">' . JText::_('MOD_SCLOGIN_FORGOT_USERNAME') . '</a></li>';
        if ($params->get('showForgotPassword'))
            echo '<li><a href="' . $forgotPasswordLink . '">' . JText::_('MOD_SCLOGIN_FORGOT_PASSWORD') . '</a></li>';
    }
    if ($params->get('showRegisterLink') || $params->get('showForgotUsername') || $params->get('showForgotPassword'))
        echo "</ul>";

    echo $helper->getPoweredByLink();
?>
</form>