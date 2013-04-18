<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <table width="100%">
        <tbody>
        <tr>
            <td>
                <table class="adminform">
                    <tbody>
                    <tr>
                        <td><label>Receiving Users:</label></td>
                        <td><strong><?php echo $this->totalUsers; ?> users</strong></td>
                    </tr>
                    <tr>
                        <td width="150"><label>Message:</label></td>
                        <td><?php echo $this->request->message; ?></td>
                    </tr>
                    <tr>
                        <td><label>Destination URL:</label></td>
                        <td><?php echo $this->request->destination_url; ?></td>
                    </tr>
                    <tr>
                        <td><label>Redirect From Canvas</label></td>
                        <td><?php echo $this->request->breakout_canvas ? 'Yes' : "No"; ?><br/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="canvasNotice"
                                 style="display:<?php echo $this->request->breakout_canvas ? "visible" : "none" ?>">
                                <strong>Please Note: </strong>Facebook's <a
                                    href="http://developers.facebook.com/policy/">Platform Policy</a> I.12 states:<br/>
                                "The primary purpose of your Canvas or Page Tab app on Facebook must not be to simply
                                redirect users out of the Facebook experience and onto an external site." <br/>
                                Please take caution with this setting. We recommend it is <strong>not</strong> used for
                                all requests.
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <div style="width:100%" id="sendStatus">
        <?php if ($this->sendToAll) : ?>
        You are sending this Request to all Facebook user's that have an account on your site by clicking the link below. <br/> If you wish to send a request to a specific user, please use the User Map area of JFBConnect.<br/><br/>
        <?php endif; ?>
        <div style="text-align:center"><input type="button" style="padding:10px; background:#235D6A; color:#FFFFFF;font-weight: bold;font-size:12px;" onclick='if (confirm("Send this notification to <?php echo $this->totalUsers; ?> of your users?")) jfbcAdmin.request.send(true);' value="Send <?php echo $this->totalUsers; ?> Notifications" /></div>
    </div>
    <input type="hidden" name="option" value="com_jfbconnect"/>
    <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="id" value="<?php echo $this->request->id; ?>"/>
    <?php if (!$this->sendToAll){
            foreach ($this->fbIds as $fbId)
                echo '<input type="hidden" name="fbIds[]" value="'.$fbId.'"/>';
        }
        echo JHTML::_('form.token'); ?>
    <br/><br/>
</form>
