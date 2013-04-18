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
$pane = JPane::getInstance('tabs');
?>
<script type="text/javascript">
    function toggleHide(rowId, styleType)
    {
        document.getElementById(rowId).style.display = styleType;
    }
</script>
<style type="text/css">
    div.config_setting {
        width: 150px;
    }
    div.config_option {
        width: 300px;
    }
    div.config_setting_option {
        width: 350px;
    }
</style>


<form method="post" id="adminForm" name="adminForm">
<div>
See the full <a href="http://www.sourcecoast.com/jfbconnect/docs/facebook-page-canvas-setup" target="_BLANK"><b>Page Tab & Canvas Configuration Guide</b></a> for more information about these settings.
<br/>
<?php
//print_r($this->canvasProperties);
echo $pane->startPane('content-pane');
echo $pane->startPanel('Application as Page Tab', 'tab_application_settings');

$tabReady = true;
$tabName = $this->canvasProperties->get('page_tab_default_name', "");
if (!$tabName)
{
    $tabName = '<span style="color:#FF4444"><b>Not Set</b></span>';
    $tabReady = false;
}
$tabUrl = $this->canvasProperties->get('page_tab_url', "");
if (!$tabUrl)
{
    $tabUrl = '<span style="color:#FF4444"><b>Not Set</b></span>';
    $tabReady = false;
}
$secureTabUrl = $this->canvasProperties->get('secure_page_tab_url', "");
if (!$secureTabUrl)
{
    $secureTabUrl = '<span style="color:#FF4444"><b>Not Set</b></span>';
    $tabReady = false;
}

$websiteUrl = $this->canvasProperties->get('website_url', '');

?>
<div>
    <div class="config_setting header config_row" style="width:250px">Page Tab Configuration Status</div>
    <div style="clear:both"></div>
    <div class="config_row">
    <?php if ($tabReady)
    { ?>
    Your Page Tab application appears to be setup properly. To add it to your Facebook Page,
    <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo $this->jfbcLibrary->facebookAppId;?>&display=popup&next=<?php echo $websiteUrl;?>" target="_BLANK">Click Here.</a>
    <?php } else
    { ?>
    Your Page Tab application <strong>does not appear</strong> to be correctly configured. To use the Canvas application, please fix any
    errors noted below, and check your application settings.
    <?php } ?>
    </div>
    <br/>
</div>
<div>
    <div class="config_row">
        <div class="config_setting header">Joomla Display Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Display Template:</div>
        <div class="config_option">
            <?php echo JHTML::_('select.genericlist', $this->templates, 'canvas_tab_template', null, 'directory', 'name', $this->canvasTabTemplate, 'canvas_tab_template'); ?>
        </div>
        <div class="config_description hasTip"
            title="Joomla template to be used when your site is viewed in a Tab within a Facebook Page">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Reveal Page Article ID:</div>
        <div class="config_option">
            <input type="text" name="canvas_tab_reveal_article_id"
                   value="<?php echo $model->getSetting('canvas_tab_reveal_article_id') ?>" size="20">
        </div>
        <div class="config_description hasTip"
            title="Article ID you want used as a reveal page. This will force users to Like your Facebook Page before they
            can view the Tab Page URL (below) through Facebook.<br/><br/>
            This article generally shouldn't contain other links to your site and should explain that the user needs
            to Like the page before they can proceed.<br/><br/>
            If left blank, the reveal page will be disabled.">Info
        </div>
        <div class="config_description2">
            See the <a href="http://www.facebook.com/SourceCoast" target="_BLANK">SourceCoast Facebook Page</a> as an example.
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Automatic Resizing:</div>
        <div class="config_option">
            <fieldset id="canvas_tab_resize_enabled" class="radio">
                <input type="radio" id="canvas_tab_resize_enabled1" name="canvas_tab_resize_enabled" value="1" <?php echo $model->getSetting('canvas_tab_resize_enabled') == '1' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_tab_resize_enabled1">Enabled</label>
                <input type="radio" id="canvas_tab_resize_enabled0" name="canvas_tab_resize_enabled" value="0" <?php echo $model->getSetting('canvas_tab_resize_enabled') == '0' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_tab_resize_enabled0">Disabled</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
            title="JFBConnect will attempt to fix your template's width to 520px to fit in the Facebook tab and enable automatic
            height resizing for your page. This can remove the scroll-bars from the right and bottom of the page.<br/>
            This setting is recommended to be enabled for a better user experience, but does not work with all templates.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <br/>
