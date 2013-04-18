<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
?>
<style>
    .jfbcAutotuneStep {
        background: #62AADD;
        font-size: 20px;
        text-align: center;
        border: 1px solid #FFFFFF;
        padding: 5px 0;
        float: left;
    }

    .jfbcAutotuneStepSelected {
        background: #025A8D;
        color: #FFFFFF;
        font-weight: bold;
    }
</style>
<div class="fltlft autotune" style="width:170px">
    <fieldset class="adminform" style="padding-left:10px; padding-right:5px">
        <legend>Steps</legend>
        <ol>
        <?php

        $allSteps = array(
            'default' => 'Start',
            'basicinfo' => 'Basic Info',
            'fbapp' => 'Facebook App',
            'siteconfig' => 'Site Check',
            'errors' => 'Error Check',
            'finish' => 'Finish'
        );

        $currentLayout = $this->getLayout();
            foreach ($allSteps as $step => $display)
            {
                echo '<li>';
                if ($step == $currentLayout)
                    echo '<strong> >> '. $allSteps[$step] .'</strong>';
                else
                    echo '<a href="index.php?option=com_jfbconnect&view=autotune&task=' . $step . '">' . $allSteps[$step] . '</a></li>';
            }
        ?>
        </ol>
    </fieldset>
</div>