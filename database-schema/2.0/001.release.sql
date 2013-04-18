-- update all right module to not published

UPDATE `tbdb_modules` SET `published` = 0 WHERE `position` = 'right';

-- set up register module

INSERT INTO `tbdb_modules` (
`id` ,
`title` ,
`content` ,
`ordering` ,
`position` ,
`checked_out` ,
`checked_out_time` ,
`published` ,
`module` ,
`numnews` ,
`access` ,
`showtitle` ,
`params` ,
`iscore` ,
`client_id` ,
`control`
)
VALUES (
NULL , 'Register Form', '', '0', 'right', '0', '0000-00-00 00:00:00', '1', 'mod_register', '0', '0', '0', '', '0', '0', ''
);

-- insert register mod to modules menu
SET @rego_mod_id= LAST_INSERT_ID();
INSERT INTO `tbdb_modules_menu` (
`moduleid` ,
`menuid`
)
VALUES (
@rego_mod_id, '0'
);



-- set up private tournament search module

INSERT INTO `tbdb_modules` (
`id` ,
`title` ,
`content` ,
`ordering` ,
`position` ,
`checked_out` ,
`checked_out_time` ,
`published` ,
`module` ,
`numnews` ,
`access` ,
`showtitle` ,
`params` ,
`iscore` ,
`client_id` ,
`control`
)
VALUES (
NULL , 'Private Tournament Search', '', '0', 'right', '0', '0000-00-00 00:00:00', '1', 'mod_private_tournament_search', '0', '0', '0', '', '0', '0', ''
);

-- set up private tournament search module

SET @tourn_mod_id= LAST_INSERT_ID();
INSERT INTO `tbdb_modules_menu` (
`moduleid` ,
`menuid`
)
VALUES (
@tourn_mod_id, '0'
);

-- update menu items
--
-- Table structure for table `tbdb_menu`
--

