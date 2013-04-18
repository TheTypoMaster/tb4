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
<style>
    .disclaimer {
        margin: 20px 0;
    }

    .disclaimer p.fields {
        border: 1px solid #333;
        background: white;
        padding: 5px;
        margin: 5px 75px;
        text-align: center;
    }

    div.autotune .disclaimer p {
        font-size: 12px;
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
<div class="width-80 fltlft autotune" xmlns="http://www.w3.org/1999/html">
    <form method="post" id="adminForm" name="adminForm">
        <h1>Welcome to the JFBConnect Auto-Configuration Tool!</h1>

        <div class="width-80 fltlft">
            <p>This tool will help you initially setup your
                Facebook Application and should be used periodically to check your configuration. These steps will:</p>
            <ul>
                <li>Automatically configure your Facebook Application based on your site information and up-to-date
                    information and recommendations from SourceCoast.com for JFBConnect.
                </li>
                <li>Let you easily make additional customizations for your Facebook Application to suit your needs.</li>
                <li>Check your site for known configuration or compatibility problems with JFBConnect.</li>
            </ul>
            <p>It's recommended that you run AutoTune whenever you run into issues with Facebook or about once a month
                to make sure your Facebook Application is optimized for
                the latest changes from Facebook.</p>

            <p>When you're ready, click "Start" to begin!</p>

            <div style="text-align: center">
                <input type="submit" value="START" class="autotuneButton"/>
            </div>
            <br/>

            <div class="disclaimer">
                <p><b>Please Note:</b> Autotune sends the following information to SourceCoast.com in order to provide
                    the best results possible:</p>

                <p class="fields">Subscriber ID, Joomla version, JFBConnect version, AutoTune version,
                    site URL.</p>

                <p>The following information will be sent to Facebook.com</p>

                <p class="fields">Facebook Application ID, Facebook Secret Key, any settings to be updated for your
                    application.</p>

                <p>No other information will be communicated with SourceCoast or Facebook. If you do not wish to use
                    Autotune or transmit this information, please Exit now.</p>
            </div>
        </div>
        <div class="width-20 fltlft">
            <fieldset>
                <legend>Basic Checks</legend>
                <table>
                    <tr>
                        <th>Setting</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td><strong>PHP</strong></td>
                        <td><?php echo $this->phpVersion; ?></td>
                    </tr>
                    <tr>
                        <td><strong>cURL</strong></td>
                        <td><?php echo $this->curlCheck; ?></td>
                    </tr>
                </table>
                <?php if ($this->errorsFound)
                echo '<div class="autotuneBad" style="font-size: 15px; text-align: center">Errors Found</div>Please correct the issues above.'; ?>
            </fieldset>
        </div>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="task" value="fbapp"/>
    </form>
</div>