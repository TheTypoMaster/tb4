<?php
/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');

$pane = JPane::getInstance('tabs');
?>
<style type="text/css">
    div.config_setting {
        width: 225px;
    }
    div.config_option {
        width: 250px;
    }
    div.config_setting_option {
        width: 350px;
    }
</style>

<form method="post" id="adminForm" name="adminForm">
    
Use the tab(s) below to configure how JFBConnect should integrate with other components on your site.<br/>
<?php
if (count($this->profilePlugins) > 0)
{
    echo $pane->startPane('content-pane');
    foreach ($this->profilePlugins as $profile)
    {
        echo $pane->startPanel(JText::_($profile->getName()), $profile->getName());
        echo $profile->getConfigurationTemplate();
        echo $pane->endPanel();
    }
    echo $pane->endPane();
}
else
{
    echo "<p><b>No JFBConnect Profile plugins are currently enabled.</b></p>";
}
?>

    <input type="hidden" name="option" value="com_jfbconnect" />
    <input type="hidden" name="controller" value="profiles" />
    <input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>

</form>