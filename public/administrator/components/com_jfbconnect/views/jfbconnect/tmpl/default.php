<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.version');
jimport('sourcecoast.utilities');

$configModel = $this->configModel;
$autotuneModel = $this->autotuneModel;
$jfbcLibrary = $this->jfbcLibrary;
$usermapModel = $this->usermapModel;
$appStats = $this->appStats;
$versionChecker = $this->versionChecker;
?>

<!--<div style="float: left; padding: 0 20px 0 0"><img src="<?php echo JURI::root() ?>components/com_jfbconnect/images/jfbconn.png" /></div>-->
<div style="margin:0 0 0 10px; padding: 0 10px 10px 10px">
    <h2>Facebook Application</h2>

    <div style="float:left;width:550px">
        <fieldset style="padding:5px">
            <legend>Configuration Information</legend>
            <div>
                <div style="float:left;width:80px">
                    <?php
                    $logoUrl = $autotuneModel->getAppConfigField('logo_url')->get('value');
                    if ($logoUrl != "")
                        echo '<img src="' . $logoUrl . '" />';
                    else
                        echo "No Application Logo Set";
                    ?>
                </div>
                <div style="float: left; margin: 0 0 0 20px">
                    <?php
                    if ($jfbcLibrary->facebookAppId)
                    {
                        ?>
                        <p style="margin:0 0 5px 0;"><b>Application
                            Name: </b><?php echo $autotuneModel->getAppConfigField('name')->get('value'); ?></p>
                        <?php
                    }
                    else
                    {
                        ?>
                        <p style="margin:0 0 5px 0;"><b>Application Name:<br/> <span style="color:#FF0000">Application ID not set. Please set it in the Configuration Tab.</span></b>
                        </p>
                        <?php } ?>
                    <p style="margin:0 0 5px 0;"><b>Site
                        URL: </b><?php echo $autotuneModel->getAppConfigField('website_url')->get('value'); ?></p>
                    <?php $appDomains = $autotuneModel->getAppConfigField('app_domains')->get('value');
                    if (is_array($appDomains))
                    {
                        ?>
                        <p style="margin:0 0 5px 0;"><b>Site
                            Domain(s): </b><?php echo implode(', ', $appDomains); ?></p>
                        <?php
                        if (!$autotuneModel->getAppConfigField('app_domains')->get('recommendMet'))
                        {
                            if (!$autotuneModel->getAppConfigField('connect_url')->get('recommendMet'))
                            {
                                print "<b style=\"color:#FF1410\">\n";
                                print '<b>WARNING: POSSIBLE MISCONFIGURATION</b><br/>Please run the JFBConnect <a href="index.php?option=com_jfbconnect&view=autotune">AutoTune wizard</a>.';
                                print "</b><br/>";
                            }
                        }
                    } else
                    {
                        ?>
                        <p style="margin:0 0 5px 0;"><b>Site Domain(s):</p>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </fieldset>
    </div>
    <div style="float:left; margin-left:15px;width: 300px">
        <fieldset style="padding: 5px;">
            <legend>Statistics</legend>
            <p style="margin:0 0 5px 0;"><b>Total Connected Users:</b> <?php echo $usermapModel->getTotalMappings(); ?>
            </p>

            <p style="margin:0 0 5px 0;"><b>Active Monthly Users:</b> <?php echo $appStats['monthly_active_users']; ?>
            </p>

            <p style="margin:0 0 5px 0;"><b>Active Weekly Users:</b> <?php echo $appStats['weekly_active_users']; ?></p>

            <p style="margin:0 0 5px 0;"><b>Active Daily Users:</b> <?php echo $appStats['daily_active_users']; ?></p>

            <p><a target="_BLANK"
                  href="http://www.facebook.com/insights/?sk=ao_<?php echo $jfbcLibrary->facebookAppId; ?>">Visit
                Facebook Insights</a> (Facebook login required)</p>
        </fieldset>
    </div>
    <?php
     //SC16
    ?>
<?php if (JRequest::getVar('debug')) : ?>
    <div style="clear:both"></div>
    <div style="float:left">
        <h2>JFBConnect Extension Check</h2>
        <?php
        $app = JFactory::getApplication();
        $version = new JVersion();
        $versionStr = $version->getShortVersion();
        $found15Version = SCStringUtilities::startsWith($versionStr, "1.5.");
        
            if (!$found15Version)
                $app->enqueueMessage("Incorrect version of JFBConnect installed for this version of Joomla", "error");
        
        
        ?>
        <div style="float:left; margin: 0 10px">
            <table>
                <tr>
                    <th>Required Extensions</th>
                    <th>Installed</th>
                    <th>Available</th>
                    <th>Status</th>
                </tr>
                <?php
                echo $versionChecker->_showVersionInfoRow('com_jfbconnect', 'component');
                echo $versionChecker->_showVersionInfoRow('sourcecoast', 'library');
                echo $versionChecker->_showVersionInfoRow('mod_sclogin', 'module');
                echo $versionChecker->_showVersionInfoRow('authentication.jfbconnectauth', 'plugin');
                echo $versionChecker->_showVersionInfoRow('system.jfbcsystem', 'plugin');
                echo $versionChecker->_showVersionInfoRow('user.jfbconnectuser', 'plugin');
                ?>
            </table>
        </div>
        <div style="float:left; margin: 0 10px">
            <table>
                <tr>
                    <th>Social Extensions</th>
                    <th>Installed</th>
                    <th>Available</th>
                    <th>Status</th>
                </tr>
                <?php
                echo $versionChecker->_showVersionInfoRow('content.jfbccontent', 'plugin');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcfan', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbclike', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcsend', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbccomments', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcrecommendations', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcfriends', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcfeed', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcrequest', 'module');
                echo $versionChecker->_showVersionInfoRow('mod_jfbcsubscribe', 'module');
                ?>
            </table>
        </div>
        <div style="float:left; margin: 0 10px">
            <table>
                <tr>
                    <th>Profile Integration</th>
                    <th>Installed</th>
                    <th>Available</th>
                    <th>Status</th>
                </tr>
                <?php
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.communitybuilder', 'plugin');
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.jomsocial', 'plugin');
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.kunena', 'plugin');
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.k2', 'plugin');
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.virtuemart2', 'plugin');

                
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.agora', 'plugin');
                echo $versionChecker->_showVersionInfoRow('jfbcprofiles.virtuemart', 'plugin');
                 //SC15

                echo $versionChecker->_showVersionInfoRow('community.jfbcjsactivity', 'plugin');
                ?>
            </table>
        </div>
    </div>
    <div style="clear:both"></div>
    <img alt="Installed/Published" src="components/com_jfbconnect/assets/images/icon-16-allow.png" width="10"
         height="10"/> - Installed & Published |
    <img alt="Installed/Unpublished" src="components/com_jfbconnect/assets/images/icon-16-notice-note.png" width="10"
         height="10"/> - Not Published |
    <img alt="Not Installed" src="components/com_jfbconnect/assets/images/icon-16-deny.png" width="10" height="10"/> -
    Not Installed
</div>



<div style="margin:0 0 0 10px; padding: 0 10px 10px 10px">
    <h2>Additional Information and Support</h2>
    <ul>
        <li><a target="_blank" href="http://www.sourcecoast.com/jfbconnect/docs/configuration-guide">JFBConnect Setup
            Instructions</a></li>
        <li><a target="_blank" href="http://developers.facebook.com/">Facebook Developer Portal</a></li>
        <li><a target="_blank" href="http://developers.facebook.com/policy/">Facebook Platform Policies</a></li>
    </ul>

    <p>For update information, view the <a target="_blank" href="http://www.sourcecoast.com/jfbconnect/docs/changelog">JFBConnect
        Changelog</a></p>
</div>
<?php endif; ?>
<div style="clear: both"></div>

<form method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_jfbconnect"/>
    <input type="hidden" name="task" value=""/>
</form>
