<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$user = &JFactory::getUser();
$templatetheme = $user->getParam('templateTheme');
$options = array(''       => JText::_("Template Theme"),
                 'theme1' => JText::_("Inspired by Adobe"),
                 'theme2' => JText::_("Inspired by Joomla"),
                 'theme3' => JText::_("Inspired by Apple"),
                 'theme4' => JText::_("Inspired by WordPress"),
                 'theme5' => JText::_("Inspired by Microsoft"),
                 'theme6' => JText::_("Inspired by GMail") );
?>
<form id="mytheme" name="mytheme" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
    <select name="templateTheme" id="templateTheme" onchange="document.mytheme.submit()">
        <?php foreach($options AS $v => $l)
        {
           $ps = "";
           if($templatetheme == $v) { $ps = ' selected="selected"'; }
           echo "<option value='$v'$ps>$l</option>";
        }
        ?>
    </select>
</form>
