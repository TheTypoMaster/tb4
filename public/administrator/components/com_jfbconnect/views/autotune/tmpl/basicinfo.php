<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');

$pane = JPane::getInstance('tabs');

?>
<div class="width-80 fltlft autotune">
    <form method="post" id="adminForm" name="adminForm">
        <h1>Basic Information Setup</h1>

        <p>To use the automatic configuration functionality of JFBConnect, please enter the following
            information:</p>

        <div style="text-align:center; font-size:16px">
            <div style="clear:left">
                <div style="width: 300px;float:left; text-align:right;">Facebook Application ID:</div>
                <div style="width:300px;float:left; margin-left:15px;"><input type="text" name="facebook_app_id"
                                                                              size="35"
                                                                              style="font-weight:bold"
                                                                              value="<?php echo $this->fbAppId; ?>"/>
                </div>
            </div>
            <div style="clear:left">
                <div style="width: 300px;float:left; text-align:right;">Facebook Secret Key:</div>
                <div style="width:300px;float:left; margin-left:15px;"><input type="text" name="facebook_secret_key"
                                                                              size="35" style="font-weight:bold"
                                                                              value="<?php echo $this->fbSecretKey; ?>"/>
                </div>
            </div>
            <div style="clear:left">
                <div style="width: 300px;float:left; text-align:right;">SourceCoast Subscriber ID:</div>
                <div style="width:300px;float:left; margin-left:15px;"><input type="text" name="subscriberId" size="35"
                                                                              style="font-weight:bold"
                                                                              value="<?php echo $this->subscriberId; ?>"/>
                </div>
            </div>
            <div style="clear:both"></div>
            <input type="submit" value="Save" class="autotuneButton"/>
        </div>
        <br/>
        <ul>
            <li>Facebook Application ID and Secret are provided by Facebook. To get yours, go to the <a
                    href="https://developers.facebook.com/apps" target="_BLANK">Facebook Developer Area</a>.
            </li>
            <li>SourceCoast Subscriber ID - You can get this ID in the your account area of SourceCoast.com.
                <ul>
                    <li>Your subscriber ID is used to fetch the up-to-date configuration information about Facebook from
                        SourceCoast.com.
                    </li>
                </ul>
            </li>
        </ul>

        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="task" value="saveBasicInfo"/>
    </form>
</div>
