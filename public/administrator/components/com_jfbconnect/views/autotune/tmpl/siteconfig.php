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

function showPluginStatus($name, $status)
{
    if ($status)
        echo '<a href="javascript:void(0)" onclick="jfbcAdmin.autotune.enablePlugin(\'' . $name . '\', 0);"><img alt="Installed/Published" src="components/com_jfbconnect/assets/images/icon-16-allow.png"/></a> Published';
    else
        echo '<a href="javascript:void(0)" onclick="jfbcAdmin.autotune.enablePlugin(\'' . $name . '\', 1);"><img alt="Installed/Published" src="components/com_jfbconnect/assets/images/icon-16-deny.png"/></a> Unpublished';
}

?>
<div class="width-80 fltlft autotune">
    <form method="post" id="adminForm" name="adminForm">
        <h1>Site Configuration</h1>

        <p>This section of AutoTune will check your Joomla site configuration for common issues and help ensure
            JFBConnect is ready for operation.</p>

        <h2>Configuration Check</h2>

        <?php if (count($this->joomlaErrors))
        {
            echo '<span class="autotuneBad">Warning: </span>There were '.count($this->joomlaErrors).' potential issue(s) found. Please correct the items below:';
            echo '<ul>';
            foreach ($this->joomlaErrors as $error)
                echo '<li>'.$error.'</li>';
            echo '</ul><br/>';
        }
        else
            echo '<p>No issues found!</p>';
        ?>
        <h2>Plugin Check</h2>

        <p>The following plugins were installed with JFBConnect, and should be enabled for proper operation of
            JFBConnect:</p>

        <table class="options">
            <tr>
                <th>Plugin</th>
                <th>Status</th>
                <th>Description</th>
            </tr>
            <tr>
                <td class="even"><strong>JFBCSystem </strong></td>
                <td class="even"><?php showPluginStatus('jfbcsystem', $this->JFBCSystemEnabled); ?></td>
                <td class="even">Required for all functions of JFBConnect. Should always be enabled.</td>
            </tr>
            <tr>
                <td class="odd"><strong>JFBCAuthentication </strong></td>
                <td class="odd"><?php showPluginStatus('jfbconnectauth', $this->JFBCAuthenticationEnabled); ?></td>
                <td class="odd">Required for Facebook authentication. Publish if you want users to be able to login using Facebook.</td>
            </tr>
            <tr>
                <td class="even"><strong>JFBCUser </strong></td>
                <td class="even"><?php showPluginStatus('jfbconnectuser', $this->JFBCUserEnabled); ?></td>
                <td class="even">Required for Facebook authentication. Publish if you want users to be able to login using Facebook.</td>
            </tr>
            <tr>
                <td class="odd"><strong>JFBCContent </strong></td>
                <td class="odd"><?php showPluginStatus('jfbccontent', $this->JFBCContentEnabled); ?></td>
                <td class="odd">Publish to automatically add social buttons and comment boxes to content.</td>
            </tr>
            <!-- <tr>
                <td colspan="1" class="even">
                    <button type="input" class="autotuneButton">Enable All</button>
                </td>
                <td colspan="1" class="even">
                    <button type="input" class="autotuneButton">Disable All</button>
                </td>
            </tr>-->
        </table>
        <p>Click the icons to enable or disable the plugins.</p>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="pluginName" value=""/>
        <input type="hidden" name="pluginStatus" value=""/>
        <input type="hidden" name="task" value=""/>
    </form>
</div>