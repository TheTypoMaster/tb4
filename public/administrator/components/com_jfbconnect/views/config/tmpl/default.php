<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');

$model = $this->model;
$isLocaleSupported = $this->isLocaleSupported;
$menuItems = JHTML::_('menu.linkoptions');

$pane = JPane::getInstance('tabs');
?>
<form method="post" id="adminForm" name="adminForm">
<?php
echo $pane->startPane('content-pane');

// First slider panel
echo $pane->startPanel('User', 'config_user');
?>

<div>
    <div class="config_row">
        <div class="config_setting header">Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Registration Flow:</div>
        <div class="config_option">
            <fieldset id="create_new_users" class="radio">
                <input type="radio" id="create_new_users1" name="create_new_users" value="1"
                       onclick="jfbcAdmin.config.setUserCreation('fullJoomla')" <?php echo $model->getSetting('create_new_users')
                        ? 'checked="checked"' : ""; ?> onclick="jfbcAdmin.config.setUserCreation('fullJoomla')">
                <label for="create_new_users1">Normal Registration</label>
                <input type="radio" id="create_new_users0" name="create_new_users" value="0"
                       onclick="jfbcAdmin.config.setUserCreation('facebookOnly')" <?php echo $model->getSetting('create_new_users')
                        ? '""' : 'checked="checked"'; ?> onclick="jfbcAdmin.config.setUserCreation('facebookOnly')">
                <label for="create_new_users0">Automatic Registration</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Normal Registration::A new Joomla user will be created whenever a user logs in using Facebook
            credentials. This allows a user to return to the site using their Joomla credentials while not logged into
            Facebook.<br/><br/>
            <b>Automatic Registration</b><br/>A new Joomla user will be created whenever a user logs in using Facebook credentials. They will not
            be able to log-in using just the Joomla credentials and will only be able to log in using their Facebook
            credentials. See the 'Auto Username Prefix' setting to set up the format for the user's new username.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row fullJoomla">
        <div class="config_setting">Email Field on Registration<br/>(Normal Registration Only):</div>
        <div class="config_option">
            <fieldset id="registration_show_email" class="radio">
                <input type="radio" id="registration_show_email0" name="registration_show_email"
                       value="0" <?php echo $model->getSetting('registration_show_email') ? '""'
                        : 'checked="checked"'; ?> />
                <label for="registration_show_email0">Hide</label>
                <input type="radio" id="registration_show_email1" name="registration_show_email"
                       value="1" <?php echo $model->getSetting('registration_show_email') ? 'checked="checked"'
                        : ""; ?>  />
                <label for="registration_show_email1">Show</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Show::Allow user to set their own e-mail address during the Joomla user account creation.<br/><br/>
            <b>Hide</b><br/>User's email address will automatically be set to their Facebook email address, or proxy email
            address if they don't allow your site access to their real email permission.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row fullJoomla">
        <div class="config_setting">Registration Display Mode<br/>(Normal Registration Only):</div>
        <div class="config_option">
            <fieldset id="registration_display_mode" class="radio">
                <?php $setting = $model->getSetting('registration_display_mode'); ?>
                <input type="radio" id="registration_display_mode_horizontal" name="registration_display_mode"
                       value="horizontal" <?php if ($setting == 'horizontal') echo 'checked="checked"'; ?> />
                <label for="registration_display_mode_vertical">Horizontal</label>
                <input type="radio" id="registration_display_mode_vertical" name="registration_display_mode"
                       value="vertical" <?php if ($setting == 'vertical') echo 'checked="checked"'; ?> />
                <label for="registration_display_mode_vertical">Vertical</label>
                <input type="radio" id="registration_display_mode_register_only" name="registration_display_mode"
                       value="register-only" <?php if ($setting == 'register-only') echo 'checked="checked"'; ?> />
                <label for="registration_display_mode_register_only">Register Only</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Horizontal::Login and Register forms will be shown side-by-side at about 50% width.<br/><br/>
            <b>Vertical</b><br/>Login and Register forms will be shown on top of each other, at 100% width.<br/><br/>
            <b>Register Only</b><br/>Hide the login form altogether (good for sites with no existing users). Register form will be shown at full width
            ">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row facebookOnly">
        <div class="config_setting">Auto Username Prefix<br/>(Automatic Registration):</div>
        <div class="config_option">
            <select name="auto_username_format">
                <option value="0" <?php echo ($model->getSetting('auto_username_format') == '0') ? 'selected' : ""; ?>>
                    fb_
                </option>
                <option value="1" <?php echo ($model->getSetting('auto_username_format') == '1') ? 'selected' : ""; ?>>
                    first.last
                </option>
                <option value="2" <?php echo ($model->getSetting('auto_username_format') == '2') ? 'selected' : ""; ?>>
                    firlas
                </option>
            </select>
        </div>
        <div class="config_description hasTip"
             title="Prefix for username when 'Automatic Registration' is enabled. Username examples: fb_12345, alex.andreae12345 or
            aleand12345">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row facebookOnly">
        <div class="config_setting">Generate Random Pasword<br/>(Automatic Registration):</div>
        <div class="config_option">
            <fieldset id="generate_random_password" class="radio">
                <input type="radio" id="generate_random_password1" name="generate_random_password" value="1" <?php echo $model->getSetting('generate_random_password') ? 'checked="checked"' : ""; ?> />
                <label for="generate_random_password1">Yes</label>
                <input type="radio" id="generate_random_password0" name="generate_random_password" value="0" <?php echo $model->getSetting('generate_random_password') ? '""' : 'checked="checked"'; ?>  />
                <label for="generate_random_password0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="When 'Automatic Registration' is enabled and set to 'Yes', JFBConnect will generate a random password for the new Joomla user.">Info
        </div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting">Automatically Link Facebook Users by Email:</div>
        <div class="config_option">
            <fieldset id="facebook_auto_map_by_email" class="radio">
                <input type="radio" id="facebook_auto_map_by_email1" name="facebook_auto_map_by_email"
                       value="1" <?php echo $model->getSetting('facebook_auto_map_by_email') ? 'checked="checked"'
                        : ""; ?> />
                <label for="facebook_auto_map_by_email1">Yes</label>
                <input type="radio" id="facebook_auto_map_by_email0" name="facebook_auto_map_by_email"
                       value="0" <?php echo $model->getSetting('facebook_auto_map_by_email') ? '""'
                        : 'checked="checked"'; ?>  />
                <label for="facebook_auto_map_by_email0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::If a user logs in via Facebook for the first time, and their email address is already in use by
            a Joomla user, map the Facebook user to that account automatically.<br/><br/>
            <b>No</b><br/>The 'Registration Flow' setting above will apply. The user will be asked to associate their Facebook
            account to a Joomla account, or one will be created automatically for them.<br/><br/>
            <b>Note:</b><br/>If set to 'No', and their Facebook email address is already in use on the site, they may
            receive an error. Joomla does not allow using the same email address for 2 separate users.">Info
        </div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting">Skip Joomla User Activation:</div>
        <div class="config_option">
            <fieldset id="joomla_skip_newuser_activation" class="radio">
                <input type="radio" id="joomla_skip_newuser_activation1" name="joomla_skip_newuser_activation"
                       value="1" <?php echo $model->getSetting('joomla_skip_newuser_activation') ? 'checked="checked"'
                        : ""; ?> />
                <label for="joomla_skip_newuser_activation1">Yes</label>
                <input type="radio" id="joomla_skip_newuser_activation0" name="joomla_skip_newuser_activation"
                       value="0" <?php echo $model->getSetting('joomla_skip_newuser_activation') ? '""'
                        : 'checked="checked"'; ?>  />
                <label for="joomla_skip_newuser_activation0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::Skip the User Activation requirement, if set in Joomla. This setting will automatically activate
            all new Facebook users.<br/><br/>
            <b>No</b><br/>Use the User Activation setting in the Global Configuration area for all new Facebook users.">
            Info
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
echo $pane->endPanel();

