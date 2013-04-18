<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$db = &JFactory::getDBO();
error_reporting(E_ALL);
// get module settings
$show_date    = (int) $params->get( 'show_date' );
$show_filter  = (int) $params->get( 'show_filter' );
$dformat      = $params->get( 'dateformat' );
$cf           = (int) JRequest::getVar('ualog_filter_id');
$cfo          = JRequest::getVar('ualog_filter_option');
$f = "";

// get logged items
if($show_filter && ($cf || $cfo)) {
    if($cf) {
        $f = "\n WHERE u.id = '$cf'";
        if($cfo) {
            $f.= "\n AND l.option = ".$db->quote($cfo);
        }
    }
    else {
        if($cfo) {
            $f = "\n WHERE l.option = ".$db->quote($cfo);
        }
    }
}

$query = "SELECT l.*, ".$params->get( 'conf_name' )." FROM #__ualog AS l"
       . "\n RIGHT JOIN #__users AS u ON u.id = l.user_id"
       . $f
       . "\n ORDER BY l.cdate DESC LIMIT ".(int) $params->get('limit');
       $db->setQuery($query);
       $rows = $db->loadObjectList();


// start filter
if($show_filter) {
    $query = "SELECT id,name,username FROM #__users WHERE gid >= 23 ORDER BY name,username ASC";
           $db->setQuery($query);
           $fusers = $db->loadObjectList();

    $query = "SELECT `option` FROM #__ualog GROUP BY `option` ORDER BY `option` ASC";
           $db->setQuery($query);
           $foptions = $db->loadResultArray();

    if(!is_array($foptions)) { $foptions = array(); }

    echo "<form action='index.php' method='get' name='ualog_form' id='ualog_form'>";
    echo "<select name='ualog_filter_id' onchange='this.form.submit();'>";
    echo "<option value='0'>".JText::_('Filter by user')."</option>";
    foreach($fusers AS $f)
    {
        $ps = "";

        if($cf == $f->id) { $ps = ' selected="selected"'; }
        echo "<option value='$f->id'$ps>".$f->name." (".$f->username.")</option>";
    }
    echo "</select>&nbsp;";
    echo "<select name='ualog_filter_option' onchange='this.form.submit();'>";
    echo "<option value='0'>".JText::_('Filter by component')."</option>";
    foreach($foptions AS $f)
    {
        $ps = "";

        if($cfo == $f) { $ps = ' selected="selected"'; }
        echo "<option value='$f'$ps>".$f."</option>";
    }
    echo "</select>";
    echo "</form><hr/>";
}

// start item output
echo "<ul class='bullet1'>";

if(!count($rows)) {
    echo "<li>".JText::_("No activity")."</li>";
    $rows = array();
}

foreach($rows AS $row)
{
    if(!$row->action_title) {
        continue;
    }

    JFilterOutput::objectHTMLSafe($row);
    
    $row->action_title = JText::_($row->action_title);
    $row->action_title = str_replace('{user}', "<a href='index.php?option=com_users&view=user&task=edit&cid[]=$row->user_id'>$row->name</a>", $row->action_title);

    if($row->action_link) {
        $ua_link = "<a href='$row->action_link'>$row->item_title</a>";
        $row->action_title = str_replace('{link}', $ua_link, $row->action_title);
    }
    else {
        $row->action_title = str_replace('{link}', $row->item_title, $row->action_title);
    }
    echo "<li>";
    echo $row->action_title;
    if($show_date) { echo "<br/><small class='small'>".date($dformat, $row->cdate)."</small>"; }
    echo "</li>";
}
echo "</ul>";
?>