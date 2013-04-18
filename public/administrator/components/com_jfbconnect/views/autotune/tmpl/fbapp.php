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

$appConfig = $this->appConfig;

$boolOptions = array(
    JHTML::_('select.option', 'enabled', 'Enabled'),
    JHTML::_('select.option', 'disabled', 'Disabled'));
?>
<style>
div.current select {
    margin: 0px;
}

.autotuneButton {
    width: 200px;
}

.autotune_setting {
    width:200px;
    float:left;
}

.autotune_option {
    width:300px;
    float:left;
}

.autotune_description {
    width:9px;
}

.autotune .hasTip {
    background:none;
    color: #000000;
}
</style>
<div class="width-80 fltlft autotune">
    <h1>Facebook Application Configuration</h1>
    <form method="post" id="adminForm" name="adminForm">
        <?php
        if (!isset($appConfig->group))
        {
            echo '<br/><p style="font-size:14px">Field data not found. You will need to make a successful connection to SourceCoast.com to load the most recent Facebook Application data.<br/>';
            echo '<p style="font-size:14px">Please check that your Subscriber ID is correct: <br/><strong>'.$this->subscriberId.'</strong> (<a href="index.php?option=com_jfbconnect&view=autotune&task=basicinfo">Change</a>)</p>';
            echo '<p>If the problem persists, please let us know in our <a href="http://www.sourcecoast.com/forums">support area</a></p>';
        }
        else
        {
            echo '<p>Your Facebook Application information is below. Tabs in red/bold with a number next to them are ones that need
            attention.</p>';
            echo $pane->startPane('content-pane');
            foreach ($appConfig->group as $group)
            {
                if ($group->numRecommendations == 0)
                    echo $pane->startPanel('<span class="autotuneGood">' . ucwords($group->name) . '</span>', $group->name);
                else
                    echo $pane->startPanel('<span class="autotuneBad">' . ucwords($group->name) . ' (' . $group->numRecommendations . ')</span>', $group->name);

                echo '<p>'.$group->description.'</p>';
                ?>

                <div class="config_row">
                    <div class="autotune_setting header"><strong>Application Setting</strong></div>
                    <div class="autotune_option header"><strong>App Setting</strong></div>
                    <div class="autotune_option header"><strong>JFBConnect Recommendation</strong></div>
                    <div style="clear:both"></div>
                </div>
                <?php

                foreach ($group->field as $field)
                {
                    ?>
                    <div class="config_row">
                        <div class="autotune_setting hasTip" title="<?php echo $field->description; ?>"><?php echo $field->display ?></div>
                        <?php if ($field->type == 'image')
                        echo '<div class="autotune_option"><img src="' . $field->value . '" />&nbsp;</div>';
                    else if (isset($field->edit))
                    {
                        if ($field->type == 'text')
                            echo '<div class="autotune_option"><input type="text" name="' . $field->name . '" value="' . $field->value . '" size="45" /></div>';
                        else if ($field->type == 'array')
                            echo '<div class="autotune_option"><input type="text" name="' . $field->name . '" value="' . implode(', ', $field->value) . '" size="45" /></div>';
                        else if ($field->type == "bool")
                            echo '<div class="autotune_option">' . JHTML::_('select.genericlist', $boolOptions, $field->name, null, 'value', 'text', strtolower($field->value)) . '</div>';
                    }
                    else
                        echo '<div class="autotune_option">' . $field->value . '&nbsp;</div>';

                        $recStyle = $field->recommendMet ? 'autotuneGood' : 'autotuneBad';
                        ?>
                        <div class="autotune_option <?php echo $recStyle?>"><?php echo $field->recommend?>&nbsp;</div>
                        <div style="clear:both">
                        </div>
                    </div>
                    <?php
                }
                echo $pane->endPanel();
            }
            echo $pane->endPane();
            ?>
            <br/>
            <p><strong>Facebook App Fetched: </strong><?php echo $this->appConfigUpdated; ?> | <strong>SourceCoast Fields Fetched: </strong><?php echo $this->fieldsUpdated; ?></p>
            <div style="text-align: center">
                <?php 
                    $javascript = 'submitbutton';
                
                
                ?>
                <input type="button" value="Set All Recommendations" class="autotuneButton" onclick="<?php echo $javascript; ?>('saveAppRecommendations');"/>
                <input type="submit" value="Update Application" class="autotuneButton"/>
            </div>
            <br/>
            <strong>Please note:</strong> Not all values from your Facebook application may be visible or editable
            through
            AutoTune. If you need more control over your application, please edit it directly in the <a
                href="http://developers.facebook.com/apps" target="_BLANK">Facebook Developer Area</a>.

            <?php
        } // end of if/else for field descriptors loaded
        ?>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="task" value="saveAppConfig"/>
    </form>
</div>