<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>

<style type="text/css">
    div.config_setting {
        width: 150px;
    }
    div.config_option {
        width: 450px;
    }
</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div>
        <div style="float:left; width:75%">
            <div class="config_row">
                <div class="config_setting header">Request Setting</div>
                <div class="config_option header">Options</div>
                <div class="config_description header">Description</div>
                <div style="clear:both"></div>
                <div><strong>Note:</strong> You must have a valid SSL certificate for your site and your canvas app must be correctly
            configured in order to successfully send requests.</div>
            </div>
            <div class="config_row">
                <div class="config_setting">Title:</div>
                <div class="config_option">
                    <input id="title" type="text" size="60" name="title" maxlength="50"
                                   value="<?php echo $this->request->title; ?>">
                </div>
                <div class="config_description hasTip"
                    title="Title of the popup window users will see on the frontend when sending the request to friends<br/><br/>
                    <strong>Note:</strong> This setting has no effect on application requests sent from the backend administrator area.">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Message:</div>
                <div class="config_option">
                    <textarea id="message" name="message" rows="3" cols="55"
                                      maxlength="250"><?php echo $this->request->message; ?></textarea>
                </div>
                <div class="config_description hasTip"
                    title="Message users will see on Facebook when viewing the request">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Destination URL:</div>
                <div class="config_option">
                    <input id="destination_url" type="text" size="60" maxlength="250" name="destination_url"
                                   value="<?php echo $this->request->destination_url; ?>">
                </div>
                <div class="config_description hasTip"
                    title="The URL that users will be redirected to when they accept a request in Facebook">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Thank You URL:</div>
                <div class="config_option">
                    <input id="thanks_url" type="text" size="60" maxlength="250" name="thanks_url"
                                   value="<?php echo $this->request->thanks_url; ?>">
                </div>
                <div class="config_description hasTip"
                    title="The URL that users will be redirected to after sending the request to friends<br/><br/>
                    <strong>Note:</strong> This setting has no effect on application requests sent from the backend administrator area.">Info
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="config_row">
                <div class="config_setting">Redirect From Canvas:</div>
                <div class="config_option">
                    <input id="breakout_canvas" type="checkbox" name="breakout_canvas"
                                   <?php echo $this->request->breakout_canvas ? 'checked="checked"' : ""; ?>">
                </div>
                <div class="config_description hasTip"
                    title='<strong>Checked</strong><br/>Your destination URL will not be shown with the Facebook canvas area frameset.<br/><br/>
                        <strong>Unchecked</strong><br/>Your destination URL will be shown within the Facebook canvas area frameset.
                        <br/><br/><strong>Please Note: </strong>Facebook Platform Policy I.12 states:<br/><br/> "The primary purpose of your Canvas or Page Tab app on Facebook must not be to simply redirect users out of the Facebook experience and onto an external site."
                        <br/><br/>Please take caution with this setting. We recommend it is <strong>not</strong> used for all requests.'>Info
                </div>
                <div style="clear:both"></div>
            </div>
            <br/>
            <p>
            Once created, you can insert a request onto your frontend page by using the JBCRequest module or JFBCRequest custom tag.
            Please see <a href="http://www.sourcecoast.com/jfbconnect/docs/facebook-requests" target="_blank">our Facebook Request guide</a> for more details.
            </p>
            <p><strong>JFBCRequest Tag Examples:</strong>
            <ul>
                <li>{JFBCRequest request_id=1 link_text=Invite Friends}</li>
                <li>{JFBCRequest request_id=1 link_image=http://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.png}</li>
            </ul>
            </p>
        </div>
        <div style="float:left; width:25%">
            <table style="width:100%; border:1px dashed silver; padding: 5px; margin: 8px 0 10px 0;">
                    <tbody>
                    <tr>
                        <td width="75px"><strong>Request ID:</strong></td>
                        <td><?php echo $this->request->id ?></td>
                    </tr>
                    <tr>
                        <td><strong>Published:</strong></td>
                        <td><input type="radio" name="published" value="0" id="published0" <?php echo !$this->request->published ? 'checked="checked"' : ""; ?>><label for="published0">No</label>
                            <input type="radio" name="published" value="1" id="published1" <?php echo $this->request->published ? 'checked="checked"' : ""; ?>><label for="published1">Yes</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td><?php echo $this->request->created; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Modified:</strong></td>
                        <td><?php echo $this->request->modified; ?></td>
                    </tr>
                    </tbody>
                </table>
                <table style="width:100%; border:1px dashed silver; padding: 5px; margin: 8px 0 10px 0;>
                    <tbody>
                    <tr colspan="2">
                        <td width="125"><strong>Notifications</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><?php echo $this->totalNotifications;?></td>
                    </tr>
                    <tr>
                        <td><strong>Pending:</strong></td>
                        <td><?php echo $this->pendingNotifications;?></td>
                    </tr>
                    <tr>
                        <td><strong>Read:</strong></td>
                        <td><?php echo $this->readNotifications;?></td>
                    </tr>
                    <tr>
                        <td><strong>Expired:</strong></td>
                        <td><?php echo $this->expiredNotifications;?></td>
                    </tr>
                    <tr>
                        <td style="text-align: center"><a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&requestid='.$this->request->id);?>">See All</a></td>
                    </tr>
                    </tbody>
                </table>
        </div>
        <div style="clear:both"></div>
    </div>
    <input type="hidden" name="option" value="com_jfbconnect"/>
    <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
    <input type="hidden" name="task" value="apply"/>
    <input type="hidden" name="id" value="<?php echo $this->request->id; ?>" />
    <?php echo JHTML::_('form.token'); ?>
</form>