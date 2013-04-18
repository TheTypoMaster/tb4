-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: devdb.mobileactive.net.au
-- Generation Time: Oct 06, 2010 at 05:54 PM
-- Server version: 5.0.77
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `topbetta_application`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_banner`
--

DROP TABLE IF EXISTS `tbdb_banner`;
CREATE TABLE IF NOT EXISTS `tbdb_banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default 'banner',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime default NULL,
  `showBanner` tinyint(1) NOT NULL default '0',
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `custombannercode` text,
  `catid` int(10) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `tags` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`bid`),
  KEY `viewbanner` (`showBanner`),
  KEY `idx_banner_catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_banner`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_bannerclient`
--

DROP TABLE IF EXISTS `tbdb_bannerclient`;
CREATE TABLE IF NOT EXISTS `tbdb_bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `contact` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` time default NULL,
  `editor` varchar(50) default NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_bannerclient`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_bannertrack`
--

DROP TABLE IF EXISTS `tbdb_bannertrack`;
CREATE TABLE IF NOT EXISTS `tbdb_bannertrack` (
  `track_date` date NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_bannertrack`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_categories`
--

DROP TABLE IF EXISTS `tbdb_categories`;
CREATE TABLE IF NOT EXISTS `tbdb_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_components`
--

DROP TABLE IF EXISTS `tbdb_components`;
CREATE TABLE IF NOT EXISTS `tbdb_components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent_option` (`parent`,`option`(32))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `tbdb_components`
--

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1),
(2, 'Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '', 1),
(3, 'Clients', '', 0, 1, 'option=com_banners&c=client', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(4, 'Web Links', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/component.png', 0, 'show_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 1),
(5, 'Links', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '', 1),
(6, 'Categories', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(7, 'Contacts', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/component.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1),
(8, 'Contacts', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '', 1),
(9, 'Categories', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1),
(10, 'Polls', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(11, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(12, 'Feeds', '', 0, 11, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, 'show_headings=1\nshow_name=1\nshow_articles=1\nshow_link=1\nshow_cat_description=1\nshow_cat_items=1\nshow_feed_image=1\nshow_feed_description=1\nshow_item_description=1\nfeed_word_count=0\n\n', 1),
(13, 'Categories', '', 0, 11, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1),
(15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1),
(16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1),
(17, 'Wrapper', 'option=com_wrapper', 0, 0, '', 'Wrapper', 'com_wrapper', 0, '', 1, '', 1),
(18, 'Mail To', '', 0, 0, '', '', 'com_mailto', 0, '', 1, '', 1),
(19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html', 1),
(20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1),
(21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1),
(22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1),
(23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1),
(24, 'Mass mail', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1),
(25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1),
(27, 'Messaging', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1),
(28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1),
(29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1),
(30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1),
(31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1),
(32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1),
(33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_contact_details`
--

DROP TABLE IF EXISTS `tbdb_contact_details`;
CREATE TABLE IF NOT EXISTS `tbdb_contact_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `con_position` varchar(255) default NULL,
  `address` text,
  `suburb` varchar(100) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `postcode` varchar(100) default NULL,
  `telephone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `misc` mediumtext,
  `image` varchar(255) default NULL,
  `imagepos` varchar(20) default NULL,
  `email_to` varchar(255) default NULL,
  `default_con` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `mobile` varchar(255) NOT NULL default '',
  `webpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_contact_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_content`
--

DROP TABLE IF EXISTS `tbdb_content`;
CREATE TABLE IF NOT EXISTS `tbdb_content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `title_alias` varchar(255) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `metadata` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_content`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_content_frontpage`
--

DROP TABLE IF EXISTS `tbdb_content_frontpage`;
CREATE TABLE IF NOT EXISTS `tbdb_content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_content_frontpage`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_content_rating`
--

DROP TABLE IF EXISTS `tbdb_content_rating`;
CREATE TABLE IF NOT EXISTS `tbdb_content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_content_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_acl_aro`
--