DROP TABLE IF EXISTS `tbdb_menu`;
CREATE TABLE `tbdb_menu` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbdb_menu`
--

INSERT INTO `tbdb_menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Home', 'home', 'index.php?option=com_tournament', 'component', 1, 0, 0, 0, 2, 62, '2010-11-15 01:01:08', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1),
(2, 'mainmenu', 'Racing Tournaments', 'race-tournaments', 'tournament/racing/cash', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(3, 'mainmenu', 'Jackpot Tournaments', 'jackpot-tournaments', 'tournament/racing/jackpot', 'url', 0, 0, 0, 0, 4, 62, '2010-11-14 22:45:17', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(4, 'mainmenu', 'Sports Tournaments', 'sports-tournaments', '#', 'url', 0, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(5, 'mainmenu', 'Sports Betting', 'sports-betting', 'http://www.bettasports.com.au/', 'url', 0, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 1, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(6, 'mainmenu', 'My Account', 'my-account', 'user/account', 'url', 0, 0, 0, 0, 7, 62, '2010-11-24 04:47:33', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(7, 'bottom', 'Home', 'home', '/', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(8, 'bottom', 'Terms & Conditions', 'terms-a-conditions', 'content/article/2', 'component', 1, 0, 20, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(9, 'bottom', 'How It Works', 'how-to-play', '/how-it-works', 'component', 1, 0, 20, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(10, 'bottom', 'Contact Us', 'contact-us', 'contact-us', 'component', 1, 0, 7, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_contact_list=0\nshow_category_crumb=0\ncontact_icons=\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(11, 'mainmenu', 'Help', 'help', 'help', 'component', 0, 0, 20, 0, 8, 62, '2010-11-25 06:27:40', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(12, 'mainmenu', 'Australia''s Top Punter', 'australia-top-punter', 'index.php?option=com_tournament&task=upcomingtournaments&jackpot=1', 'url', -2, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(13, 'mainmenu', 'Contact Us', 'contact-us', 'contact-us', 'component', 0, 0, 7, 0, 9, 62, '2010-10-28 05:11:18', 0, 0, 0, 0, 'show_contact_list=0\nshow_category_crumb=0\ncontact_icons=\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(14, 'mainmenu', 'How It Works', 'how-it-works', '/how-it-works', 'url', 1, 0, 0, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n', 0, 0, 0),
(15, 'mainmenu', 'Winners List', 'winners-list', 'winners-list', 'url', 1, 0, 0, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);


-- Schema changes for 2.0 --



-- Table for Sport mapping

DROP TABLE IF EXISTS `tbdb_sport_map`;
CREATE TABLE IF NOT EXISTS `tbdb_sport_map` (
  `tournament_sport_id` int unsigned NOT NULL,
  `external_sport_id` int unsigned NOT NULL,
  PRIMARY KEY  (`tournament_sport_id`,`external_sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Table for competition

DROP TABLE IF EXISTS `tbdb_tournament_competition`;
CREATE TABLE `tbdb_tournament_competition` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_sport_id` INT UNSIGNED NOT NULL ,
`external_competition_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL ,
`status_flag` TINYINT( 1 ) NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL,
KEY `sport_external_name` (`tournament_sport_id`,`external_competition_id`,`name`),
KEY `competition_cr_dt` (`created_date`),
KEY `competition_up_dt` (`updated_date`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for match

DROP TABLE IF EXISTS `tbdb_tournament_match`;
CREATE TABLE `tbdb_tournament_match` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_competition_id` INT UNSIGNED NOT NULL ,
`external_meeting_id` INT UNSIGNED NOT NULL ,
`external_match_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL ,
`start_date` DATETIME NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL,
KEY `competition_meeting_name` (`tournament_competition_id`,`external_meeting_id`,`name`),
KEY `competition_st_dt` (`start_date`),
KEY `competition_cr_dt` (`created_date`),
KEY `competition_up_dt` (`updated_date`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for market

DROP TABLE IF EXISTS `tbdb_tournament_market`;
CREATE TABLE `tbdb_tournament_market` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_match_id` INT UNSIGNED NOT NULL ,
`external_market_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL,
`refund_flag` TINYINT( 1 ) NOT NULL,
KEY `match_bet_name` (`tournament_match_id`,`external_market_id`,`name`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament offer

DROP TABLE IF EXISTS `tbdb_tournament_offer`;
CREATE TABLE `tbdb_tournament_offer` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_market_id` INT UNSIGNED NOT NULL ,
`external_offer_id` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL ,
`external_odds` float NOT NULL ,
`override_odds` float NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL,
KEY `market_offer_name` (`tournament_market_id`,`external_offer_id`,`name`),
KEY `competition_cr_dt` (`created_date`),
KEY `competition_up_dt` (`updated_date`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament sport bet

DROP TABLE IF EXISTS `tbdb_tournament_sport_bet`;
CREATE TABLE `tbdb_tournament_sport_bet` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_ticket_id` INT UNSIGNED NOT NULL ,
`tournament_offer_id` INT UNSIGNED NOT NULL ,
`bet_result_status_id` INT UNSIGNED NOT NULL ,
`bet_amount` INT UNSIGNED NOT NULL ,
`win_amount` INT UNSIGNED NOT NULL ,
`odds` float NOT NULL ,
`resulted_flag` TINYINT( 1 ) NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL,
KEY `ticket_offer` (`tournament_ticket_id`,`tournament_offer_id`),
KEY `competition_cr_dt` (`created_date`),
KEY `competition_up_dt` (`updated_date`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament sport event

DROP TABLE IF EXISTS `tbdb_tournament_sport_event`;
CREATE TABLE `tbdb_tournament_sport_event` (
`tournament_id` INT UNSIGNED NOT NULL ,
`tournament_event_id` INT UNSIGNED NOT NULL ,
`closed_betting_on_first_match_flag` TINYINT( 2 ) NOT NULL DEFAULT '0',
`betting_closed_date` DATETIME ,
`reinvest_winnings_flag` TINYINT( 1 ) NOT NULL,
KEY `id_event` (`tournament_id`,`tournament_event_id`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament event

DROP TABLE IF EXISTS `tbdb_tournament_event`;
CREATE TABLE `tbdb_tournament_event` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 64 ) NOT NULL ,
`tournament_competition_id` INT UNSIGNED NOT NULL,
`start_date` DATETIME NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL,
KEY `event_name` (`name`),
KEY `competition_st_dt` (`start_date`),
KEY `competition_cr_dt` (`created_date`),
KEY `competition_up_dt` (`updated_date`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament event match

DROP TABLE IF EXISTS `tbdb_tournament_event_match`;
CREATE TABLE `tbdb_tournament_event_match` (
`tournament_event_id` INT UNSIGNED NOT NULL ,
`tournament_match_id` INT UNSIGNED NOT NULL,
 PRIMARY KEY  (`tournament_event_id`,`tournament_match_id`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8;

-- Table for private tournament

DROP TABLE IF EXISTS `tbdb_tournament_private`;
CREATE TABLE `tbdb_tournament_private` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_id` INT UNSIGNED NOT NULL ,
`tournament_prize_format_id` INT UNSIGNED NOT NULL ,
`user_id` INT UNSIGNED NOT NULL ,
`display_identifier` VARCHAR( 64 ) NOT NULL ,
`password` VARCHAR( 64 ) NOT NULL
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Table for tournament prize format

DROP TABLE IF EXISTS `tbdb_tournament_private_format`;
CREATE TABLE `tbdb_tournament_prize_format` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`keyword` VARCHAR( 100 ) NOT NULL ,
`name` VARCHAR( 100 ) NOT NULL ,
`description` VARCHAR( 200 ) NULL
) ENGINE = MYISAM  DEFAULT CHARACTER SET utf8;

-- Populate tournament prize formats

INSERT INTO `tbdb_tournament_prize_format` (
`id` ,
`keyword` ,
`name` ,
`description`
)
VALUES (
NULL , 'all', 'Winner Takes All', 'The entire prize pool goes to the first place finisher (or divided among tied first place finishers).'
), (
NULL , 'top3', 'Top 3 Finishers', 'The prize pool will be divided as follows: 1st place - 50%, 2nd place - 30%, 3rd place - 20%.'
), (
NULL , 'multiple', 'Multiple Placings', 'The normal cash payout structure dependent on the number of entrants.'
);

-- New Component menu for Sports Event Manager

INSERT INTO `tbdb_components` (
`id` ,
`name` ,
`link` ,
`menuid` ,
`parent` ,
`admin_menu_link` ,
`admin_menu_alt` ,
`option` ,
`ordering` ,
`admin_menu_img` ,
`iscore` ,
`params` ,
`enabled`
)
VALUES (
NULL , 'Sports Tournaments', '', '0', '68', 'option=com_tournament&controller=tournamentsport', 'Sports Tournaments', 'com_tournament', '0', 'js/ThemeOffice/component.png', '0', '', '1'
),(
NULL , 'Sports Event Manager', '', '0', '68', 'option=com_tournament&controller=tournamentsportevent', 'Sports Event Manager', 'com_tournament', '0', 'js/ThemeOffice/component.png', '0', '', '1'
),(
NULL , 'Sports Odds Manager', '', '0', '68', 'option=com_tournament&controller=tournamentsportoffer', 'Sports Odds Manager', 'com_tournament', '0', 'js/ThemeOffice/component.png', '0', '', '1'
);

-- add Sport Tournament to menu 

INSERT INTO `tbdb_menu` (
`id` ,
`menutype` ,
`name` ,
`alias` ,
`link` ,
`type` ,
`published` ,
`parent` ,
`componentid` ,
`sublevel` ,
`ordering` ,
`checked_out` ,
`checked_out_time` ,
`pollid` ,
`browserNav` ,
`access` ,
`utaccess` ,
`params` ,
`lft` ,
`rgt` ,
`home`
)
VALUES (
NULL , 'mainmenu', 'Sports Tournaments', 'sports-tournaments', 'tournament/sports', 'url', '1', '0', '0', '0', '2', '0', '0000-00-00 00:00:00', '0', '0', '0', '0', 'menu_image=-1', '0', '0', '0'
);

--
-- Table structure for table `tbdb_match_status`
--

DROP TABLE IF EXISTS `tbdb_match_status`;
CREATE TABLE `tbdb_match_status` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(255) NOT NULL COMMENT 'A short identifier for the current match status (e.g. selling)',
  `name` varchar(255) NOT NULL COMMENT 'The name of the status',
  `description` varchar(255) NOT NULL COMMENT 'An optional long description for the status',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `tbdb_match_status` (`id`, `keyword`, `name`, `description`) VALUES
(1, 'selling', 'Selling', 'Open for bets'),
(2, 'paying', 'Paying', 'All Paying'),
(3, 'abandoned', 'Abandoned', 'Match abandoned'),
(4, 'paid', 'Paid', 'All bets paid');

ALTER TABLE  `tbdb_tournament_match` ADD  `match_status_id` INT NOT NULL AFTER  `name` ,
ADD  `paid_flag` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `match_status_id` ;

--
-- Table structure for table `tbdb_tournament_offer_result`
--

DROP TABLE IF EXISTS `tbdb_tournament_offer_result`;
CREATE TABLE `tbdb_tournament_offer_result` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_offer_id` int(11) NOT NULL COMMENT 'The offer which has been selected as paying',
  `created_date` DATETIME NOT NULL ,
  `updated_date` DATETIME NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `created_date` (`created_date`),
  KEY `updated_date` (`updated_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



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
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `status_flag` (`status_flag`),
  KEY `created_date` (`created_date`),
  KEY `updated_date` (`updated_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbdb_tournament_sport`
--

INSERT INTO `tbdb_tournament_sport` (`id`, `name`, `description`, `status_flag`, `created_date`, `updated_date`) VALUES
(1, 'galloping', 'gallop racing', 1, '2010-10-09 12:21:54', '2010-10-09 12:21:54'),
(2, 'harness', 'harness racing', 1, '2010-10-09 12:21:54', '2010-10-09 12:21:54'),
(3, 'greyhounds', 'greyhound racing', 1, '2010-10-09 12:21:54', '2010-10-09 12:21:54'),
(4, 'AFL', 'AFL', 1, '2011-02-25 12:05:24', NOW()),
(5, 'Rugby League', 'Rugby League', 1, '2011-02-25 12:06:24', NOW()),
(6, 'Rugby Union', 'Rugby Union', 1, '2011-02-25 12:07:24', NOW()),
(7, 'Soccer', 'Soccer', 1, '2011-02-25 12:08:24', NOW());

--
-- Dumping data for table `tbdb_sport_map`
--
INSERT INTO `tbdb_sport_map` (`tournament_sport_id`, `external_sport_id`) VALUES
(4, 1),
(5, 10),
(6, 9),
(7, 3);