//New slider panel
echo $pane->startPanel('Login / Logout', 'config_redirection');
?>
<div>
    <div class="config_row">
        <div class="config_setting header">Facebook Login Redirection Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting">Enable New User Redirection:</div>
        <div class="config_option">
            <fieldset id="facebook_new_user_redirect_enable" class="radio">
                <input type="radio" id="facebook_new_user_redirect_enable1" name="facebook_new_user_redirect_enable"
                       value="1" <?php echo $model->getSetting('facebook_new_user_redirect_enable')
                        ? 'checked="checked"' : ""; ?>
                       onclick="jfbcAdmin.config.showOptions('newUserRedirection', true)"/>
                <label for="facebook_new_user_redirect_enable1">Yes</label>
                <input type="radio" id="facebook_new_user_redirect_enable0" name="facebook_new_user_redirect_enable"
                       value="0" <?php echo $model->getSetting('facebook_new_user_redirect_enable') ? '""'
                        : 'checked="checked"'; ?>  onclick="jfbcAdmin.config.showOptions('newUserRedirection', false)"/>
                <label for="facebook_new_user_redirect_enable0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::The new user will always be redirected to the specified page when registering.<br/><br/>
            <b>No</b><br/>Upon registration, the user will remain on their current page.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row newUserRedirection">
        <div class="config_setting">New User Redirection:</div>
        <div class="config_option">
            <?php
            $selected = $model->getSetting('facebook_new_user_redirect');
            echo JHTML::_('select.genericlist', $menuItems, 'facebook_new_user_redirect', null, 'value', 'text', $selected);
            ?>
        </div>
        <div class="config_description hasTip"
             title="Set the page to redirect a new Facebook user to after the first time they've connected and a new Joomla user is
            created for them. This is useful for sending users to a one-time 'Welcome' page.">Info
        </div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting">Enable Returning User Redirection:</div>
        <div class="config_option">
            <fieldset id="facebook_login_redirect_enable" class="radio">
                <input type="radio" id="facebook_login_redirect_enable1" name="facebook_login_redirect_enable"
                       value="1" <?php echo $model->getSetting('facebook_login_redirect_enable') ? 'checked="checked"'
                        : ""; ?> onclick="jfbcAdmin.config.showOptions('loginRedirection', true)"/>
                <label for="facebook_login_redirect_enable1">Yes</label>
                <input type="radio" id="facebook_login_redirect_enable0" name="facebook_login_redirect_enable"
                       value="0" <?php echo $model->getSetting('facebook_login_redirect_enable') ? '""'
                        : 'checked="checked"'; ?>  onclick="jfbcAdmin.config.showOptions('loginRedirection', false)"/>
                <label for="facebook_login_redirect_enable0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::The user will always be redirected to the specified page when logging in.<br/><br/>
            <b>No</b><br/>After logging in, the user will remain on their current page.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row loginRedirection">
        <div class="config_setting">Returning User Redirection:</div>
        <div class="config_option">
            <?php
            $selected = $model->getSetting('facebook_login_redirect');
            echo JHTML::_('select.genericlist', $menuItems, 'facebook_login_redirect', null, 'value', 'text', $selected);
            ?>
        </div>
        <div class="config_description hasTip"
             title="Redirection page for returning users logging in via Facebook.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting header">Facebook Login Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Auto Login Facebook Users:</div>
        <div class="config_option">
            <fieldset id="facebook_auto_login" class="radio facebookAutoLogin">
                <input type="radio" id="facebook_auto_login1" name="facebook_auto_login"
                       value="1" <?php echo $model->getSetting('facebook_auto_login') ? 'checked="checked"' : ""; ?>
                        />
                <label for="facebook_auto_login1">Yes</label>
                <input type="radio" id="facebook_auto_login0" name="facebook_auto_login"
                       value="0" <?php echo $model->getSetting('facebook_auto_login') ? '""'
                        : 'checked="checked"'; ?> />
                <label for="facebook_auto_login0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::If a user returns to your site who is already logged into Facebook AND is already a member of
            your site with a valid user-mapping, log them in automatically.<br/><br/>
            <b>No</b><br/>A user must always log into your site, regardless of whether they're already logged into their
            mapped Facebook account.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Logout Of Joomla Only:</div>
        <div class="config_option">
            <fieldset id="logout_joomla_only" class="radio facebookLogout">
                <input type="radio" id="logout_joomla_only1" name="logout_joomla_only"
                       value="1" <?php echo $model->getSetting('logout_joomla_only') ? 'checked="checked"' : ""; ?>
                        />
                <label for="logout_joomla_only1">Yes</label>
                <input type="radio" id="logout_joomla_only0" name="logout_joomla_only"
                       value="0" <?php echo $model->getSetting('logout_joomla_only') ? '""' : 'checked="checked"'; ?> />
                <label for="logout_joomla_only0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::The user will be logged out of only Joomla when the Logout button is pressed.<br/><br/>
            <b>No</b><br/>The user will be logged out of Joomla AND Facebook when the Logout button is pressed.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Integrate into Joomla Login:</div>
        <div class="config_option">
            <fieldset id="show_login_with_joomla_reg" class="radio">
                <input type="radio" id="show_login_with_joomla_reg1" name="show_login_with_joomla_reg" value="1" <?php echo $model->getSetting('show_login_with_joomla_reg') ? 'checked="checked"': ""; ?> />
                <label for="show_login_with_joomla_reg1">Yes</label>
                <input type="radio" id="show_login_with_joomla_reg0" name="show_login_with_joomla_reg" value="0" <?php echo $model->getSetting('show_login_with_joomla_reg') ? '""': 'checked="checked"'; ?>  />
                <label for="show_login_with_joomla_reg0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Include Login with Facebook button on Joomla Login page">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Show "Logging In" popup:</div>
        <div class="config_option">
            <fieldset id="facebook_login_show_modal" class="radio">
                <input type="radio" id="facebook_login_show_modal1" name="facebook_login_show_modal" value="1" <?php echo $model->getSetting('facebook_login_show_modal') ? 'checked="checked"': ""; ?> />
                <label for="facebook_login_show_modal1">Yes</label>
                <input type="radio" id="facebook_login_show_modal0" name="facebook_login_show_modal" value="0" <?php echo $model->getSetting('facebook_login_show_modal') ? '""': 'checked="checked"'; ?>  />
                <label for="facebook_login_show_modal0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Show a popup notifying the user that the login process is occuring. The popup will show for both user-initiated logins as well as automatic logins (if enabled).
             <br/>Enabling this will insert the Mootools onto the page, if it's not already included.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting header">Facebook Logout Redirection Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Enable Logout Redirection:</div>
        <div class="config_option">
            <fieldset id="facebook_logout_redirect_enable" class="radio">
                <input type="radio" id="facebook_logout_redirect_enable1" name="facebook_logout_redirect_enable"
                       value="1" <?php echo $model->getSetting('facebook_logout_redirect_enable') ? 'checked="checked"'
                        : ""; ?> onclick="jfbcAdmin.config.showOptions('logoutRedirection', true)"/>
                <label for="facebook_logout_redirect_enable1">Yes</label>
                <input type="radio" id="facebook_logout_redirect_enable0" name="facebook_logout_redirect_enable"
                       value="0" <?php echo $model->getSetting('facebook_logout_redirect_enable') ? '""'
                        : 'checked="checked"'; ?> onclick="jfbcAdmin.config.showOptions('logoutRedirection', false)"/>
                <label for="facebook_logout_redirect_enable0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="Yes::The user will always be redirected to the specified page when logging out.<br/><br/>
            <b>No</b><br/>After logging out, the user will remain on their current page.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row logoutRedirection">
        <div class="config_setting">Logout Redirection:</div>
        <div class="config_option">
            <?php
            $selected = $model->getSetting('facebook_logout_redirect');
            echo JHTML::_('select.genericlist', $menuItems, 'facebook_logout_redirect', null, 'value', 'text', $selected);
            ?>
        </div>
        <div class="config_description hasTip"
             title="Redirection page for users logging out of your site, when using the JFBConnect logout button.">Info
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Status / Wall', 'config_status');
?>
<div>
    <div class="config_row">
        <div class="config_setting header">New User Status Settings</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
        <div>(Leave all fields blank to not post to the user's wall after registration)</div>
    </div>
    <div class="config_row">
        <div class="config_setting">Message:</div>
        <div class="config_option">
            <textarea rows="3" cols="35"
                      name="facebook_new_user_status_msg"><?php echo $model->getSetting('facebook_new_user_status_msg'); ?></textarea>
        </div>
        <div class="config_description hasTip"
             title="Message to set as the user's current Facebook status when registering on your
            site. This message is sent to the user's profile page, so be tactful and we recommend keeping it short. HTML
            is not allowed.<br/> Ex. 'just joined MySite'">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">URL:</div>
        <div class="config_option">
            <input type="text" name="facebook_new_user_status_link"
                   value="<?php echo $model->getSetting('facebook_new_user_status_link') ?>" size="40">
        </div>
        <div class="config_description hasTip"
             title="Link to set in the user's current Facebook status when registering on your site. URL's are great for getting
            your page noticed!">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Picture URL:</div>
        <div class="config_option"><input type="text" name="facebook_new_user_status_picture"
                                          value="<?php echo $model->getSetting('facebook_new_user_status_picture') ?>"
                                          size="40"></div>
        <div class="config_description hasTip"
             title="Picture to set in the user's current Facebook status when registering on your site.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting header">Returning User Status Settings</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
        <div>(Leave all fields blank to not post to the user's wall on login)</div>
    </div>
    <div class="config_row">
        <div class="config_setting">Message:</div>
        <div class="config_option">
            <textarea rows="3" cols="35"
                      name="facebook_login_status_msg"><?php echo $model->getSetting('facebook_login_status_msg'); ?></textarea>
        </div>
        <div class="config_description hasTip"
             title="Message to set as the user's current Facebook status when they log back into
            your site. This message is sent to the user's profile page, so be tactful and we recommend keeping it short.
            HTML is not allowed.<br/> Ex. 'just logged back into MySite.'">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">URL:</div>
        <div class="config_option">
            <input type="text" name="facebook_login_status_link"
                   value="<?php echo $model->getSetting('facebook_login_status_link') ?>" size="40">
        </div>
        <div class="config_description hasTip"
             title="Link to set in the user's current Facebook status when they log back into your site. URL's are great for
            getting your page noticed!">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Picture URL:</div>
        <div class="config_option">
            <input type="text" name="facebook_login_status_picture"
                   value="<?php echo $model->getSetting('facebook_login_status_picture') ?>" size="40">
        </div>
        <div class="config_description hasTip"
             title="Picture to set in the user's current Facebook status when they log back into your site.">Info
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Facebook API', 'config_facebook_api');
?>
<div>
    <div class="config_row">
        <div class="config_setting header">Faceboook API Setting</div>
        <div class="config_option header">Options</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Facebook App ID:</div>
        <div class="config_option"><input type="text" name="facebook_app_id"
                                          value="<?php echo $model->getSetting('facebook_app_id') ?>" size="50"></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Facebook Secret Key:</div>
        <div class="config_option"><input type="text" name="facebook_secret_key"
                                          value="<?php echo $model->getSetting('facebook_secret_key') ?>" size="50">
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<br/>

<div>
    <div>
        <div class="config_row">
            <div class="config_setting header">Facebook Permissions Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
            <div>JFBConnect automatically determines the permissions required for the user to login based on your
                settings.<br/>Use the option below to specify additional permissions you want if you are developing
                custom functionality which needs additional user permissions.
            </div>
        </div>

        <div class="config_row">
            <div class="config_setting">Additional Permissions Request:</div>
            <div class="config_option">
                <textarea rows="3" cols="35"
                          name="facebook_perm_custom"><?php echo $model->getSetting('facebook_perm_custom'); ?></textarea>
            </div>
            <div class="config_description hasTip"
                 title="Specify the additional permissions you require from your users on logging in. This should only be used for custom development.<br/>
                Note: Please enter a comma-separated list of values.<br/>
                Example: publish_stream,user_events,user_hometown">Info
            </div>
            <div class="config_description2">
                Please see <a
                    href="http://developers.facebook.com/docs/authentication/permissions/" target="_blank">this page</a>
                for
                a list of available permissions.
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <br/>
</div>
<div>
    <div>
        <div class="config_row">
            <div class="config_setting header">Facebook Debugging Setting</div>
            <div class="config_option header">Options</div>
            <div class="config_description header">Description</div>
            <div style="clear:both"></div>
            <div>These settings help diagnose and fix issues related to communicating with Facebook.</div>
        </div>
        <div class="config_row">
            <div class="config_setting">Display API Errors On Front-End:</div>
            <div class="config_option">
                <fieldset id="facebook_display_errors" class="radio">
                    <input type="radio" id="facebook_display_errors1" name="facebook_display_errors"
                           value="1" <?php echo $model->getSetting('facebook_display_errors') ? 'checked="checked"'
                            : ""; ?>  />
                    <label for="facebook_display_errors1">Yes</label>
                    <input type="radio" id="facebook_display_errors0" name="facebook_display_errors"
                           value="0" <?php echo $model->getSetting('facebook_display_errors') ? '""'
                            : 'checked="checked"'; ?>  />
                    <label for="facebook_display_errors0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                 title="Enable to help debug logging in and registration problems.">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Disable SSL Certificate Validation:</div>
            <div class="config_option">
                <fieldset id="facebook_curl_disable_ssl" class="radio">
                    <input type="radio" id="facebook_curl_disable_ssl1" name="facebook_curl_disable_ssl"
                           value="1" <?php echo $model->getSetting('facebook_curl_disable_ssl')
                            ? 'checked="checked"'
                            : ""; ?>  />
                    <label for="facebook_curl_disable_ssl1">Yes</label>
                    <input type="radio" id="facebook_curl_disable_ssl0" name="facebook_curl_disable_ssl"
                           value="0" <?php echo $model->getSetting('facebook_curl_disable_ssl') ? '""'
                            : 'checked="checked"'; ?>  />
                    <label for="facebook_curl_disable_ssl0">No</label>
                </fieldset>
            </div>
            <div class="config_description hasTip"
                 title="If you receive a 'SSL certificate problem, verify that the CA cert is OK.' error, enable this
                setting.<br/>
                Your server's SSL root certificates are out of date. This will bypass the validation check.<br/>
                You should contact your host to update their root certificates.<br/>">Info
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting">Facebook Language Locale Override:<br/>(Generally, leave blank)</div>
            <div class="config_option">
                <input type="text" name="facebook_language_locale"
                       value="<?php echo $model->getSetting('facebook_language_locale'); ?>" size="6" maxlength="5">
            </div>
            <div class="config_description hasTip"
                 title="<strong>Normally, leave this override BLANK</strong>, unless your current Joomla language is not
                supported by Facebook. It will override the language locale for Facebook integration.<br/>
                The value should be a supported language locale, such as 'de_DE' or 'en_US'.">Info
            </div>
            <div class="config_description2">
                Please see <a
                    href="http://www.facebook.com/translations/FacebookLocales.xml" target="_blank">
                http://www.facebook.com/translations/FacebookLocales.xml</a>
                for supported locales.<br/>
                <?php
                if (!$isLocaleSupported)
                {
                    echo '<span style="color:red;"><strong>WARNING - Your locale is not supported by Facebook; please provide a supported locale.</strong></span>';
                }
                ?>
            </div>
            <div style="clear:both"></div>
        </div>

    </div>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel('Misc', 'config_misc');
?>
<div>
    <div class="config_row">
        <div class="config_setting header">Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Show Powered By Link:</div>
        <div class="config_option">
            <fieldset id="show_powered_by_link" class="radio">
                <input type="radio" id="show_powered_by_link1" name="show_powered_by_link"
                       value="1" <?php echo $model->getSetting('show_powered_by_link') ? 'checked="checked"'
                        : ""; ?>  />
                <label for="show_powered_by_link1">Yes</label>
                <input type="radio" id="show_powered_by_link0" name="show_powered_by_link"
                       value="0" <?php echo $model->getSetting('show_powered_by_link') ? '""'
                        : 'checked="checked"'; ?>  />
                <label for="show_powered_by_link0">No</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
             title="If this is set to Yes, shows Powered By Link at the bottom of the Login/Register page.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Affiliate ID:</div>
        <div class="config_option">
            <input type="text" name="affiliate_id" value="<?php echo $model->getSetting('affiliate_id') ?>"
                   size="40">
        </div>
        <div class="config_description hasTip"
             title="SourceCoast Affiliate ID for Powered By Link.">Info
        </div>
        <div class="config_description2">
            By sending referrals, you can earn 20% commissions at <a
                href="http://www.sourcecoast.com" target="_blank">SourceCoast</a> for this and other great
            extensions."
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">SourceCoast Subscription ID:</div>
        <div class="config_option">
            <input type="text" name="sc_download_id" value="<?php echo $model->getSetting('sc_download_id');?>" size="40">
        </div>
        <div class="config_description hasTip"
             title="Subscription ID for the Auto-Configuration tool and Live Update downloads of JFBConnect extension files.">
            Info
        </div>
        <div style="clear:both"></div>
    </div>

</div>
<?php
echo $pane->endPanel();
echo $pane->endPane();
?>

<input type="hidden" name="option" value="com_jfbconnect"/>
<input type="hidden" name="controller" value="config"/>
<input type="hidden" name="cid[]" value="0"/>
<input type="hidden" name="task" value=""/>
<?php echo JHTML::_('form.token'); ?>

</form>

<?php
$createUsers = $model->getSetting('create_new_users') == '1' ? 'fullJoomla' : 'facebookOnly';
$loginRedirection = $model->getSetting('facebook_login_redirect_enable') == '1' ? 'true' : 'false';
$logoutRedirection = $model->getSetting('facebook_logout_redirect_enable') == '1' ? 'true' : 'false';
$newUserRedirection = $model->getSetting('facebook_new_user_redirect_enable') == '1' ? 'true' : 'false';
?>

<script type="text/javascript">
    jfbcAdmin.config.setUserCreation('<?php echo $createUsers; ?>');
    jfbcAdmin.config.showOptions('newUserRedirection', <?php echo $newUserRedirection ?>);
    jfbcAdmin.config.showOptions('loginRedirection', <?php echo $loginRedirection ?>);
    jfbcAdmin.config.showOptions('logoutRedirection', <?php echo $logoutRedirection ?>);
</script>