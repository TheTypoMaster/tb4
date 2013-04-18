<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

$subStatus = $this->subStatus;
?>
<div class="clrlft"></div>
<div class="width-100 fltlft autotune">
    <fieldset class="adminform">
        <legend>SourceCoast Subscription Status</legend>
        <div>
            <?php
            foreach ($subStatus as $key => $msg)
            {
                echo '<div class="fltlft" style="margin:0 10px;"><strong>' . ucwords(str_replace("_", " ", $key)) . ":</strong> " . $msg . '</div>';
            }
            echo '<div class="fltlft" style="margin:0 10px;"><strong>Last Checked:</strong> ' . $this->subStatusUpdated . '</div>';
            ?>
        </div>
    </fieldset>
</div>
