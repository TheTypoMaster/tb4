<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function ualog_predict_id($table)
{
    $db = &JFactory::getDBO();

    $query = "SHOW TABLE STATUS LIKE '".$db->replacePrefix($table)."'";
		   $db->setQuery($query);
		   $result = $db->loadAssocList();

	$id = $result[0]['Auto_increment'];

    return $id;
}


function ualog_get_title($id, $table, $field = 'title')
{
    $db = &JFactory::getDBO();

    $where = "id";

    if($table == '#__banner') {
        $where = "bid";
    }

    if($table == '#__bannerclient') {
        $where = "cid";
    }
    
    $query = "SELECT $field FROM $table WHERE id = $id";
           $db->setQuery($query);
           $title = $db->loadResult();

    return htmlspecialchars( $title );
}

function ualog_save( $alink, $atitle, $item )
{
    $db      = &JFactory::getDBO();
    $user    = &JFactory::getUser();
    $time    = time();
    $com     = $db->Quote( JRequest::getVar('option') );
    $task    = $db->Quote( JRequest::getVar('task') );
    $user_id = $db->Quote( $user->id );

    $query = "INSERT INTO #__ualog VALUES("
           . "\n NULL,$user_id,$com,$task,$alink,$atitle,$item,$time)";
           $db->setQuery($query);
           $db->query();

    if($db->getErrorMsg()) {
        die($db->getErrorMsg());
    }
}

$user = &JFactory::getUser();
$db   = &JFactory::getDBO();

// create log table if not exists
$query = "CREATE TABLE IF NOT EXISTS `#__ualog` (
          `id` int(11) NOT NULL auto_increment,
          `user_id` int(11) NOT NULL,
          `option` varchar(255) NOT NULL,
          `task` varchar(255) NOT NULL,
          `action_link` text NOT NULL,
          `action_title` text NOT NULL,
          `item_title` varchar(255) NOT NULL,
          `cdate` int(11) NOT NULL,
          PRIMARY KEY  (`id`))";
$db->setQuery($query);
$db->query();

