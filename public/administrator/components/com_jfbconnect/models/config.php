<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jfbconnect'.DS.'common'.DS.'scconfig.php');

class JFBConnectModelConfig extends JFBCConfig
{
    var $_availableSettings = array();
    var $componentSettings = array(
        'facebook_app_id' => '',
        'facebook_secret_key' => '',
        'create_new_users' => '1',
        'auto_username_format' => '0', //0 = fb_, 1=first.last, 2=firlas
        'generate_random_password' => '1',
        'registration_show_email' => '0',
        'registration_display_mode' => 'horizontal',
        'joomla_skip_newuser_activation' => '1',
        'facebook_new_user_redirect' => "",
        'facebook_new_user_redirect_enable' => '0',
        'facebook_login_redirect' => "",
        'facebook_login_redirect_enable' => "0",
        'facebook_logout_redirect' => "",
        'facebook_logout_redirect_enable' => "0",
        'facebook_perm_custom' => '',
        'facebook_new_user_status_msg' => "",
        'facebook_new_user_status_link' => "",
        'facebook_new_user_status_picture' => "",
        'facebook_login_status_msg' => "",
        'facebook_login_status_link' => "",
        'facebook_login_status_picture' => "",
        'facebook_auto_login' => "0",
        'facebook_display_errors' => '0',
        'facebook_auto_map_by_email' => '0',
        'facebook_curl_disable_ssl' => '0',
        'facebook_language_locale' => '',
        'facebook_login_show_modal' => '0',
        'show_powered_by_link' => '1',
        'affiliate_id' => "",
        'sc_download_id' => "",
        'logout_joomla_only' => '0',
        'show_login_with_joomla_reg' => '1',
        'social_tag_admin_key' => '',
        'social_comment_article_include_ids' => '',
        'social_comment_article_exclude_ids' => '',
        'social_comment_cat_include_type' => '0', //0=ALL, 1=Include, 2=Exclude
        'social_comment_cat_ids' => '',
        'social_comment_sect_include_type' => '0',
        'social_comment_sect_ids' => '',
        'social_comment_article_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_frontpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_section_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_article_comment_max_num' => '10',
        'social_article_comment_width' => '350',
        'social_article_comment_color_scheme' => 'light',
        'social_blog_comment_max_num' => '10',
        'social_blog_comment_width' => '350',
        'social_blog_comment_color_scheme' => 'light',
        'social_k2_comment_item_include_ids' => '',
        'social_k2_comment_item_exclude_ids' => '',
        'social_k2_comment_cat_include_type' => '0', //0=ALL, 1=Include, 2=Exclude
        'social_k2_comment_cat_ids' => '',
        'social_k2_comment_item_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_tag_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_userpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_latest_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_item_comment_max_num' => '10',
        'social_k2_item_comment_width' => '350',
        'social_k2_item_comment_color_scheme' => 'light',
        'social_k2_blog_comment_max_num' => '10',
        'social_k2_blog_comment_width' => '350',
        'social_k2_blog_comment_color_scheme' => 'light',
        'social_like_article_include_ids' => '',
        'social_like_article_exclude_ids' => '',
        'social_like_cat_include_type' => '0',
        'social_like_cat_ids' => '',
        'social_like_sect_include_type' => '0',
        'social_like_sect_ids' => '',
        'social_like_article_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_frontpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_section_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_article_like_layout_style' => 'standard', //standard, box_count or button_count
        'social_article_like_show_faces' => '1', //1=Yes, 0=No
        'social_article_like_show_send_button' => '0', //0=No, 1=Yes
        'social_article_like_width' => '250',
        'social_article_like_verb_to_display' => 'like', //like or recommend
        'social_article_like_font' => 'arial', //arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_article_like_color_scheme' => 'light', //light or dark
        'social_article_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_article_like_show_twitter' => '0', //0=No, 1=Yes
        'social_article_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_blog_like_layout_style' => 'standard', //standard, box_count or button_count
        'social_blog_like_show_faces' => '1', //1=Yes, 0=No
        'social_blog_like_show_send_button' => '0', //0=No, 1=Yes
        'social_blog_like_width' => '250',
        'social_blog_like_verb_to_display' => 'like', //like or recommend
        'social_blog_like_font' => 'arial', //arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_blog_like_color_scheme' => 'light', //light or dark
        'social_blog_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_blog_like_show_twitter' => '0', //0=No, 1=Yes
        'social_blog_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_k2_like_item_include_ids' => '',
        'social_k2_like_item_exclude_ids' => '',
        'social_k2_like_cat_include_type' => '0',
        'social_k2_like_cat_ids' => '',
        'social_k2_like_item_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_tag_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_userpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_latest_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_item_like_layout_style' => 'standard', //standard, box_count or button_count
        'social_k2_item_like_show_faces' => '1', //1=Yes, 0=No
        'social_k2_item_like_show_send_button' => '0', //0=No, 1=Yes
        'social_k2_item_like_width' => '250',
        'social_k2_item_like_verb_to_display' => 'like', //like or recommend
        'social_k2_item_like_font' => 'arial', //arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_k2_item_like_color_scheme' => 'light', //light or dark
        'social_k2_item_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_twitter' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_k2_blog_like_layout_style' => 'standard', //standard, box_count or button_count
        'social_k2_blog_like_show_faces' => '1', //1=Yes, 0=No
        'social_k2_blog_like_show_send_button' => '0', //0=No, 1=Yes
        'social_k2_blog_like_width' => '250',
        'social_k2_blog_like_verb_to_display' => 'like', //like or recommend
        'social_k2_blog_like_font' => 'arial', //arial, lucinda grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_k2_blog_like_color_scheme' => 'light', //light or dark
        'social_k2_blog_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_twitter' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_graph_enabled' => '1',
        'social_graph_fields' => '',
        'social_graph_first_image' => '0',
        'social_graph_first_text' => '0',
        'social_graph_first_text_length' => '100',
        'social_notification_comment_enabled' => '0',
        'social_notification_like_enabled' => '0',
        'social_notification_email_address' => '',
        'social_notification_google_analytics' => '0',
        'social_alphauserpoints_enabled' => '0',

        // Canvas Settings
        'canvas_tab_template' => '-1',
        'canvas_tab_reveal_article_id' => '',
        'canvas_canvas_template' => '-1',
        'canvas_tab_resize_enabled' => '0',
        'canvas_canvas_resize_enabled' => '0',

        // AutoTune
        'autotune_authorization' => '',
        'autotune_field_descriptors' => '',
        'autotune_app_config' => ''
    );
    var $profileFields = array(
        '0' => 'None',
        'name' => 'User - Full Name',
        'first_name' => 'User - First Name',
        'middle_name' => 'User - Middle Name',
        'last_name' => 'User - Last Name',
        'profile_url' => 'User - Profile Link',
        'hometown_location.city' => 'Basic Info - Hometown City',
        'hometown_location.state' => 'Basic Info - Hometown State',
        'hometown_location.country' => 'Basic Info - Hometown Country',
        'hometown_location.name' => 'Basic Info - Hometown City/State', // user_hometown
        'current_location.city' => 'Basic Info - Current City',
        'current_location.state' => 'Basic Info - Current State',
        'current_location.country' => 'Basic Info - Current Country',
        'current_location.name' => 'Basic Info - Current City/State', // user_location
        'timezone' => 'Basic Info - Timezone',
        'sex' => 'Basic Info - Sex (Male / Female)',
        'birthday' => 'Basic Info - Birthday', // user_birthday
        'political' => 'Basic Info - Political View', // user_religion_politics
        'religion' => 'Basic Info - Religious Views', // user_religion_politics
        'about_me' => 'Basic Info - Bio', // user_about_me
        'profile_blurb' => 'Basic Info - Profile Blurb', // user_about_me
        'quotes' => 'Basic Info - Favorite Quotes', // user_about_me
        'music' => 'Likes & Interests - Music', // user_likes
        'books' => 'Likes & Interests - Books', // user_likes
        'movies' => 'Likes & Interests - Movies', // user_likes
        'tv' => 'Likes & Interests - TV', // user_likes
        'games' => 'Likes & Interests - Games', //user_likes
        'activities' => 'Likes & Interests - Activities', // user_activities
        'interests' => 'Likes & Interests - Interests', // user_interests
        'relationship_status' => 'Relationship - Relationship Status', // user_relationships
        //'significant_other_id' => 'Relationship - Significant Other',
        //'meeting_sex' => 'Relationship - Type of Relationship Looking For', // 3.0.2
        //'meeting_for' => 'Relationship - Reasons For Looking', // 3.0.2
        //'affiliations' => 'Network Affiliations', // 3.0.2
        'work.0.employer' => 'Education and Work - Employer', // user_work_history
        'work.0.location' => 'Education and Work - Location', // user_work_history
        //'work.0.location.state' => 'Education and Work - Location State', // user_work_history
        'work.0.position' => 'Education and Work - Position', // user_work_history
        'work.0.start_date' => 'Education and Work - Start Date', // user_work_history
        'work.0.end_date' => 'Education and Work - End Date', // user_work_history
        'education.type:College.school.name' => 'Education and Work - College Name',
        'education.type:College.concentration.0' => 'Education and Work - College Degree',
        'education.type:College.year' => 'Education and Work - College Year',
        //'education.school.name|type.High School' => 'Education and Work - High School', // user_education_history
        'education.type:High School.school.name' => 'Education and Work - High School', // user_education_history
        //'education.year|type.High School' => 'Education and Work - High School Year', // user_education_history
        'education.type:High School.year' => 'Education and Work - High School Year', // user_education_history
        'email' => 'Contact - Email', // email
        'website' => 'Contact - Website' // user_website
    );

