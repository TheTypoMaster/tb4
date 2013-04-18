<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

if ($this->defaultValidationNeeded)
{
    JHtml::_('behavior.formvalidation');
}

$profileFields = $this->profileFields;
?>

<script type="text/javascript">
    var jfbcUsernameIsAvailable = '<?php echo JText::_('COM_JFBCONNECT_USERNAME_IS_AVAILABLE'); ?>';
    var jfbcUsernameIsInUse = '<?php echo JText::_('COM_JFBCONNECT_USERNAME_IS_IN_USE'); ?>';
    var jfbcEmailIsAvailable = '<?php echo JText::_('COM_JFBCONNECT_EMAIL_IS_AVAILABLE'); ?>';
    var jfbcEmailIsInUse = '<?php echo JText::_('COM_JFBCONNECT_EMAIL_IS_IN_USE'); ?>';
    var jfbcPasswordInvalid = '<?php echo JText::_('COM_JFBCONNECT_PASSWORD_INVALID'); ?>';
    var jfbcPassword2NoMatch = '<?php echo JText::_('COM_JFBCONNECT_PASSWORDS_DO_NOT_MATCH'); ?>';
    var jfbcRoot = '<?php echo JURI::base(); ?>';
</script>

<div id="jfbc_loginregister">

    <h1><?php echo JText::_('COM_JFBCONNECT_WELCOME') . ' ' . $this->fbUserProfile['first_name'] ?>!</h1>

    <p><?php echo JText::_('COM_JFBCONNECT_THANKS_FOR_SIGNING_IN');?></p>

    <div id="jfbc_loginregister_userinfo" class="<?php echo $this->displayType; ?>">
        <div id="jfbc_loginregister_existinguser">
            <form action="" method="post" name="form">
                <fieldset>
                    <legend><?php echo JText::_('COM_JFBCONNECT_EXISTING_USER_REGISTRATION')?></legend>
                    <p><?php echo JText::_('COM_JFBCONNECT_EXISTING_USER_INSTRUCTIONS') ?></p>
                    <dl>
                        <dt><label><?php echo JText::_('COM_JFBCONNECT_USERNAME') ?> </label></dt>
                        <dd><input type="text" class="inputbox" name="username" value="" size="20"/></dd>
                        <dt><label><?php echo JText::_('COM_JFBCONNECT_PASSWORD') ?> </label></dt>
                        <dd><input type="password" class="inputbox" name="password" value="" size="20"/></dd>
                        <dt></dt><dd><input type="submit" class="button" value="<?php echo JText::_('COM_JFBCONNECT_LOGIN'); ?>"/>
                        </dd>
                    </dl>
                </fieldset>

                <input type="hidden" name="controller" value="loginregister"/>
                <input type="hidden" name="view" value="loginregister"/>
                <input type="hidden" name="option" value="com_jfbconnect"/>
                <input type="hidden" name="task" value="loginMap"/>
                <?php echo JHTML::_('form.token'); ?>
                <?php echo $this->jLinkedLoginButton; ?>
            </form>
            <div style="clear:both"> </div>
        </div>
        <div id="jfbc_loginregister_newuser"">

            <?php
            
                ?>
        <form action="" method="post" name="adminForm" class="form-validate" id="adminForm">
            <fieldset>
            <legend><?php echo JText::_('COM_JFBCONNECT_NEW_USER_REGISTRATION') ?></legend>
                <p><?php echo JText::_('COM_JFBCONNECT_NEW_USER_INSTRUCTIONS') ?></p>
                <div class="label username"><label><?php echo JText::_('COM_JFBCONNECT_USERNAME') ?></label></div><input
                    type="text" class="inputbox required" id="username" name="username"
                    value="<?php echo $this->postDataUsername;?>" size="20"
                    onblur="jfbc.register.checkUsernameAvailable()"/><br/>
                <div id="jfbcUsernameSuccess"></div>
                <?php if ($this->configModel->getSetting('registration_show_email') || !$this->fbUserEmail)
            { ?>
                <div class="label email"><label><?php echo JText::_('COM_JFBCONNECT_E-MAIL_ADDRESS')?></label></div>
                <input type="text" class="inputbox required" id="email" name="email"
                       value="<?php echo $this->fbUserEmail; ?>" size="20"
                       onblur="jfbc.register.checkEmailAvailable('emailSuccess')"/><br/>
                <?php } else
            { ?>
                <input type="hidden" class="inputbox" id="email" name="email"
                       value="<?php echo $this->fbUserProfile['email']; ?>"/>
                <?php } ?>
                <div id="jfbcEmailSuccess"></div>
                <div class="label password"><label><?php echo JText::_('COM_JFBCONNECT_PASSWORD')?></label></div><input
                    type="password" class="inputbox required" id="password" name="password" value="" size="20"
                    onblur="jfbc.register.checkPassword()"/><br/>
                <div id="jfbcPasswordSuccess"></div>
                <div class="label password1"><label><?php echo JText::_('COM_JFBCONNECT_VERIFY_PASSWORD') ?></label>
                </div><input type="password" class="inputbox required" id="password2" name="password2" value=""
                             size="20" onblur="jfbc.register.checkPassword2()"/><br/>
                <div id="jfbcPassword2Success"></div>
                <?php
             //SC15
             //SC16
            ?>
            <div class="profile-fields">
                <?php
                foreach ($profileFields as $profileForm)
                    echo $profileForm;
                ?>
            </div>

            <input type="submit" class="button validate" value="<?php echo JText::_('COM_JFBCONNECT_REGISTER')?>"/>

            <input type="hidden" name="controller" value="loginregister"/>
            <input type="hidden" name="option" value="com_jfbconnect"/>
            <input type="hidden" name="view" value="loginregister"/>
            <input type="hidden" name="task" value="createNewUser"/>
            <?php echo JHTML::_('form.token'); ?>
        </fieldset>
        </form>
        </div>
    </div>

    <div style="clear:both;"></div>

    <?php
    $link = 'http://www.sourcecoast.com/jfbconnect/';
    $affiliateID = $this->configModel->getSetting('affiliate_id');
    if ($affiliateID)
        $link .= '?amigosid=' . $affiliateID;

    if ($this->configModel->getSetting('show_powered_by_link'))
    {
        ?>
        <div id="powered_by"><?php echo JText::_('COM_JFBCONNECT_POWERED_BY');?> <a target="_blank"
                                                                                    href="<?php echo $link;?>"
                                                                                    title="Joomla Facebook Integration">JFBConnect</a>
        </div>
        <?php } ?>

</div>
