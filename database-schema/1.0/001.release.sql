
-- ****************** INSERTING MENU DATA START *******************

-- Add menus to the site

TRUNCATE TABLE `tbdb_menu`;

INSERT INTO `tbdb_menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Home', 'home', 'index.php?option=com_tournament', 'component', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1),
(2, 'mainmenu', 'Race Tournaments', 'race-tournaments', 'index.php?option=com_tournament&task=upcomingtournaments', 'url', 1, 0, 0, 0, 2, 2, '2010-09-08 10:19:45', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(3, 'mainmenu', 'Race Betting', 'race-betting', 'index.php?option=com_ucbetman&view=racebetting', 'url', 0, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(4, 'mainmenu', 'Sports Tournaments', 'sports-tournaments', '#', 'url', 0, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(5, 'mainmenu', 'Sports Betting', 'sports-betting', 'http://www.bettasports.com.au/', 'url', 0, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 1, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(6, 'mainmenu', 'My Account', 'my-account', 'index.php?option=com_topbetta_user&view=myaccount', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(7, 'bottom', 'Home', 'home', '/', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(8, 'bottom', 'Terms & Conditions', 'terms-a-conditions', 'index.php?option=com_content&view=article&id=1', 'component', 1, 0, 20, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(9, 'bottom', 'How To Play', 'how-to-play', 'index.php?option=com_content&view=article&id=2', 'component', 1, 0, 20, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(10, 'bottom', 'Contact Us', 'contact-us', 'index.php?option=com_contact&view=contact&id=1', 'component', 1, 0, 7, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_contact_list=0\nshow_category_crumb=0\ncontact_icons=\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0);

-- --------------------------------------------------------

TRUNCATE TABLE `tbdb_menu_types`;

INSERT INTO `tbdb_menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site'),
(2, 'bottom', 'Bottom Menu', 'The bottom menu');

-- --------------------------------------------------------

-- ****************** INSERTING MENU DATA END *******************

-- ****************** INSERTING MODULE DATA START *******************

-- Add modules to the site

TRUNCATE TABLE `tbdb_modules`;