if($user->id != 0) {
    $com     = JRequest::getVar('option');
    $task    = JRequest::getVar('task');
    $id      = (int) JRequest::getVar('id');
    $user_id = $db->quote( $user->id );
    $time    = $db->quote( time() );
    $cid     = JRequest::getVar('cid', array());
    $scope   = JRequest::getVar('scope');
    $section = JRequest::getVar('section');

    $alink  = $db->quote( "" );
    $atitle = $db->quote( "" );
    $item   = $db->quote( "" );
    $aid    = 0;

    switch($com)
    {
        case 'com_content':
        case 'com_frontpage':
            switch($task)
            {
                case 'save':
                case 'apply':
                    if($id) {
                        $alink  = $db->quote( "index.php?option=com_content&task=edit&cid[]=$id" );
                        $atitle = $db->quote( "{user} updated an article: {link}" );
                        $item   = $db->quote( JRequest::getVar('title') );
                        ualog_save( $alink, $atitle, $item );
                    }
                    else {
                        $id = ualog_predict_id("#__content");
                        $alink  = $db->quote( "index.php?option=com_content&task=edit&cid[]=$id" );
                        $atitle = $db->quote( "{user} created an article: {link}" );
                        $item   = $db->quote( JRequest::getVar('title') );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                case 'publish':
                case 'unpublish':
                case 'archive':
                case 'unarchive':
                    switch($task)
                    {
                        case 'remove':$atitle = $db->quote( "{user} deleted an article: {link}" );break;
                        case 'publish':$atitle = $db->quote( "{user} published an article: {link}" );break;
                        case 'unpublish':$atitle = $db->quote( "{user} unpublished an article: {link}" );break;
                        case 'archive':$atitle = $db->quote( "{user} archived an article: {link}" );break;
                        case 'unarchive':$atitle = $db->quote( "{user} unarchived an article: {link}" );break;
                    }
                    foreach($cid AS $id)
                    {
                        if($task != 'remove' && $task != 'archive') {
                            $alink  = $db->quote( "index.php?option=com_content&task=edit&cid[]=$id" );
                        }
                        $item   = $db->quote(ualog_get_title($id, '#__content') );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_sections':
            switch($scope)
            {
                case 'content':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_sections&scope=content&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a content section: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__sections");
                                $alink  = $db->quote( "index.php?option=com_sections&scope=content&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new content section: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a content section: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a content section: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a content section: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_sections&scope=content&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote(ualog_get_title($id, '#__sections') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;

                
            }
            break;

        case 'com_categories':
            switch($section)
            {
                case 'com_content':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_content&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a content category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__categories");
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_content&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new content category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a content category: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a content category: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a content category: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_categories&section=com_content&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote( ualog_get_title($id, '#__categories') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;

                case 'com_banner':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_banner&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a banner category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__categories");
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_banner&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new banner category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a banner category: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a banner category: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a banner category: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_categories&section=com_banner&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote( ualog_get_title($id, '#__categories') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;

                case 'com_contact_details':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_contact_details&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a contact category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__categories");
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_contact_details&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new contact category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a contact category: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a contact category: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a contact category: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_categories&section=com_contact_details&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote( ualog_get_title($id, '#__categories') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;

                case 'com_newsfeeds':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_newsfeeds&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a feed category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__categories");
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_newsfeeds&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new feed category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a feed category: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a feed category: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a feed category: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_categories&section=com_newsfeeds&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote( ualog_get_title($id, '#__categories') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;

                case 'com_weblinks':
                    switch($task)
                    {
                        case 'save':
                        case 'apply':
                            if($id) {
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_weblinks&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} updated a weblink category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            else {
                                $id = ualog_predict_id("#__categories");
                                $alink  = $db->quote( "index.php?option=com_categories&section=com_weblinks&task=edit&cid[]=$id" );
                                $atitle = $db->quote( "{user} created a new weblink category: {link}" );
                                $item   = $db->quote(JRequest::getVar('title') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;

                        case 'remove':
                        case 'publish':
                        case 'unpublish':
                            switch($task)
                            {
                                case 'remove':$atitle = $db->quote( "{user} deleted a weblink category: {link}" );break;
                                case 'publish':$atitle = $db->quote( "{user} published a weblink category: {link}" );break;
                                case 'unpublish':$atitle = $db->quote( "{user} unpublished a weblink category: {link}" );break;
                            }
                            foreach($cid AS $id)
                            {
                                if($task != 'remove' && $task != 'archive') {
                                    $alink  = $db->quote( "index.php?option=com_categories&section=com_weblinks&task=edit&cid[]=$id" );
                                }
                                $item   = $db->quote( ualog_get_title($id, '#__categories') );
                                ualog_save( $alink, $atitle, $item );
                            }
                            break;
                    }
                    break;
            }
            break;

        case 'com_menus':
            switch($task)
            {
                case 'savemenu':
                    $menutype = JRequest::getVar('menutype');
                    $menutype = str_replace('_','',$menutype);
                    $menutype = str_replace(' ', '-', $menutype);
                    if($menutype && $title) {
                        if($id) {
                            $alink  = $db->quote( "index.php?option=com_menus&task=view&menutype=$menutype" );
                            $atitle = $db->quote( "{user} updated a menu: {link}" );
                            $item   = $db->quote( JRequest::getVar('title') );
                            ualog_save( $alink, $atitle, $item );
                        }
                        else {
                            $alink  = $db->quote( "index.php?option=com_menus&task=view&menutype=$menutype" );
                            $atitle = $db->quote( "{user} created a new menu: {link}" );
                            $item   = $db->quote( JRequest::getVar('title') );
                            ualog_save( $alink, $atitle, $item );
                        }
                        
                    }
                    break;

                case 'doDeleteMenu':
                            $atitle = $db->quote( "{user} deleted a menu: {link}" );
                            $item   = $db->quote( ualog_get_title($id, '#__menu_types') );
                            ualog_save( $alink, $atitle, $item );
                    break;

                case 'remove':
                    foreach($cid AS $id)
                    {
                        $atitle = $db->quote( "{user} deleted a menu item: {link}" );
                        $item   = $db->quote( ualog_get_title($id, '#__menu', 'name') );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == "publish") {
                            $atitle = $db->quote( "{user} published a menu item: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a menu item: {link}" );
                        }
                        $query = "SELECT menutype FROM #__menu WHERE id = '$id'";
                               $db->setQuery($query);
                               $menutype = $db->loadResult();
                               
                        $alink  = $db->quote( "index.php?option=com_menus&task=view&menutype=$menutype" );
                        $item   = $db->quote( ualog_get_title($id, '#__menu', 'name') );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'setdefault':
                    
                    $query = "SELECT menutype FROM #__menu WHERE id = '$cid[0]'";
                               $db->setQuery($query);
                               $menutype = $db->loadResult();
                    
                    $atitle = $db->quote( "{user} set the default menu item: {link}" );
                    $alink  = $db->quote( "index.php?option=com_menus&task=view&menutype=$menutype" );
                    $item   = $db->quote( ualog_get_title($cid[0], '#__menu', 'name') );

                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'save':
                    if($id) {
                        $atitle = $db->quote( "{user} updated a menu item: {link}" );
                    }
                    else {
                        $atitle = $db->quote( "{user} created a menu item: {link}" );
                    }
                    $menutype = JRequest::getVar('menutype');
                    $alink  = $db->quote( "index.php?option=com_menus&task=view&menutype=$menutype" );
                    $item = $db->quote( JRequest::getVar('name') );
                    //die($item."-".$alink."-".$atitle);
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

        case 'com_modules':
            switch($task)
            {
                case 'save':
                case 'apply':
                    $client = (int) JRequest::getVar('client');
                    $module = JRequest::getVar('module');
                    if($id) {
                        $atitle = $db->quote( "{user} updated a module: {link}" );
                    }
                    else {
                        $id = ualog_predict_id('#__modules');
                        $atitle = $db->quote( "{user} created a new module: {link}" );
                    }
                    $alink  = $db->quote( "index.php?option=com_modules&client=$client&task=edit&cid[]=$id" );
                    $item   = $db->quote( JRequest::getVar('title')." ($module)" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    $client = (int) JRequest::getVar('client');
                    foreach($cid AS $id)
                    {
                        if($task == "publish") {
                            $atitle = $db->quote( "{user} published a module: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a module: {link}" );
                        }
                        $query = "SELECT title, module FROM #__modules WHERE id = '$id'";
                               $db->setQuery($query);
                               $result = $db->loadObject();

                        $alink  = $db->quote( "index.php?option=com_modules&client=$client&task=edit&cid[]=$id" );
                        $item   = $db->quote( $result->title." ($result->module)" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_users':
            switch($task)
            {
                case 'remove':
                    foreach($cid AS $id)
                    {
                        $atitle = $db->quote( "{user} deleted a user account: {link}" );
                        $query = "SELECT name, username FROM #__users WHERE id = '$id'";
                               $db->setQuery($query);
                               $result = $db->loadObject();

                        $item   = $db->quote( $result->name." ($result->username)" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'block':
                case 'unblock':
                    foreach($cid AS $id)
                    {
                        $query = "SELECT name, username FROM #__users WHERE id = '$id'";
                               $db->setQuery($query);
                               $result = $db->loadObject();

                        if($task == "unblock") {
                            $atitle = $db->quote( "{user} unblocked a user account: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} blocked a user account: {link}" );
                        }
                        $item  = $db->quote( $result->name." ($result->username)" );
                        $alink = $db->quote( "index.php?option=com_users&view=user&task=edit&cid[]=$id" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'save':
                case 'apply':
                    $name = JRequest::getVar('name');
                    $uname= JRequest::getVar('username');
                    if($id) {
                        $atitle = $db->quote( "{user} updated a user account: {link}" );
                    }
                    else {
                        $id = ualog_predict_id('#__users');
                        $atitle = $db->quote( "{user} created a new user account: {link}" );
                    }
                    $alink = $db->quote( "index.php?option=com_users&view=user&task=edit&cid[]=$id" );
                    $item  = $db->quote( $name." ($uname)" );
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

       case 'com_config':
            switch($task)
            {
                case 'save':
                case 'apply':
                    $atitle = $db->quote( "{user} updated the global configuration" );
                    $item  = $db->quote( "none" );
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

        case 'com_plugins':
            switch($task)
            {
                case 'save':
                case 'apply':
                    $client = JRequest::getVar('client');
                    $atitle = $db->quote( "{user} updated a plugin: {link}" );
                    $alink  = $db->quote( "index.php?option=com_plugins&client=$client&task=edit&cid[]=$id" );
                    $item   = $db->quote( JRequest::getVar('name') );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    $client = JRequest::getVar('client');
                    foreach($cid AS $id)
                    {
                        if($task == "publish") {
                            $atitle = $db->quote( "{user} published a plugin: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a plugin: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_plugins&client=$client&task=edit&cid[]=$id" );
                        $item   = $db->quote( ualog_get_title($id, "#__plugins", "name") );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_templates':
            switch($task)
            {
                case 'save':
                case 'apply':
                    $id = JRequest::getVar('id');
                    $client = JRequest::getVar('client');
                    $atitle = $db->quote( "{user} updated the parameters of a template: {link}" );
                    $alink  = $db->quote( "index.php?option=com_templates&task=edit&cid[]=$id&client=$client" );
                    $item   = $db->quote( $id );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'save_source':
                case 'apply_source':
                    $id = JRequest::getVar('id');
                    $client = JRequest::getVar('client');
                    $atitle = $db->quote( "{user} changed the source code of a template: {link}" );
                    $alink  = $db->quote( "index.php?option=com_templates&task=edit&cid[]=$id&client=$client" );
                    $item   = $db->quote( $id );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'save_css':
                case 'apply_css':
                    $id = JRequest::getVar('id');
                    $client = JRequest::getVar('client');
                    $atitle = $db->quote( "{user} changed the CSS code of a template: {link}" );
                    $alink  = $db->quote( "index.php?option=com_templates&task=edit&cid[]=$id&client=$client" );
                    $item   = $db->quote( $id );
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

        case 'com_trash':
            switch($task)
            {
                case 'restore':
                    $atitle = $db->quote( "{user} restored items from the trash" );
                    $item   = $db->quote( "none" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'delete':
                    $atitle = $db->quote( "{user} deleted items from the trash" );
                    $item   = $db->quote( "none" );
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

        case 'com_checkin':
            $atitle = $db->quote( "{user} checked in all items" );
            $item   = $db->quote( "none" );
            ualog_save( $alink, $atitle, $item );
            break;

        case 'com_cache':
            $atitle = $db->quote( "{user} cleaned/purged the cache" );
            $item   = $db->quote( "none" );
            ualog_save( $alink, $atitle, $item );
            break;

        case 'com_messages':
            switch($task)
            {
                case 'save':
                    $user_id_to = (int) JRequest::getVar('user_id_to');
                    $query = "SELECT name, username FROM #__users WHERE id = '$user_id_to'";
                           $db->setQuery($query);
                           $result = $db->loadObject();
                           
                    $atitle = $db->quote( "{user} has sent a private message to: {link}" );
                    $item   = $db->quote( $result->name." ($result->username)" );
                    $alink  = $db->quote( "index.php?option=com_users&view=user&task=edit&cid[]=$user_id_to" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'remove':
                    $atitle = $db->quote( "{user} deleted private messages" );
                    $item   = $db->quote( "none" );
                    ualog_save( $alink, $atitle, $item );
                    break;
            }
            break;

        case 'com_banners':
            switch($task)
            {
                case 'save':
                case 'apply':
                    $c = JRequest::getVar('c');

                    if($c) {
                        if($id) {
                            $atitle = $db->quote( "{user} updated a banner client: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} created a banner client: {link}" );
                            $id = ualog_predict_id('#__bannerclient');
                        }
                        $item = $db->quote( JRequest::getVar('name') );
                        $alink  = $db->quote( "index.php?option=com_banners&c=client&task=edit&cid[]=$id" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    else {
                        if($id) {
                            $atitle = $db->quote( "{user} updated a banner: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} created a banner: {link}" );
                            $id = ualog_predict_id('#__banner');
                        }
                        $item = $db->quote( JRequest::getVar('name') );
                        $alink  = $db->quote( "index.php?option=com_banners&task=edit&cid[]=$id" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                    $c = JRequest::getVar('c');

                    if($c) {
                        foreach($cid AS $id)
                        {
                            $item = ualog_get_title($id, '#__bannerclient', 'name');
                            $atitle = $db->quote( "{user} deleted a banner client: {link}" );
                            ualog_save( $alink, $atitle, $item );
                        }
                    }
                    else {
                        foreach($cid AS $id)
                        {
                            $item = ualog_get_title($id, '#__banner', 'name');
                            $atitle = $db->quote( "{user} deleted a banner: {link}" );
                            ualog_save( $alink, $atitle, $item );
                        }
                    }
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == 'publish') {
                            $atitle = $db->quote( "{user} published a banner: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a banner: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_banners&task=edit&cid[]=$id" );
                        $item = ualog_get_title($id, '#__banner', 'name');
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_contact':
            switch($task)
            {
                case 'save':
                case 'apply':
                    if($id) {
                        $atitle = $db->quote( "{user} updated a contact: {link}" );
                    }
                    else {
                        $atitle = $db->quote( "{user} created a new contact: {link}" );
                        $id = ualog_predict_id('#__contact_details');
                    }
                    $item = $db->quote( JRequest::getVar('name') );
                    $alink  = $db->quote( "index.php?option=com_contact&task=edit&cid[]=$id" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == 'publish') {
                            $atitle = $db->quote( "{user} published a contact: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a contact: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_contact&task=edit&cid[]=$id" );
                        $item = ualog_get_title($id, '#__contact_details', 'name');
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                    foreach($cid AS $id)
                    {
                        $item = ualog_get_title($id, '#__contact_details', 'name');
                        $atitle = $db->quote( "{user} deleted a contact: {link}" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_newsfeed':
            switch($task)
            {
                case 'save':
                case 'apply':
                    if($id) {
                        $atitle = $db->quote( "{user} updated a news feed: {link}" );
                    }
                    else {
                        $atitle = $db->quote( "{user} created a news feed: {link}" );
                        $id = ualog_predict_id('#__newsfeeds');
                    }
                    $item = $db->quote( JRequest::getVar('name') );
                    $alink  = $db->quote( "index.php?option=com_newsfeeds&task=edit&cid[]=$id" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == 'publish') {
                            $atitle = $db->quote( "{user} published a news feed: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a news feed: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_newsfeeds&task=edit&cid[]=$id" );
                        $item = ualog_get_title($id, '#__newsfeeds', 'name');
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                    foreach($cid AS $id)
                    {
                        $item = ualog_get_title($id, '#__newsfeeds', 'name');
                        $atitle = $db->quote( "{user} deleted a news feed: {link}" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_poll':
            switch($task)
            {
                case 'save':
                case 'apply':
                    if($id) {
                        $atitle = $db->quote( "{user} updated a poll: {link}" );
                    }
                    else {
                        $atitle = $db->quote( "{user} created a poll: {link}" );
                        $id = ualog_predict_id('#__polls');
                    }
                    $item = $db->quote( JRequest::getVar('title') );
                    $alink  = $db->quote( "index.php?option=com_poll&task=edit&cid[]=$id" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == 'publish') {
                            $atitle = $db->quote( "{user} published a poll: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a poll: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_poll&task=edit&cid[]=$id" );
                        $item = ualog_get_title($id, '#__polls', 'title');
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                    foreach($cid AS $id)
                    {
                        $item = ualog_get_title($id, '#__polls', 'title');
                        $atitle = $db->quote( "{user} deleted a poll: {link}" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;

        case 'com_weblinks':
            switch($task)
            {
                case 'save':
                case 'apply':
                    if($id) {
                        $atitle = $db->quote( "{user} updated a weblink: {link}" );
                    }
                    else {
                        $atitle = $db->quote( "{user} created a weblink: {link}" );
                        $id = ualog_predict_id('#__polls');
                    }
                    $item = $db->quote( JRequest::getVar('title') );
                    $alink  = $db->quote( "index.php?option=com_weblinks&view=weblink&task=edit&cid[]=$id" );
                    ualog_save( $alink, $atitle, $item );
                    break;

                case 'publish':
                case 'unpublish':
                    foreach($cid AS $id)
                    {
                        if($task == 'publish') {
                            $atitle = $db->quote( "{user} published a weblink: {link}" );
                        }
                        else {
                            $atitle = $db->quote( "{user} unpublished a weblink: {link}" );
                        }
                        $alink  = $db->quote( "index.php?option=com_weblinks&view=weblink&task=edit&cid[]=$id" );
                        $item = ualog_get_title($id, '#__weblinks', 'title');
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;

                case 'remove':
                    foreach($cid AS $id)
                    {
                        $item = ualog_get_title($id, '#__weblinks', 'title');
                        $atitle = $db->quote( "{user} deleted a weblink: {link}" );
                        ualog_save( $alink, $atitle, $item );
                    }
                    break;
            }
            break;
    }
}
?>