    function __construct()
    {
        $this->table = '#__jfbconnect_config';
        $this->_profilePlugin = 'jfbcprofiles';
        $this->_profileSettings = 'jfbcProfilesGetSettings';
        $this->_profileGetPlugin = 'jfbcProfilesGetPlugins';

        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'tables');

        parent::__construct();
    }

    function getPermissionsForFields($fields)
    {
        $perms = array();
        if (!$fields)
            return $perms;

        foreach ($fields as $field)
        {
            if ($field == "current_location.name")
                $perms[] = "user_location";
            else if ($field == "hometown_location.name")
                $perms[] = "user_hometown";
            else if ($field == "activities" || $field == "birthday" || $field == "interests" || $field == "website")
                $perms[] = "user_" . $field;
            else if ($field == "about_me" || $field == "quotes" || $field == "profile_blurb")
                $perms[] = "user_about_me";
            else if ($field == "religion" || $field == "political")
                $perms[] = "user_religion_politics";
            else if ($field == "relationship_status")
                $perms[] = "user_relationships";
            else if ($field == "music" || $field == "books" || $field == "movies" || $field == "tv" || $field == "games")
                $perms[] = "user_likes";
            else if (strpos($field, "work") !== false)
                $perms[] = "user_work_history";
            else if (strpos($field, "education") !== false)
                $perms[] = "user_education_history";
        }
        return $perms;
    }

    /**
     *  Get all permissions that are required by Facebook for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return string Comma separated list of FB permissions that are required
     */
    private $_requiredPermissions;

    function getRequiredPermissions()
    {
        if ($this->_requiredPermissions)
            return $this->_requiredPermissions;

        $this->_requiredPermissions = array();
        $this->_requiredPermissions[] = "email";

        // Check if any of the login/register wall posts are set, which require the publish_stream perm
        if ($this->getSetting('facebook_new_user_status_msg') != "" ||
            $this->getSetting('facebook_new_user_status_link') != "" ||
            $this->getSetting('facebook_new_user_status_picture') != "" ||
            $this->getSetting('facebook_login_status_msg') != "" ||
            $this->getSetting('facebook_login_status_link') != "" ||
            $this->getSetting('facebook_login_status_picture') != ""
        )
            $this->_requiredPermissions[] = "publish_stream";

        JPluginHelper::importPlugin('jfbcprofiles');
        $app = JFactory::getApplication();
        $perms = $app->triggerEvent('jfbcProfilesGetRequiredPermissions');
        if ($perms)
        {
            foreach ($perms as $permArray)
                $this->_requiredPermissions = array_merge($this->_requiredPermissions, $permArray);
        }

        $customPermsSetting = $this->getSetting('facebook_perm_custom');
        if ($customPermsSetting != '')
        {
            //Separate into an array to be able to merge and then take out duplicates
            $customPerms = explode(',', $customPermsSetting);
            foreach ($customPerms as $customPerm)
                $this->_requiredPermissions[] = trim($customPerm);
        }

        $this->_requiredPermissions = array_unique($this->_requiredPermissions);
        $this->_requiredPermissions = implode(",", $this->_requiredPermissions);

        return $this->_requiredPermissions;
    }
}