INSERT INTO `tbdb_modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(1, 'Main Menu', '', 0, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menu\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 1, 0, ''),
(2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, ''),
(3, 'Popular', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_popular', 0, 2, 1, '', 0, 1, ''),
(4, 'Recent added Articles', '', 3, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_latest', 0, 2, 1, 'ordering=c_dsc\nuser_id=0\ncache=0\n\n', 0, 1, ''),
(5, 'Menu Stats', '', 4, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_stats', 0, 2, 1, '', 0, 1, ''),
(6, 'Unread Messages', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_unread', 0, 2, 1, '', 1, 1, ''),
(7, 'Online Users', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_online', 0, 2, 1, '', 1, 1, ''),
(8, 'Toolbar', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', 1, 'mod_toolbar', 0, 2, 1, '', 1, 1, ''),
(9, 'Quick Icons', '', 1, 'icon', 0, '0000-00-00 00:00:00', 1, 'mod_quickicon', 0, 2, 1, '', 1, 1, ''),
(10, 'Logged in Users', '', 1, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_logged', 0, 2, 1, '', 0, 1, ''),
(11, 'Footer', '', 1, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, ''),
(12, 'Admin Menu', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(14, 'User Status', '', 1, 'status', 0, '0000-00-00 00:00:00', 1, 'mod_status', 0, 2, 1, '', 0, 1, ''),
(15, 'Title', '', 1, 'title', 0, '0000-00-00 00:00:00', 1, 'mod_title', 0, 2, 1, '', 0, 1, ''),
(16, 'Upcoming Tournaments', '<table class="tournInfo_1" border="0" cellpadding="0" cellspacing="0" width="100%">\r\n	<tr class="sectiontableheader"><td>ID</td><td>DATE</td><td>GAME PLAY</td><td>TOURNAMENT</td><td>SPORT</td><td>BUY IN</td><td>ENTRANTS</td><td>PRIZE POOL</td></tr>\r\n\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry2"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n	<tr class="sectiontableentry1"><td>10023</td><td>4th Sep - 18:32</td><td>JACKPOT</td><td>Sea Eagles V Knights</td><td>RUGBY LEAGUE</td><td>FREE</td><td>10578</td><td>100 Tickets to 10050</td></tr>\r\n</table>', 0, 'newsflash', 0, '0000-00-00 00:00:00', 0, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, ''),
(17, 'Next To Jump', '<table class="tournInfo_1" border="0" cellpadding="0" cellspacing="0" width="100%">\r\n	<tr class="sectiontableheader"><td>-</td><td>-</td><td>-</td></tr>\r\n	<tr class="sectiontableentry1d"><td>-</td><td>-</td><td><i>layout Pending...</i></td></tr>\r\n</table>', 0, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, ''),
(18, 'Core Login', '', 0, 'newsflash', 0, '0000-00-00 00:00:00', 0, 'mod_login', 0, 0, 1, 'cache=0\nmoduleclass_sfx=\npretext=\nposttext=\nlogin=\nlogout=\ngreeting=1\nname=0\nusesecure=0\n\n', 0, 0, ''),
(31, 'My Editor', '', 5, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_myeditor', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 1, ''),
(47, 'Online Users', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_online', 0, 2, 1, '', 1, 1, ''),
(64, 'Coming Soon...', '<center>\r\n<span style="color:#fff;font-size:24px;font-weight:bold;">\r\n<br><br>NEW TOURNAMENT<br><br>Spring racing carnival<br>Starts 17th October<br><br><br>\r\n<br><br>\r\n</span>\r\n</center>', 0, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, ''),
(65, 'BS Core Login', '', 0, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_bslogin', 0, 0, 0, 'cache=0\nmoduleclass_sfx=\npretext=\nposttext=\nlogin=\nlogout=\ngreeting=1\nusesecure=0\n\n', 0, 0, ''),
(66, 'Bottom Menu', '', 0, 'credits', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=bottom\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');

-- --------------------------------------------------------

TRUNCATE TABLE `tbdb_modules_menu`;

INSERT INTO `tbdb_modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0),
(16, 62),
(17, 63),
(18, 0),
(64, 0),
(65, 0),
(66, 0);
-- --------------------------------------------------------
-- ****************** INSERTING MODULE DATA END *******************

-- ****************** INITIAL DATA START *******************

--
-- Dumping data for table `tbdb_categories`
--

TRUNCATE TABLE `tbdb_categories`;
INSERT INTO `tbdb_categories` (`id`, `parent_id`, `title`, `name`, `alias`, `image`, `section`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `editor`, `ordering`, `access`, `count`, `params`) VALUES
(1, 0, 'Bettasports Team', '', 'bettasports-team', '', 'com_contact_details', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '');

-- --------------------------------------------------------

--
-- Dumping data for table `tbdb_components`
--