DROP TABLE IF EXISTS `tbdb_core_acl_aro`;
CREATE TABLE IF NOT EXISTS `tbdb_core_acl_aro` (
  `id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tbdb_section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `tbdb_gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tbdb_core_acl_aro`
--

INSERT INTO `tbdb_core_acl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', '62', 0, 'Administrator', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_acl_aro_groups`
--

DROP TABLE IF EXISTS `tbdb_core_acl_aro_groups`;
CREATE TABLE IF NOT EXISTS `tbdb_core_acl_aro_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tbdb_gacl_parent_id_aro_groups` (`parent_id`),
  KEY `tbdb_gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `tbdb_core_acl_aro_groups`
--

INSERT INTO `tbdb_core_acl_aro_groups` (`id`, `parent_id`, `name`, `lft`, `rgt`, `value`) VALUES
(17, 0, 'ROOT', 1, 22, 'ROOT'),
(28, 17, 'USERS', 2, 21, 'USERS'),
(29, 28, 'Public Frontend', 3, 12, 'Public Frontend'),
(18, 29, 'Registered', 4, 11, 'Registered'),
(19, 18, 'Author', 5, 10, 'Author'),
(20, 19, 'Editor', 6, 9, 'Editor'),
(21, 20, 'Publisher', 7, 8, 'Publisher'),
(30, 28, 'Public Backend', 13, 20, 'Public Backend'),
(23, 30, 'Manager', 14, 19, 'Manager'),
(24, 23, 'Administrator', 15, 18, 'Administrator'),
(25, 24, 'Super Administrator', 16, 17, 'Super Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_acl_aro_map`
--

DROP TABLE IF EXISTS `tbdb_core_acl_aro_map`;
CREATE TABLE IF NOT EXISTS `tbdb_core_acl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_core_acl_aro_map`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_acl_aro_sections`
--

DROP TABLE IF EXISTS `tbdb_core_acl_aro_sections`;
CREATE TABLE IF NOT EXISTS `tbdb_core_acl_aro_sections` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tbdb_gacl_value_aro_sections` (`value`),
  KEY `tbdb_gacl_hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tbdb_core_acl_aro_sections`
--

INSERT INTO `tbdb_core_acl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', 1, 'Users', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_acl_groups_aro_map`
--

DROP TABLE IF EXISTS `tbdb_core_acl_groups_aro_map`;
CREATE TABLE IF NOT EXISTS `tbdb_core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_core_acl_groups_aro_map`
--

INSERT INTO `tbdb_core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES
(25, '', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_log_items`
--

DROP TABLE IF EXISTS `tbdb_core_log_items`;
CREATE TABLE IF NOT EXISTS `tbdb_core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_core_log_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_core_log_searches`
--

DROP TABLE IF EXISTS `tbdb_core_log_searches`;
CREATE TABLE IF NOT EXISTS `tbdb_core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_core_log_searches`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_groups`
--

DROP TABLE IF EXISTS `tbdb_groups`;
CREATE TABLE IF NOT EXISTS `tbdb_groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_groups`
--

INSERT INTO `tbdb_groups` (`id`, `name`) VALUES
(0, 'Public'),
(1, 'Registered'),
(2, 'Special');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_menu`
--

DROP TABLE IF EXISTS `tbdb_menu`;
CREATE TABLE IF NOT EXISTS `tbdb_menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(75) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `componentid` int(11) unsigned NOT NULL default '0',
  `sublevel` int(11) default '0',
  `ordering` int(11) default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default '0',
  `browserNav` tinyint(4) default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `utaccess` tinyint(3) unsigned NOT NULL default '0',
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL default '0',
  `rgt` int(11) unsigned NOT NULL default '0',
  `home` int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbdb_menu`
--

INSERT INTO `tbdb_menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Home', 'home', 'index.php?option=com_content&view=frontpage', 'component', 1, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'num_leading_articles=1\nnum_intro_articles=4\nnum_columns=2\nnum_links=4\norderby_pri=\norderby_sec=front\nshow_pagination=2\nshow_pagination_results=1\nshow_feed_link=1\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_menu_types`
--

DROP TABLE IF EXISTS `tbdb_menu_types`;
CREATE TABLE IF NOT EXISTS `tbdb_menu_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `menutype` varchar(75) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `menutype` (`menutype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbdb_menu_types`
--

INSERT INTO `tbdb_menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_messages`
--

DROP TABLE IF EXISTS `tbdb_messages`;
CREATE TABLE IF NOT EXISTS `tbdb_messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_messages_cfg`
--

DROP TABLE IF EXISTS `tbdb_messages_cfg`;
CREATE TABLE IF NOT EXISTS `tbdb_messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_messages_cfg`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_migration_backlinks`
--

DROP TABLE IF EXISTS `tbdb_migration_backlinks`;
CREATE TABLE IF NOT EXISTS `tbdb_migration_backlinks` (
  `itemid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `sefurl` text NOT NULL,
  `newurl` text NOT NULL,
  PRIMARY KEY  (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_migration_backlinks`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_modules`
--

DROP TABLE IF EXISTS `tbdb_modules`;
CREATE TABLE IF NOT EXISTS `tbdb_modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(50) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  `control` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `tbdb_modules`
--

INSERT INTO `tbdb_modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(1, 'Main Menu', '', 1, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=mainmenu\nmoduleclass_sfx=_menu\n', 1, 0, ''),
(2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, ''),
(3, 'Popular', '', 3, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_popular', 0, 2, 1, '', 0, 1, ''),
(4, 'Recent added Articles', '', 4, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_latest', 0, 2, 1, 'ordering=c_dsc\nuser_id=0\ncache=0\n\n', 0, 1, ''),
(5, 'Menu Stats', '', 5, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_stats', 0, 2, 1, '', 0, 1, ''),
(6, 'Unread Messages', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_unread', 0, 2, 1, '', 1, 1, ''),
(7, 'Online Users', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_online', 0, 2, 1, '', 1, 1, ''),
(8, 'Toolbar', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', 1, 'mod_toolbar', 0, 2, 1, '', 1, 1, ''),
(9, 'Quick Icons', '', 1, 'icon', 0, '0000-00-00 00:00:00', 1, 'mod_quickicon', 0, 2, 1, '', 1, 1, ''),
(10, 'Logged in Users', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_logged', 0, 2, 1, '', 0, 1, ''),
(11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, ''),
(12, 'Admin Menu', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(14, 'User Status', '', 1, 'status', 0, '0000-00-00 00:00:00', 1, 'mod_status', 0, 2, 1, '', 0, 1, ''),
(15, 'Title', '', 1, 'title', 0, '0000-00-00 00:00:00', 1, 'mod_title', 0, 2, 1, '', 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_modules_menu`
--

DROP TABLE IF EXISTS `tbdb_modules_menu`;
CREATE TABLE IF NOT EXISTS `tbdb_modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_modules_menu`
--

INSERT INTO `tbdb_modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_newsfeeds`
--

DROP TABLE IF EXISTS `tbdb_newsfeeds`;
CREATE TABLE IF NOT EXISTS `tbdb_newsfeeds` (
  `catid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `filename` varchar(200) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `numarticles` int(11) unsigned NOT NULL default '1',
  `cache_time` int(11) unsigned NOT NULL default '3600',
  `checked_out` tinyint(3) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `rtl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_newsfeeds`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_plugins`
--

DROP TABLE IF EXISTS `tbdb_plugins`;
CREATE TABLE IF NOT EXISTS `tbdb_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `tbdb_plugins`
--

INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(2, 'Authentication - LDAP', 'ldap', 'authentication', 0, 2, 0, 1, 0, 0, '0000-00-00 00:00:00', 'host=\nport=389\nuse_ldapV3=0\nnegotiate_tls=0\nno_referrals=0\nauth_method=bind\nbase_dn=\nsearch_string=\nusers_dn=\nusername=\npassword=\nldap_fullname=fullName\nldap_email=mail\nldap_uid=uid\n\n'),
(3, 'Authentication - GMail', 'gmail', 'authentication', 0, 4, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(4, 'Authentication - OpenID', 'openid', 'authentication', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n'),
(6, 'Search - Content', 'content', 'search', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n'),
(7, 'Search - Contacts', 'contacts', 'search', 0, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(8, 'Search - Categories', 'categories', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(9, 'Search - Sections', 'sections', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(10, 'Search - Newsfeeds', 'newsfeeds', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(11, 'Search - Weblinks', 'weblinks', 'search', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(12, 'Content - Pagebreak', 'pagebreak', 'content', 0, 10000, 1, 1, 0, 0, '0000-00-00 00:00:00', 'enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n'),
(13, 'Content - Rating', 'vote', 'content', 0, 4, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(14, 'Content - Email Cloaking', 'emailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'mode=1\n\n'),
(15, 'Content - Code Hightlighter (GeSHi)', 'geshi', 'content', 0, 5, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(16, 'Content - Load Module', 'loadmodule', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'enabled=1\nstyle=0\n\n'),
(17, 'Content - Page Navigation', 'pagenavigation', 'content', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 'position=1\n\n'),
(18, 'Editor - No Editor', 'none', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=en\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=0\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=1\nvisualchars=1\nnonbreaking=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=1\ncontextmenu=1\ninlinepopups=1\nsafari=1\ncustom_plugin=\ncustom_button=\n\n'),
(20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(21, 'Editor Button - Image', 'image', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(22, 'Editor Button - Pagebreak', 'pagebreak', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(23, 'Editor Button - Readmore', 'readmore', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(24, 'XML-RPC - Joomla', 'joomla', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(25, 'XML-RPC - Blogger API', 'blogger', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', 'catid=1\nsectionid=0\n\n'),
(27, 'System - SEF', 'sef', 'system', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(28, 'System - Debug', 'debug', 'system', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n'),
(29, 'System - Legacy', 'legacy', 'system', 0, 3, 0, 1, 0, 0, '0000-00-00 00:00:00', 'route=0\n\n'),
(30, 'System - Cache', 'cache', 'system', 0, 4, 0, 1, 0, 0, '0000-00-00 00:00:00', 'browsercache=0\ncachetime=15\n\n'),
(31, 'System - Log', 'log', 'system', 0, 5, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(32, 'System - Remember Me', 'remember', 'system', 0, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(33, 'System - Backlink', 'backlink', 'system', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(34, 'System - Mootools Upgrade', 'mtupgrade', 'system', 0, 8, 0, 1, 0, 0, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_polls`
--

DROP TABLE IF EXISTS `tbdb_polls`;
CREATE TABLE IF NOT EXISTS `tbdb_polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_polls`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_poll_data`
--

DROP TABLE IF EXISTS `tbdb_poll_data`;
CREATE TABLE IF NOT EXISTS `tbdb_poll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_poll_data`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_poll_date`
--

DROP TABLE IF EXISTS `tbdb_poll_date`;
CREATE TABLE IF NOT EXISTS `tbdb_poll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_poll_date`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_poll_menu`
--

DROP TABLE IF EXISTS `tbdb_poll_menu`;
CREATE TABLE IF NOT EXISTS `tbdb_poll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_poll_menu`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_sections`
--

DROP TABLE IF EXISTS `tbdb_sections`;
CREATE TABLE IF NOT EXISTS `tbdb_sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` text NOT NULL,
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_sections`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_session`
--

DROP TABLE IF EXISTS `tbdb_session`;
CREATE TABLE IF NOT EXISTS `tbdb_session` (
  `username` varchar(150) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `client_id` tinyint(3) unsigned NOT NULL default '0',
  `data` longtext,
  PRIMARY KEY  (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_session`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_stats_agents`
--

DROP TABLE IF EXISTS `tbdb_stats_agents`;
CREATE TABLE IF NOT EXISTS `tbdb_stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_stats_agents`
--


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_templates_menu`
--

DROP TABLE IF EXISTS `tbdb_templates_menu`;
CREATE TABLE IF NOT EXISTS `tbdb_templates_menu` (
  `template` varchar(255) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`menuid`,`client_id`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_templates_menu`
--

INSERT INTO `tbdb_templates_menu` (`template`, `menuid`, `client_id`) VALUES
('rhuk_milkyway', 0, 0),
('khepri', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_users`
--

DROP TABLE IF EXISTS `tbdb_users`;
CREATE TABLE IF NOT EXISTS `tbdb_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`,`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

--
-- Dumping data for table `tbdb_users`
--

INSERT INTO `tbdb_users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES
(62, 'Administrator', 'admin', 'fei.sun@mobileactive.com', 'ed9d7328f2f29cc2c068856d29e63c1c:epP4imlxmWwKjTFq1iCoEmLgRLpJAMBk', 'Super Administrator', 0, 1, 25, '2010-10-06 17:42:14', '0000-00-00 00:00:00', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_weblinks`
--

DROP TABLE IF EXISTS `tbdb_weblinks`;
CREATE TABLE IF NOT EXISTS `tbdb_weblinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `description` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '1',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tbdb_weblinks`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_bet_result_status`
--

DROP TABLE IF EXISTS `tbdb_bet_result_status`;
CREATE TABLE IF NOT EXISTS `tbdb_bet_result_status` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `description` text,
  `status_flag` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO  `tbdb_bet_result_status` (
`id` ,
`name` ,
`description` ,
`status_flag` ,
`created_date` ,
`updated_date`
)
VALUES (
NULL ,  'unresulted', NULL ,  '1', NOW( ) , NOW( )
), (
NULL ,  'paid', NULL ,  '1', NOW( ) , NOW( )
), (
NULL ,  'partially-refunded', NULL ,  '0', NOW( ) , NOW( )
), (
NULL ,  'fully-refunded', NULL ,  '1', NOW( ) , NOW( )
);


-- --------------------------------------------------------

--
-- Table structure for table `tbdb_bet_type`
--

DROP TABLE IF EXISTS `tbdb_bet_type`;
CREATE TABLE IF NOT EXISTS `tbdb_bet_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `description` text,
  `status_flag` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO  `tbdb_bet_type` (
`id` ,
`name` ,
`description` ,
`status_flag` ,
`created_date` ,
`updated_date`
)
VALUES (
NULL ,  'win',  'win bet',  '1', NOW( ) , NOW( )
), (
NULL ,  'place',  'place bet',  '1', NOW( ) , NOW( )
), (
NULL ,  'each way',  'each way bet',  '1', NOW( ) , NOW( )
);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament`
--

DROP TABLE IF EXISTS `tbdb_tournament`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_sport_id` int(11) NOT NULL,
  `parent_tournament_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text,
  `start_currency` int(10) unsigned NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `jackpot_flag` tinyint(1) NOT NULL,
  `buy_in` int(10) unsigned NOT NULL,
  `entry_fee` int(10) unsigned NOT NULL,
  `minimum_prize_pool` int(11) NOT NULL,
  `paid_flag` tinyint(1) NOT NULL,
  `auto_create_flag` tinyint(1) NOT NULL,
  `cancelled_flag` tinyint(1) NOT NULL,
  `cancelled_reason` text NOT NULL,
  `status_flag` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_bet_limit`
--

DROP TABLE IF EXISTS `tbdb_tournament_bet_limit`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_bet_limit` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_id` int(11) NOT NULL,
  `bet_type_id` int(11) NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_racing`
--

DROP TABLE IF EXISTS `tbdb_tournament_racing`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_racing` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_id` int(11) NOT NULL,
  `racing_meeting_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_racing_bet`
--

DROP TABLE IF EXISTS `tbdb_tournament_racing_bet`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_racing_bet` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_ticket_id` int(11) NOT NULL,
  `racing_race_id` int(11) NOT NULL,
  `bet_type_id` int(11) NOT NULL,
  `bet_amount` int(10) unsigned NOT NULL,
  `win_amount` int(10) unsigned NOT NULL,
  `resulted_flag` tinyint(1) NOT NULL,
  `bet_result_status_id` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_racing_bet_selection`
--

DROP TABLE IF EXISTS `tbdb_tournament_racing_bet_selection`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_racing_bet_selection` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_racing_bet_id` int(11) NOT NULL,
  `racing_runner_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_sport`
--

DROP TABLE IF EXISTS `tbdb_tournament_sport`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_sport` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `description` text,
  `status_flag` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO  `tbdb_tournament_sport` (
`id` ,
`name` ,
`description` ,
`status_flag` ,
`created_date` ,
`updated_date`
)
VALUES (
NULL ,  'galloping',  'gallop racing',  '1', NOW( ) , NOW( )
), (
NULL ,  'harness',  'harness racing',  '1', NOW( ) , NOW( )
), (
NULL ,  'greyhounds',  'greyhound racing',  '1', NOW( ) , NOW( )
);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_ticket`
--

DROP TABLE IF EXISTS `tbdb_tournament_ticket`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_ticket` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `entry_fee_transaction_id` int(11) NOT NULL,
  `buy_in_transaction_id` int(11) NOT NULL,
  `result_transaction_id` int(11) NOT NULL,
  `refunded_flag` tinyint(1) NOT NULL,
  `resulted_flag` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `resulted_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `racing_meeting`
--

DROP TABLE IF EXISTS `racing_meeting`;
CREATE TABLE `racing_meeting` (
  `id` int(11) NOT NULL auto_increment,
  `tab_meeting_id` varchar(32) NOT NULL COMMENT 'TAB Meeting ID',
  `name` varchar(64) NOT NULL COMMENT 'Meeting Name',
  `events` int(11) NOT NULL COMMENT 'number of events in meeting',
  `type` varchar(25) NOT NULL COMMENT 'type of meeting gallops/greyhouds/harness',
  `track` varchar(25) NOT NULL COMMENT 'track condition for meeting',
  `weather` varchar(25) NOT NULL COMMENT 'Weather conditions for meeting',
  `date` varchar(64) NOT NULL,
  `atp` tinyint(1) NOT NULL default '0' COMMENT 'IS meeting in ATP',
  `odds_type` varchar(25) NOT NULL,
  `jumpoffset` int(11) NOT NULL default '0' COMMENT 'jump time offset in seconds',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY  (`tab_meeting_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `racing_race`
--

DROP TABLE IF EXISTS `racing_race`;
CREATE TABLE `racing_race` (
  `id` int(32) NOT NULL auto_increment,
  `meeting_id` int(11) NOT NULL COMMENT 'ID of meeting record races are in',
  `tab_race_id` varchar(64) NOT NULL COMMENT 'TAB Race ID',
  `type` varchar(255) NOT NULL COMMENT 'Race Name',
  `win_odds` varchar(512) NOT NULL COMMENT 'Latest approx win odds for race runners',
  `place_odds` varchar(512) NOT NULL COMMENT 'Type of Meeting',
  `location` varchar(255) NOT NULL COMMENT 'Meeting location',
  `number` int(11) NOT NULL COMMENT 'Race Number',
  `name` varchar(255) NOT NULL COMMENT 'Latest approx place odds for race runners',
  `time` varchar(255) NOT NULL default '0' COMMENT 'Time Race Starts',
  `date` int(32) NOT NULL default '0' COMMENT 'Date race is on',
  `distance` varchar(32) NOT NULL COMMENT 'Race distance ',
  `class` varchar(32) NOT NULL COMMENT 'Race Class',
  `time2jump` varchar(32) NOT NULL COMMENT 'time till race starts',
  `status` varchar(32) NOT NULL COMMENT 'race status',
  `dump_timestamp` varchar(32) NOT NULL COMMENT 'timestamp of when the race was written to the DB',
  `start_unixtimestamp` varchar(32) NOT NULL COMMENT 'unixtimestamp of race start time',
  `start_datetime` datetime NOT NULL,
  `dividends_paid` tinyint(1) NOT NULL COMMENT 'whether race has had dividends paid - Not Interim',
  `clients_paid` tinyint(1) NOT NULL default '0' COMMENT 'Set to 1 when clients are paid ',
  `leaderboard_updated` tinyint(1) NOT NULL default '0' COMMENT 'set to 1 when the leaderboard for race has been updted on running tournaments',
  `whatDay` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`),
  UNIQUE KEY `tab_race_id` (`tab_race_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `racing_runner`
--

DROP TABLE IF EXISTS `racing_runner`;
CREATE TABLE `racing_runner` (
  `id` int(32) NOT NULL auto_increment,
  `win_odds` float(10,4) NOT NULL default '0.0000' COMMENT 'Win odds for runner',
  `place_odds` float(10,4) NOT NULL default '0.0000' COMMENT 'Place Odds for runner',
  `race_id` int(32) NOT NULL default '0' COMMENT 'ID of RACE record runners are in',
  `number` varchar(255) NOT NULL COMMENT 'Runner  Number',
  `name` varchar(255) NOT NULL COMMENT 'Runner Name',
  `associate` varchar(25) NOT NULL COMMENT 'Jockey on Rider or Trainer',
  `status` varchar(64) NOT NULL COMMENT 'Status of Runner',
  `barrier` varchar(64) NOT NULL COMMENT 'Barrier runner starts from',
  `handicap` varchar(64) NOT NULL COMMENT 'Runners handicap',
  `ident` varchar(255) NOT NULL COMMENT 'Runner name ident',
  `date` int(32) NOT NULL COMMENT 'Date runner is running',
  `tab_race_id` varchar(32) NOT NULL COMMENT 'what race the runner is in',
  PRIMARY KEY  (`id`),
  KEY `ident` (`ident`),
  KEY `tab_race_id` (`tab_race_id`),
  KEY `race_id` (`race_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_places_paid`
--

DROP TABLE IF EXISTS `tbdb_tournament_places_paid`;
CREATE TABLE `tbdb_tournament_places_paid` (
  `id` int(11) NOT NULL auto_increment,
  `entrants` int(11) NOT NULL COMMENT 'entrants',
  `places_paid` int(11) NOT NULL COMMENT 'number of places paid in tournament',
  `pay_perc` varchar(5000) NOT NULL COMMENT 'list of prize pool percentages paid to each place paid',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_tournament_places_paid`
--

INSERT INTO `tbdb_tournament_places_paid` (`id`, `entrants`, `places_paid`, `pay_perc`, `checked_out`, `checked_out_time`, `ordering`, `published`) VALUES
(1, 4, 1, '100', 0, '0000-00-00 00:00:00', 0, 1),
(2, 9, 2, '70, 30', 0, '0000-00-00 00:00:00', 0, 1),
(3, 21, 3, '50, 30, 20', 0, '0000-00-00 00:00:00', 0, 1),
(4, 29, 4, '45, 30, 15, 10', 0, '0000-00-00 00:00:00', 0, 1),
(5, 39, 5, '40, 27, 15, 10, 8', 0, '0000-00-00 00:00:00', 0, 1),
(6, 49, 6, '37, 25, 15, 10, 7.50, 5.50', 0, '0000-00-00 00:00:00', 0, 1),
(7, 59, 7, '35, 22.50, 15, 10, 7.50, 5.50, 4.50', 0, '0000-00-00 00:00:00', 0, 1),
(8, 69, 9, '30, 20, 15, 10, 7.50, 5.50, 4.50, 4.00, 3.50', 0, '0000-00-00 00:00:00', 0, 1),
(9, 89, 12, '27.55, 18.50, 14, 9.50, 7, 5.25, 4.25, 3.75, 3, 2.40, 2.40, 2.40', 0, '0000-00-00 00:00:00', 0, 1),
(10, 119, 15, '25, 17, 13, 9.25, 6.50, 5.25, 4.25, 3.50, 2.75, 2.30, 2.30, 2.30, 2.20, 2.20, 2.20', 0, '0000-00-00 00:00:00', 0, 1),
(11, 149, 18, '23, 16, 12.15, 9, 6, 5, 4, 3, 2.50, 2.20, 2.20, 2.20, 2.15, 2.15, 2.15, 2.10, 2.10, 2.10', 0, '0000-00-00 00:00:00', 0, 1),
(12, 209, 27, '21.50, 15.50, 11.75, 8.75, 6, 5, 4, 3, 2, 1.75, 1.75, 1.75, 1.50, 1.50, 1.50, 1.25, 1.25, 1.25, 1, 1, 1, 1, 1, 1, 1, 1, 1', 0, '0000-00-00 00:00:00', 0, 1),
(13, 259, 36, '20.50, 15, 11.30, 8.50, 5.90, 4.50, 3.50, 2.50, 1.75, 1.60, 1.60, 1.60, 1.35, 1.35, 1.35, 1.10, 1.10, 1.10, 0.9, 0.9, 0.9, 0.9, 0.9, 0.9, 0.9, 0.9, 0.9, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7', 0, '0000-00-00 00:00:00', 0, 1),
(14, 329, 45, '19.50, 14.75, 11.00, 8.40, 5.75, 4.50, 3.50, 2.50, 1.75, 1.40, 1.40, 1.40, 1.20, 1.20, 1.20, 1.00, 1.00, 1.00, 0.70, 0.70, 0.70, 0.70, 0.70, 0.70, 0.70, 0.70, 0.70, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65, 0.60, 0.60, 0.60  0.60, 0.60, 0.60  0.60, 0.60, 0.60', 0, '0000-00-00 00:00:00', 0, 1),
(15, 399, 54, ' 19.25, 14.30, 10.50, 8.00, 5.60, 4.35, 3.35, 2.35, 1.70, 1.30, 1.30, 1.30, 1.10, 1.10, 1.10, 0.90, 0.90, 0.90, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65, 0.65,0.65, 0.65, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.50, 0.55,  0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50', 0, '0000-00-00 00:00:00', 0, 1),
(16, 499, 63, '19.00, 14.00, 10.25, 7.80, 5.50, 4.25, 3.25, 2.25, 1.66, 1.20, 1.20, 1.20, 1.00, 1.00, 1.00, 0.80, 0.80, 0.80, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.50, 0.50, 0.50, 0.50,  0.50, 0.50, 0.50, 0.50, 0.50, 0.47, 0.47, 0.47, 0.47, 0.47, 0.47, 0.47, 0.47, 0.47, 0.44, 0.44, 0.44, 0.44, 0.44, 0.44, 0.44, 0.44, 0.44', 0, '0000-00-00 00:00:00', 0, 1),
(17, 599, 72, '18.90, 13.85, 10.20, 7.70, 5.40, 4.25, 3.25, 2.25, 1.53, 1.20, 1.20, 1.20, 1.00, 1.00, 1.00, 0.80, 0.80, 0.80, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.60, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.33, 0.33, 0.33, 0.33, 0.33, 0.33, 0.33, 0.33, 0.33', 0, '0000-00-00 00:00:00', 0, 1),
(18, 799, 81, '18.80, 13.70 ,10.10, 7.60, 5.34, 4.25, 3.25, 2.25, 1.50, 1.15, 1.15, 1.15, 0.95, 0.95, 0.95, 0.75, 0.75, 0.75, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.55, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.40, 0.37, 0.37, 0.37, 0.37, 0.37, 0.37, 0.37, 0.37, 0.37, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.35, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31  0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31, 0.31', 0, '0000-00-00 00:00:00', 0, 1),
(19, 999, 90, '18.75, 13.50, 10.00, 7.50, 5.25, 4.25, 3.25, 2.25, 1.41,  1.10, 1.10, 1.10, 0.90, 0.90, 0.90, 0.70, 0.70, 0.70, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50 0.50, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.37, 0.37, 0.37  0.37, 0.37, 0.37, 0.37, 0.37, 0.37, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.32, 0.30  0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30, 0.30', 0, '0000-00-00 00:00:00', 0, 1),
(20, 1000, 99, '18.75, 13.50, 10.00, 7.50, 5.25, 4.25, 3.25, 2.25, 1.81, 1.20, 1.20, 1.20, 0.90, 0.90, 0.90, 0.70, 0.70, 0.70, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.50, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.41, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.34, 0.29, 0.29, 0.29, 0.29, 0.29, 0.29, 0.29, 0.29, 0.29, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.27, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26, 0.26', 0, '0000-00-00 00:00:00', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbdb_tournament_buyin`
--

DROP TABLE IF EXISTS `tbdb_tournament_buyin`;
CREATE TABLE `tbdb_tournament_buyin` (
  `id` int(11) NOT NULL auto_increment,
  `buy_in` float(6,2) NOT NULL COMMENT 'tournament value',
  `entry_fee` float(6,2) NOT NULL COMMENT 'cost to buy into tournament',
  `status_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='buy in values for each tournament value';

--
-- Dumping data for table `tbdb_tournament_buyin`
--

INSERT INTO `tbdb_tournament_buyin` VALUES (1,1.00,0.25,1),(2,2.00,0.50,1),(3,5.00,1.00,0),(4,10.00,2.00,0),(5,20.00,3.00,1),(6,50.00,5.00,1),(7,100.00,10.00,1),(8,200.00,20.00,1),(9,500.00,50.00,1),(10,1000.00,100.00,1),(11,0.00,0.00,1);
