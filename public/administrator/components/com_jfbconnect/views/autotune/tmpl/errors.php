<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');

?>
<div class="width-80 fltlft autotune">
    <form method="post" id="adminForm" name="adminForm">

        <iframe src="<?php echo $this->iframeUrl ?>" width="99%" height="500px" ></iframe>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="task" value="startAutoTune"/>
    </form>
</div>
    <div class="clear:both"></div>