</div>
<div>
    <div class="config_row">
        <div class="config_setting header">Application Setting</div>
        <div class="config_option header">Current Setting</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
        <div>You can change these settings in your
            <b>
            <a target="_BLANK" href="https://developers.facebook.com/apps/<?php echo $this->jfbcLibrary->get('facebookAppId');?>/fb-apps">
                Facebook Application Canvas Settings area</a>
            </b>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting">Tab Name:</div>
        <div class="config_option"><?php echo $tabName; ?>
        </div>
        <div class="config_description hasTip"
             title="This is the default name of the tab when added to a Facebook Page">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Tab Page URL:</div>
        <div class="config_option"><?php echo $tabUrl; ?>
        </div>
        <div class="config_description hasTip"
             title="This is the URL that will initially be shown in the Facebook Page.<br/><br/>
                    Note: If a Reveal Page article ID is specified above, this URL will only be shown after the
                    Facebook Page has been Like'd by the user.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Secure Tab Page URL:</div>
        <div class="config_option"><?php echo $secureTabUrl; ?>
        </div>
        <div class="config_description hasTip"
             title="This is the secure (https) URL that will initially be shown in the Facebook Page
                    Tab when a secure connection is requested.<br/><br/>
                    This value will be required by Facebook as of October 1st, 2011.">Info
        </div>
        <div class="config_description2">
            For more information, see <a href="http://www.sourcecoast.com/jfbconnect/docs/common-support-questions/ssl-certificates-for-facebook" target="_blank">
            Obtaining SSL Certificates for Facebook</a>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel("Canvas App", "canvas_app_settings");

$autoResizingEnabled = $model->getSetting('canvas_canvas_resize_enabled');
$canvasFluidHeight = $this->canvasProperties->get('canvas_fluid_height', false);
if ($canvasFluidHeight)
    $canvasFluidHeight = $autoResizingEnabled ? "Fluid" : 'Fluid<br/><span style="color:#FF4444">Please enable the "Automatic Resizing" setting above</span>';
else
    $canvasFluidHeight = 'Manual/Settable <span style="color:#FF4444">(Not Recommended)</span>';

$canvasFluidWidth = $this->canvasProperties->get('canvas_fluid_width', false);
$canvasFluidWidth = $canvasFluidWidth ? "Fluid" : "Fixed (Fix your template to a width of 760px)";

$canvasReady = true;
$canvasName = $this->canvasProperties->get('namespace', "");
if (!$canvasName)
{
    $canvasReady = false;
    $canvasName = '<span style="color:#FF4444"><b>Not Set</b></span>';
}

$canvasUrl = $this->canvasProperties->get('canvas_url', '');
if ($canvasUrl == "")
{
    $canvasUrl = '<span style="color:#FF4444"><b>Not Set</b></span>';
    $canvasReady = false;
}
$secureCanvasUrl = $this->canvasProperties->get('secure_canvas_url', '');
if ($secureCanvasUrl == "")
{
    $secureCanvasUrl = '<span style="color:#FF4444"><b>Not Set</b></span>';
    $canvasReady = false;
}

if ($canvasReady)
    $canvasLink = '<a target="_blank" href="http://apps.facebook.com/' . $canvasName . '">https://apps.facebook.com/' . $canvasName . '</a>';
else
    $canvasLink = '';



?>
<div>
    <div class="config_setting header config_row" style="width:250px">Canvas Status</div>
    <div style="clear:both"></div>
    <div class="config_row">
    <?php if ($canvasLink)
{ ?>
    Your Canvas application appears to be setup properly. To see what it looks like, click the following link:
    <b><?php echo $canvasLink; ?></b>
    <?php } else
{ ?>
    Your Canvas application <strong>does not appear</strong> to be correctly configured. To use the Canvas application, please fix any
    errors noted below, and check your application settings.<br/>
    <?php } ?>
    </div>
    <br/>