TRUNCATE TABLE `tbdb_components`;
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
(19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\nallowed_media_usergroup=3\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html\nenable_flash=0\n\n', 1),
(20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1),
(21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1),
(22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1),
(23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, 'administrator=en-GB\nsite=en-GB', 1),
(24, 'Mass mail', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1),
(25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1),
(27, 'Messaging', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1),
(28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1),
(29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1),
(30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1),
(31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1),
(32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1),
(33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1),
(43, 'UC Game Manager', 'option=com_ucbetman', 0, 0, 'option=com_ucbetman', 'uc_betman', 'com_ucbetman', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(58, 'uc_betman', '', 0, 43, 'option=com_uc_betman', 'uc_betman', 'com_uc_betman', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(59, 'BettaSports User Manager', '', 0, 43, 'option=com_ucbetman&controller=user_manager', 'BS User Manager', 'com_ucbetman', 0, '', 0, '', 1),
(60, 'Tournament Wizard', '', 0, 43, 'option=com_uc_betman&controller=tournament_wizard', 'uc_betman', 'com_uc_betman', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(61, 'Punters Challenge Wizard', '', 0, 43, 'option=com_ucbetman&controller=atp_wizard', 'PC Wizard', 'com_ucbetman', 0, '', 0, '', 1);

-- --------------------------------------------------------

--
-- Dumping data for table `tbdb_contact_details`
--

TRUNCATE TABLE `tbdb_contact_details`;
INSERT INTO `tbdb_contact_details` (`id`, `name`, `alias`, `con_position`, `address`, `suburb`, `state`, `country`, `postcode`, `telephone`, `fax`, `misc`, `image`, `imagepos`, `email_to`, `default_con`, `published`, `checked_out`, `checked_out_time`, `ordering`, `params`, `user_id`, `catid`, `access`, `mobile`, `webpage`) VALUES
(1, 'BettaSports Suggestions', 'bettasports-suggestions', 'send us your feedback ... ', '', '', '', '', '', '', '', '', '', NULL, 'play@bettasports.com', 0, 1, 0, '0000-00-00 00:00:00', 1, 'show_name=0\nshow_position=0\nshow_email=0\nshow_street_address=0\nshow_suburb=0\nshow_state=0\nshow_postcode=0\nshow_country=0\nshow_telephone=0\nshow_mobile=0\nshow_fax=0\nshow_webpage=0\nshow_misc=1\nshow_image=1\nallow_vcard=0\ncontact_icons=1\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_email_form=1\nemail_description=Enter your message below...\nshow_email_copy=1\nbanned_email=\nbanned_subject=\nbanned_text=', 0, 1, 0, '', 'http://www.bettasports.com');

-- --------------------------------------------------------

--
-- Dumping data for table `tbdb_content`
--

TRUNCATE TABLE `tbdb_content`;
INSERT INTO `tbdb_content` (`id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) VALUES
(1, 'Tournament Announcements', 'tournament-announcements', '', 'Coming Soon\r\n<br />\r\nSpring racing carnival challenge...', '', 1, 0, 0, 0, '2009-08-19 23:33:55', 62, '', '2009-08-20 01:03:05', 2, 0, '0000-00-00 00:00:00', '2009-08-19 23:33:55', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=0\nshow_create_date=0\nshow_modify_date=\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nlanguage=\nkeyref=\nreadmore=', 5, 0, 2, '', '', 0, 42, 'robots=\nauthor='),
(2, 'Test Layouts', 'test-layouts', '', 'Test Layouts ...', '', 1, 0, 0, 0, '2009-08-19 23:35:05', 2, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '2009-08-19 23:35:05', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 1, '', '', 0, 25, 'robots=\nauthor=');

-- --------------------------------------------------------

--
-- Dumping data for table `tbdb_core_acl_aro_groups`
--

TRUNCATE TABLE `tbdb_core_acl_aro_groups`;
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
-- Dumping data for table `tbdb_core_acl_aro_sections`
--

TRUNCATE TABLE `tbdb_core_acl_aro_sections`;
INSERT INTO `tbdb_core_acl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', 1, 'Users', 0);

-- --------------------------------------------------------
--
-- Dumping data for table `tbdb_groups`
--

TRUNCATE TABLE `tbdb_groups`;
INSERT INTO `tbdb_groups` (`id`, `name`) VALUES
(0, 'Public'),
(1, 'Registered'),
(2, 'Special');

-- --------------------------------------------------------

--
-- Dumping data for table `tbdb_plugins`
--

TRUNCATE TABLE `tbdb_plugins`;
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
(19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'theme=advanced\ncleanup=1\ncleanup_startup=0\nautosave=0\ncompressed=0\nrelative_urls=1\ntext_direction=ltr\nlang_mode=0\nlang_code=en\ninvalid_elements=applet\ncontent_css=1\ncontent_css_custom=\nnewlines=0\ntoolbar=top\nhr=1\nsmilies=1\ntable=1\nstyle=1\nlayer=1\nxhtmlxtras=0\ntemplate=0\ndirectionality=1\nfullscreen=1\nhtml_height=550\nhtml_width=750\npreview=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\n\n'),
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
(34, 'System - User activity log', 'ualog', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(37, 'System - Mootools Upgrade', 'mtupgrade', 'system', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------
--
-- Dumping data for table `tbdb_templates_menu`
--

TRUNCATE TABLE `tbdb_templates_menu`;
INSERT INTO `tbdb_templates_menu` (`template`, `menuid`, `client_id`) VALUES
('topbetta', 0, 0),
('khepri', 0, 1);

-- --------------------------------------------------------

-- ****************** INITIAL DATA END *******************

--
-- topbetta user table
--
CREATE TABLE IF NOT EXISTS `tbdb_topbetta_user` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `title` varchar(5) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `street` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(20) NOT NULL,
  `postcode` int(2) NOT NULL,
  `country` varchar(50) NOT NULL,
  `dob_day` int(2) unsigned NOT NULL,
  `dob_month` int(2) unsigned NOT NULL,
  `dob_year` int(2) unsigned NOT NULL,
  `msisdn` varchar(15) NOT NULL COMMENT 'mobile number',
  `phone_number` varchar(15) NOT NULL,
  `promo_code` varchar(10) default NULL,
  `heard_about` varchar(100) default NULL,
  `heard_about_info` text,
  `homepage` varchar(10) default NULL,
  `marketing_opt_in_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- ****************** TRANSACTION STUFF & PLUGIN STUFF START ****************
--
-- system config plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'System - Config', 'config', 'system', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- table for tbdb_session_tracking
--
CREATE TABLE `tbdb_session_tracking` (
`id` INT( 11 ) NOT NULL auto_increment,
`session_id` VARCHAR( 200 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`remote_ip` VARCHAR( 100 ) NOT NULL ,
`user_agent` VARCHAR( 200 ) NOT NULL ,
`session_start` DATETIME NOT NULL ,
`session_close` DATETIME NULL ,
`last_access` DATETIME NULL ,
`session_close_code_id` INT( 11 ) NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- table for tbdb_session_close_code
--
 CREATE TABLE `tbdb_session_close_code` (
`id` INT( 11 ) NOT NULL auto_increment,
`keyword` VARCHAR( 100 ) NOT NULL,
`name` VARCHAR( 100 ) NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- insert code data
--
INSERT INTO `tbdb_session_close_code` (
`id` ,
`keyword` ,
`name`
)
VALUES (
NULL , 'logout', 'Logged out'
), (
NULL , 'timeout', 'Timed out'
);

--
-- Setup user session plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'User - Session Tracking', 'sessiontracking', 'user', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- Setup system session plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'System - Session Tracking', 'sessiontracking', 'system', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- table for tbdb_withdrawal_type
--
CREATE TABLE `tbdb_withdrawal_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`keyword` VARCHAR( 100 ) NOT NULL ,
`name` VARCHAR( 100 ) NOT NULL ,
`description` VARCHAR( 200 ) NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- table for tbdb_withdrawal_request
--
 CREATE TABLE `tbdb_withdrawal_request` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`requester_id` INT( 11 ) NOT NULL ,
`fulfiller_id` INT( 11 ) NULL ,
`session_tracking_id` INT ( 11 ) NULL ,
`withdrawal_type_id` INT( 11 ) NOT NULL ,
`amount` INT( 10 ) UNSIGNED NOT NULL COMMENT 'Cents based value',
`approved_flag` TINYINT( 1 ) NULL ,
`notes` TEXT NULL ,
`requested_date` DATETIME NOT NULL ,
`fulfilled_date` DATETIME NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- insert withdrawal types
--
INSERT INTO `tbdb_withdrawal_type` (
`id` ,
`keyword` ,
`name` ,
`description`
)
VALUES (
NULL , 'bank', 'bank account', NULL
), (
NULL , 'paypal', 'paypal', NULL
);

--
-- table for tbdb_withdrawal_paypal
--
 CREATE TABLE `tbdb_withdrawal_paypal` (
`withdrawal_request_id` INT( 11 ) NOT NULL ,
`paypal_id` VARCHAR( 100 ) NOT NULL,
 KEY `withdrawal_request_id` (`withdrawal_request_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- Set up payment coponent
--
INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Payment', 'option=com_payment', 0, 0, 'option=com_payment', 'Payment', 'com_payment', 0, '../administrator/components/com_payment/images/payment_icon.png', 0, '', 1);

SET @my_cat_id= LAST_INSERT_ID();

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Payment Withdrawal Requests', '', 0, @my_cat_id, 'option=com_payment&c=withdrawal', 'Payment Withdrawal Requests', 'com_payment', 1, '../administrator/components/com_payment/images/payment_withdrawal_icon.png', 0, '', 1);

--
-- table for tbdb_account_transaction
--
CREATE TABLE `tbdb_account_transaction` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`recipient_id` INT( 11 ) NOT NULL ,
`giver_id` INT( 11 ) NOT NULL ,
`session_tracking_id` INT ( 11 ) NULL ,
`account_transaction_type_id` INT ( 11 ) NOT NULL ,
`amount` INT( 10 ) UNSIGNED NOT NULL COMMENT 'Cents based value',
`notes` TEXT NULL,
`created_date` DATETIME NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- table for tbdb_account_transaction_type
--
 CREATE TABLE `tbdb_account_transaction_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`keyword` VARCHAR( 100 ) NOT NULL ,
`name` VARCHAR( 100 ) NOT NULL ,
`description` VARCHAR( 200 ) NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- insert transaction types
--
INSERT INTO `tbdb_account_transaction_type` (
`id` ,
`keyword` ,
`name` ,
`description`
)
VALUES (
NULL , 'tournamentdollars', 'Tournament Dollars', 'Account Balance is being spent on Tournament Dollars.'
), (
NULL , 'tournamentwin', 'Tournament Win', 'Account Balance is being increased because of a tournament win.'
), (
NULL , 'livebetentry', 'Live Bet Entry', 'Account Balance is being spent on a live bet (not for phase 1).'
), (
NULL , 'livebetwin', 'Live Bet Win', 'Account Balance is being increased because of a live bet win (not for phase 1).'
), (
NULL , 'paypaldeposit', 'PayPal Deposit', 'Account Balance is being increased because of a PayPal deposit.'
), (
NULL , 'ewaydeposit', 'Eway Deposit', 'Account Balance is being increased because of a credit-card deposit via Eway.'
), (
NULL , 'bankdeposit', 'Bank Deposit', 'Account Balance is being increased because of a bank deposit.'
), (
NULL , 'bpaydeposit', 'BPay Deposit', 'Account Balance is being increased because of a BPAY deposit.'
), (
NULL , 'admin', 'Admin', 'Account Balance is being adjusted by an administrator.'
), (
NULL , 'testing', 'Testing', 'Account Balance is being adjusted for testing purposes.'
);

--
-- table for tbdb_paypal_ipn_log
--
 CREATE TABLE `tbdb_paypal_ipn_log` (
`txn_id` varchar(100) NOT NULL,
`user_id` int(11) NOT NULL,
`mc_gross` float NOT NULL,
`payer_email` varchar(100) NOT NULL,
`payer_id` varchar(100) NOT NULL,
`payment_type` varchar(100) NOT NULL,
`payment_status` varchar(100) NOT NULL,
`payment_date` datetime NOT NULL,
`ipn_date` datetime NOT NULL,
`ipn_response` varchar(100) default NULL,
`notification_flag` tinyint(1) NOT NULL,
PRIMARY KEY  (`txn_id`),
INDEX `user_id` (`user_id`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- table for tbdb_eway_request_log
--
 CREATE TABLE `tbdb_eway_request_log` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user_id` INT( 11 ) NOT NULL ,
`total_amount` INT( 10 ) NOT NULL ,
`card_holders_name` VARCHAR( 100 ) NOT NULL ,
`trxn_status` VARCHAR( 100 ) NOT NULL ,
`trxn_reference` INT( 10 ) NOT NULL ,
`auth_code` INT( 10 ) NOT NULL ,
`return_amount` INT( 10 ) NOT NULL ,
`trxn_error` VARCHAR( 200 ) NOT NULL ,
`request_date` DATETIME NOT NULL ,
INDEX ( `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

SELECT (
@com_payment_id := id
) AS id
FROM `tbdb_components`
WHERE link='option=com_payment';

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Account Transactions', '', 0, @com_payment_id, 'option=com_payment&c=account', 'Account Transactions', 'com_payment', 2, '../administrator/components/com_payment/images/account_transaction_icon.png', 0, '', 1);

--
-- Allowed transaction amount to be negative
--
ALTER TABLE `tbdb_account_transaction` CHANGE `amount` `amount` INT( 10 ) NOT NULL COMMENT 'Cents based value';

--
-- Setup account balance plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'System - Account Balance', 'accountbalance', 'system', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- Set up payment configuration section in payment component
--
SELECT (
@com_payment_id := id
) AS id
FROM `tbdb_components`
WHERE link='option=com_payment';

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Configuration', '', 0, @com_payment_id, 'option=com_payment&task=configuration', 'Configuration', 'com_payment', 3, '../administrator/components/com_payment/images/configuration_icon.png', 0, '', 1);

--
-- table for tbdb_tournament_transaction
--
CREATE TABLE `tbdb_tournament_transaction` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`recipient_id` INT( 11 ) NOT NULL ,
`giver_id` INT( 11 ) NOT NULL ,
`session_tracking_id` INT ( 11 ) NULL ,
`tournament_transaction_type_id` INT ( 11 ) NOT NULL ,
`amount` INT( 10 ) NOT NULL COMMENT 'Cents based value',
`notes` TEXT NULL,
`created_date` DATETIME NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- table for tbdb_tournament_transaction_type
--
 CREATE TABLE `tbdb_tournament_transaction_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`keyword` VARCHAR( 100 ) NOT NULL ,
`name` VARCHAR( 100 ) NOT NULL ,
`description` VARCHAR( 200 ) NULL,
`private_flag` TINYINT(1) NOT NULL default '0'
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

--
-- insert transaction types
--
INSERT INTO `tbdb_tournament_transaction_type` (
`id` ,
`keyword` ,
`name` ,
`description` ,
`private_flag`
)
VALUES (
NULL , 'entry', 'Entry', 'The user is spending Tournament Dollars in order to enter a tournament.', '0'
), (
NULL , 'buyin', 'Buy-in', 'The user is spending Tournament Dollars as part of their tournament buy-in.', '0'
), (
NULL , 'win', 'Win', 'The user has won Tournament Dollars in a tournament.', '0'
), (
NULL , 'refund', 'Refund', 'The user has cancelled their tournament entry, or a tournament has been cancelled, and they are being given a refund.', '0'
), (
NULL , 'promo', 'Promo', 'The user has entered a promotional code which gives them an allotment of Tournament Dollars.', '0'
), (
NULL , 'testing', 'Testing', 'Tournament Dollars are being added or removed from a user for testing purposes.', '0'
), (
NULL , 'admin', 'Admin', 'An administrator has given Tournament Dollars to a user. In this case, the administrator should specify the reason in the description.', '0'
);

--
-- Setup tournament dollars plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'System - Tournament Dollars', 'tournamentdollars', 'system', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- Setup tournament dollars xmlrpc plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'XMLRPC - Tournament Dollars', 'tournamentdollars', 'xmlrpc', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

--
-- set up tournament dollars transaction component
--

SELECT (
@com_payment_id := id
) AS id
FROM `tbdb_components`
WHERE link='option=com_payment';

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Tournament Dollars Transactions', '', 0, @com_payment_id, 'option=com_tournamentdollars', 'Tournament Dollars Transactions', 'com_tournamentdollars', 2, '../administrator/components/com_tournamentdollars/images/tournament_transaction_icon.png', 0, '', 1);

--
-- Setup account balance xmlrpc plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'XMLRPC - Account Balance', 'accountbalance', 'xmlrpc', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');

-- ****************** TRANSACTION STUFF & PLUGIN STUFF END ****************

-- ****************** RACING TABLES START **********************
--
-- Table structure for table `racing_meeting`
--

DROP TABLE IF EXISTS `racing_meeting`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
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
  PRIMARY KEY  (`tab_meeting_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `racing_race`
--

DROP TABLE IF EXISTS `racing_race`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `racing_race` (
  `id` int(32) NOT NULL auto_increment,
  `meeting_id` int(11) NOT NULL COMMENT 'ID of meeting record races are in',
  `tab_race_id` varchar(64) NOT NULL COMMENT 'TAB Race ID',
  `type` varchar(255) NOT NULL COMMENT 'Race Name',
  `win_odds` varchar(512) NOT NULL COMMENT 'Latest approx win odds for race runners',
  `place_odds` varchar(512) NOT NULL COMMENT 'Type of Meeting',
  `location` varchar(255) NOT NULL COMMENT 'Meeting location',
  `number` varchar(255) NOT NULL COMMENT 'Race Number',
  `name` varchar(255) NOT NULL COMMENT 'Latest approx place odds for race runners',
  `time` varchar(255) NOT NULL default '0' COMMENT 'Time Race Starts',
  `date` int(32) NOT NULL default '0' COMMENT 'Date race is on',
  `distance` varchar(32) NOT NULL COMMENT 'Race distance ',
  `class` varchar(32) NOT NULL COMMENT 'Race Class',
  `time2jump` varchar(32) NOT NULL COMMENT 'time till race starts',
  `status` varchar(32) NOT NULL COMMENT 'race status',
  `dump_timestamp` varchar(32) NOT NULL COMMENT 'timestamp of when the race was written to the DB',
  `start_unixtimestamp` varchar(32) NOT NULL COMMENT 'unixtimestamp of race start time',
  `dividends_paid` tinyint(1) NOT NULL COMMENT 'whether race has had dividends paid - Not Interim',
  `clients_paid` tinyint(1) NOT NULL default '0' COMMENT 'Set to 1 when clients are paid ',
  PRIMARY KEY  (`id`),
  KEY `date` (`date`),
  KEY `tab_race_id` (`tab_race_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

-- ****************** RACING TABLES END *******************

-- ****************** TOPBETTA COMPONENT START *******************

--
-- Set up topbetta_user component
--
INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Topbetta User', 'option=com_topbetta_user', 0, 0, 'option=com_topbetta_user', 'Payment', 'com_topbetta_user', 0, '../administrator/components/com_topbetta_user/images/topbetta_user.png', 0, '', 1);




-- ****************** TOPBETTA COMPONENT END *******************

--
-- set up the tournament component
--

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Tournament', 'option=com_tournament', 0, 0, 'option=com_tournament', 'Tournament Management', 'com_tournament', 0, '', 0, '', 1);

SELECT (
@com_tournament_id := id
) AS id
FROM `tbdb_components`
WHERE `link`='option=com_tournament';

INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Racing Tournaments', '', 0, @com_tournament_id, 'option=com_tournament&controller=tournamentracing', 'Racing Tournament Management', 'com_tournament', 0, '', 0, '', 1);

CREATE TABLE `tbdb_tournament_leaderboard` (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  tournament_id INT(11) UNSIGNED NOT NULL,
  user_id INT(11) UNSIGNED NOT NULL,
  currency INT(11) UNSIGNED NOT NULL,
  turned_over INT(11) UNSIGNED NOT NULL,
  updated_date DATETIME,
  PRIMARY KEY ( id ),
  KEY ( user_id, tournament_id ),
  KEY ( currency ),
  KEY ( updated_date )
) ENGINE=MyISAM default charset=utf8 ;