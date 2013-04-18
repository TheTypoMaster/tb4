<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" id="adminForm" name="adminForm">
    <div id="editcell">
        <h2>Select request to send</h2>
        <?php echo $this->requestList; ?>
        <p>A preview for this request to <?php echo count($this->fbIds); ?> user(s) will be shown on the next page.</p>
    </div>
    <br/><br/>

    <input type="hidden" name="option" value="com_jfbconnect"/>
    <input type="hidden" name="task" value="previewSend"/>
    <input type="hidden" name="controller" value="request"/>
    <?php foreach ($this->fbIds as $id)
        echo '<input type="hidden" name="fbIds[]" value="'.$id.'" />';
    ?>
</form>
