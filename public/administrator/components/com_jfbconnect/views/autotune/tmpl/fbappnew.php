<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');


?>
<style>
    button {
        width: 170px;
        height: 70px;
        color: #FFFFFF;
        padding: 0px;
        font-weight: bold;
        font-size: 12px;
        float: left;
        margin: 0 20px;
    }
</style>
<div class="width-80 fltlft autotune">
    <form method="post" id="adminForm" name="adminForm">
        <h1>New Application Detected</h1>

        <p>Your Facebook Application looks to be new. Would you like JFBConnect to
            automatically configure most settings for you?</p>
        <p style="height:80px">
            <button type="button" class="autotuneYes" onclick="Joomla.submitbutton('saveAppRecommendations');">Yes, Autoconfigure<br/>(Recommended)</button>
            <button type="button" class="autotuneNo" onclick="Joomla.submitbutton('fbapp');">No, I'll do it myself</button>
        </p>

        <p>Select No if your application has already been configured, or if you want want to manually set all options.</p>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="autotune"/>
        <input type="hidden" name="task" value="saveBasicInfo"/>
    </form>
    <div style="clear:both"></div>
</div>