</div>
<div>
        <div class="config_row">
        <div class="config_setting header">Joomla Display Setting</div>
        <div class="config_option header">Options</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Display Template:</div>
        <div class="config_option">
            <?php echo JHTML::_('select.genericlist', $this->templates, 'canvas_canvas_template', null, 'directory', 'name', $this->canvasCanvasTemplate, 'canvas_canvas_template'); ?>
        </div>
        <div class="config_description hasTip"
            title="Joomla template to be used when your site is viewed in a Canvas/Application view on Facebook.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Automatic Resizing:</div>
        <div class="config_option">
            <fieldset id="canvas_canvas_resize_enabled" class="radio">
                <input type="radio" id="canvas_canvas_resize_enabled1" name="canvas_canvas_resize_enabled" value="1" <?php echo $model->getSetting('canvas_canvas_resize_enabled') == '1' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_canvas_resize_enabled1">Enabled</label>
                <input type="radio" id="canvas_canvas_resize_enabled0" name="canvas_canvas_resize_enabled" value="0" <?php echo $model->getSetting('canvas_canvas_resize_enabled') == '0' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_canvas_resize_enabled0">Disabled</label>
            </fieldset>
        </div>
        <div class="config_description hasTip"
            title="Recommended: Enabled.<br/>JFBConnect automatically grow the height of the canvas area according to the content shown.<br/>
            This setting works with the Facebook application setting 'Canvas Height' (see below).">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <br/>
</div>
<div>
    <div class="config_row">
        <div class="config_setting header">Application Setting</div>
        <div class="config_option header">Current Setting</div>
        <div class="config_description header">Description</div>
        <div style="clear:both"></div>
        <div>You can change these settings in your
            <b><a target="_BLANK"
                href="https://developers.facebook.com/apps/<?php echo $this->jfbcLibrary->get('facebookAppId');?>/fb-apps">
                Facebook Application Canvas Settings area
            </a></b>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting">Canvas Name:</div>
        <div class="config_option"><?php echo $canvasName ?>
        </div>
        <div class="config_description hasTip"
            title="URL for your app on Facebook. This value is appended to https://apps.facebook.com/ to specify your unique Canvas URL.<br/><br>">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Canvas URL:</div>
        <div class="config_option"><?php echo $canvasUrl; ?>
        </div>
        <div class="config_description hasTip" title="This is the URL that will initially be shown in the Facebook Canvas.">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Secure Canvas URL:</div>
        <div class="config_option"><?php echo $secureCanvasUrl; ?>
        </div>
        <div class="config_description hasTip"
             title="This is the secure (https) URL that will initially be shown in the Facebook
                    Canvas when a secure connection is requested.<br/><br/>
                    This value will be required by Facebook as of October 1st, 2011. ">Info
        </div>
        <div class="config_description2">
            For more information, see <a href="http://www.sourcecoast.com/jfbconnect/docs/common-support-questions/ssl-certificates-for-facebook" target="_blank">
            Obtaining SSL Certificates for Facebook</a>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Canvas Height:</div>
        <div class="config_option"><?php echo $canvasFluidHeight; ?>
        </div>
        <div class="config_description hasTip" title="Recommended: Fluid.<br/> When set to fluid, and the 'Automatic Resizing' is enabled above, JFBConnect will automatically grow your site in the Facebook Canvas as necessary for the height of your page. <br/><br/>When 'Fixed' is selected, you will need to use Javascript calls to manually set the height for each page on your site">Info
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting">Canvas Width:</div>
        <div class="config_option"><?php echo $canvasFluidWidth; ?>
        </div>
        <div class="config_description hasTip" title="Recommended: Fluid.<br/> This will show your full-width site in the Canvas area. <br/><br/>Optionally can choose 'Fixed' if using a template specifically made for 760px width display areas.">Info
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
echo $pane->endPanel();
echo $pane->endPane();
?>
<input type="hidden" name="option" value="com_jfbconnect"/>
<input type="hidden" name="controller" value="canvas"/>
<input type="hidden" name="cid[]" value="0"/>
<input type="hidden" name="task" value=""/>
<?php echo JHTML::_('form.token'); ?>
</div>
</form>
