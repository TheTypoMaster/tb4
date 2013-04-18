-- 
-- TABLE AND FIELD NAME CHANGES
--
-- tbdb_tournament
ALTER TABLE `tbdb_tournament`
	ADD `event_group_id` INT(11) NOT NULL AFTER `parent_tournament_id`,
	ADD `closed_betting_on_first_match_flag` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status_flag`,
	ADD `betting_closed_date` DATETIME NULL AFTER `closed_betting_on_first_match_flag`,
	ADD `reinvest_winnings_flag` TINYINT(1) NOT NULL DEFAULT 0 AFTER `betting_closed_date`;

-- tbdb_event_group
ALTER TABLE `tbdb_tournament_event`
  RENAME TO `tbdb_event_group`,
  ADD `external_event_group_id` VARCHAR(64) NULL AFTER `id`,
  ADD `display_flag` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `start_date` ;

-- tbdb_event_group_event
ALTER TABLE `tbdb_tournament_event_match`
  RENAME TO `tbdb_event_group_event`,
  CHANGE `tournament_event_id` `event_group_id` INT UNSIGNED NOT NULL,
  CHANGE `tournament_match_id` `event_id` INT UNSIGNED NOT NULL;

-- populate external event group id
UPDATE `tbdb_event_group` eg
	LEFT JOIN 
		`tbdb_event_group_event` ege ON ege.`event_group_id` = eg.id
	LEFT JOIN
		`tbdb_tournament_match` e ON e.id = ege.event_id
SET `external_event_group_id` = external_meeting_id;

-- tbdb_event
ALTER TABLE `tbdb_tournament_match`
  RENAME TO `tbdb_event`,
  CHANGE `external_match_id` `external_event_id` INT UNSIGNED NOT NULL,
  CHANGE `match_status_id` `event_status_id` INT UNSIGNED NOT NULL,
  DROP `external_meeting_id`;
  
ALTER TABLE `tbdb_event` ENGINE = InnoDB;
  
-- tbdb_event_status
ALTER TABLE `tbdb_match_status`
	RENAME TO `tbdb_event_status`;

UPDATE `tbdb_event_status` SET `description` = 'Event Abandoned' WHERE `keyword` = 'abandoned';

-- tbdb_tournament_event_group
ALTER TABLE `tbdb_tournament_sport_event`
  RENAME TO `tbdb_tournament_event_group`,
  CHANGE `tournament_event_id` `event_group_id` INT UNSIGNED NOT NULL;
  
UPDATE `tbdb_tournament`,`tbdb_tournament_event_group` SET tbdb_tournament.`event_group_id` = tbdb_tournament_event_group.`event_group_id`
WHERE tbdb_tournament.id = tbdb_tournament_event_group.tournament_id;

-- tbdb_market
ALTER TABLE `tbdb_tournament_market`
  RENAME TO `tbdb_market`,
  CHANGE `tournament_match_id` `event_id` INT UNSIGNED NOT NULL,
  CHANGE `bet_type_id` `market_type_id` INT UNSIGNED NOT NULL;

-- tbdb_market_type
ALTER TABLE `tbdb_bet_type`
  RENAME TO `tbdb_market_type`;

-- tbdb_event_group_market_type
ALTER TABLE `tbdb_tournament_event_bet_type`
  RENAME TO `tbdb_event_group_market_type`,
  CHANGE `bet_type_id` `market_type_id` INT UNSIGNED NOT NULL,
  CHANGE `tournament_sport_event_id` `event_group_id` INT UNSIGNED NOT NULL;
  
-- tbdb_selection_status
ALTER TABLE `tbdb_runner_status`
	RENAME TO `tbdb_selection_status`;

-- tbdb_selection
CREATE TABLE `tbdb_selection` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `market_id`               INT UNSIGNED NOT NULL,
  `external_selection_id`   INT UNSIGNED NOT NULL,
  `selection_status_id`		INT UNSIGNED NOT NULL DEFAULT 1,
  `name`                    VARCHAR(64) NOT NULL,
  `created_date`            DATETIME NOT NULL,
  `updated_date`            DATETIME NULL,
  PRIMARY KEY ( `id` ),
  KEY ( `market_id` ),
  KEY ( `external_selection_id` ),
  KEY ( `selection_status_id` ),
  KEY ( `name` ),
  KEY ( `created_date` ),
  KEY ( `updated_date` )
) ENGINE=InnoDB default charset=utf8 ;


-- tbdb_selection_price
CREATE TABLE `tbdb_selection_price` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  `selection_id`    INT UNSIGNED NOT NULL,
  `bet_product_id`  INT UNSIGNED NOT NULL DEFAULT 1,
  `win_odds`        FLOAT(8, 2) NOT NULL,
  `place_odds`      FLOAT(8, 2) NULL,
  `override_odds`   FLOAT(8, 2) NULL,
  `created_date`    DATETIME NOT NULL,
  `updated_date`    DATETIME NULL,
  PRIMARY KEY ( `id` ),
  KEY ( `selection_id` ),
  KEY ( `bet_product_id` ),
  KEY ( `win_odds` ),
  KEY ( `place_odds` ),
  KEY ( `override_odds` ),
  KEY ( `created_date` ),
  KEY ( `updated_date` )
) ENGINE=InnoDB default charset=utf8 ;

-- tbdb_selection_result
CREATE TABLE `tbdb_selection_result` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `selection_id`  INT UNSIGNED NOT NULL,
  `position`      INT UNSIGNED NOT NULL,
  `payout_flag`   INT UNSIGNED NOT NULL DEFAULT 0,
  `created_date`  DATETIME NOT NULL,
  `updated_date`  DATETIME NULL,
  PRIMARY KEY ( `id` ),
  KEY ( `selection_id` ),
  KEY ( `position` ),
  KEY ( `created_date` ),
  KEY ( `updated_date` )
) ENGINE=InnoDB default charset=utf8 ;

-- END OF MERGED 2.8 CHANGES

-- Insert competitions for 'galloping', 'harness' and 'greyhounds'
INSERT INTO `tbdb_tournament_competition` (`id`, `tournament_sport_id`, `external_competition_id`, `name`, `status_flag`, `created_date`, `updated_date`) VALUES
(NULL, 1, 0, 'Galloping', 1, NOW(), '0000-00-00 00:00:00'),
(NULL, 2, 0, 'Harness', 1, NOW(), '0000-00-00 00:00:00'),
(NULL, 3, 0, 'Greyhounds', 1, NOW(), '0000-00-00 00:00:00');

-- Set up "user audit" table

CREATE TABLE tbdb_user_audit (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL,
  admin_id int(11) NOT NULL,
  field_name varchar(128) NOT NULL,
  old_value text NOT NULL,
  new_value text NOT NULL,
  update_date datetime NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
ALTER TABLE `tbdb_user_audit` ADD INDEX ( `user_id` );
ALTER TABLE `tbdb_user_audit` ADD INDEX ( `field_name` );

-- Add 'self exclusion date' and 'loss_limit' to topbetta user table
ALTER TABLE `tbdb_topbetta_user`
ADD `self_exclusion_date` DATETIME NULL ,
ADD `bet_limit` INT( 10 ) NOT NULL DEFAULT -1,
ADD `requested_bet_limit` INT( 10 ) NOT NULL DEFAULT 0
;

-- Add index to account transactions table
ALTER TABLE `tbdb_account_transaction` ADD INDEX ( `recipient_id` );
ALTER TABLE `tbdb_account_transaction` ADD INDEX ( `giver_id` );
ALTER TABLE `tbdb_account_transaction` ADD INDEX ( `session_tracking_id` );
ALTER TABLE `tbdb_account_transaction` ADD INDEX ( `account_transaction_type_id` );

-- Add index to tournament transactions table
ALTER TABLE `tbdb_tournament_transaction` ADD INDEX ( `recipient_id` );
ALTER TABLE `tbdb_tournament_transaction` ADD INDEX ( `giver_id` );
ALTER TABLE `tbdb_tournament_transaction` ADD INDEX ( `session_tracking_id` );
ALTER TABLE `tbdb_tournament_transaction` ADD INDEX ( `tournament_transaction_type_id` );

-- Update account transaction bet types to remove the references of word 'live'
UPDATE `tbdb_account_transaction_type` SET `keyword` = 'betentry',
`name` = 'Bet Entry',
`description` = 'Account Balance is being spent on a bet.' WHERE `id` =3;
UPDATE `tbdb_account_transaction_type` SET `keyword` = 'betwin',
`name` = 'Bet Win',
`description` = 'Account Balance is being increased because of a bet win.' WHERE `id` =4;

-- Add 'Bet Refund' to account transactiont type
INSERT INTO `tbdb_account_transaction_type` (
`id` ,
`keyword` ,
`name` ,
`description`
)
VALUES (
NULL , 'betrefund', 'Bet Refund', 'Account Balance is being refunded on a bet.'
);

-- Drop home page reference
ALTER TABLE `tbdb_topbetta_user` DROP `homepage`;

-- Add bet table
CREATE TABLE `tbdb_bet` (
`id` INT( 11 ) NOT NULL auto_increment,
`external_bet_id` INT( 11 ) NOT NULL ,
`user_id` INT( 11 ) NOT NULL ,
`bet_amount` INT( 10 ) NOT NULL ,
`bet_type_id` INT( 11 ) NOT NULL ,
`bet_result_status_id` INT( 11 ) NOT NULL ,
`bet_origin_id` VARCHAR( 45 ) NOT NULL ,
`bet_product_id` VARCHAR( 45 ) NOT NULL ,
`bet_transaction_id` INT( 11 ) NULL ,
`result_transaction_id` INT( 11 ) NULL ,
`refund_transaction_id` INT( 11 ) NULL ,
`resulted_flag` TINYINT( 1 ) NOT NULL DEFAULT '0',
`refunded_flag` TINYINT( 1 ) NOT NULL DEFAULT '0',
`flexi_flag` TINYINT( 1 ) NOT NULL DEFAULT '0',
`fixed_odds` FLOAT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `external_bet_id` ) , 
INDEX ( `user_id` ),
INDEX ( `bet_type_id` ),
INDEX ( `bet_result_status_id` ),
INDEX ( `bet_origin_id` ),
INDEX ( `bet_product_id` ),
INDEX ( `bet_transaction_id` ),
INDEX ( `result_transaction_id` ),
INDEX ( `refund_transaction_id` )
)  ENGINE=InnoDB default charset=utf8 ;

-- Add bet product table
CREATE TABLE `tbdb_bet_product` (
`id` INT( 11 ) NOT NULL auto_increment,
`keyword` VARCHAR( 64 ) NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL,
`created_date` DATETIME NOT NULL,
`updated_date` DATETIME NOT NULL,
PRIMARY KEY ( `id` ) ,
INDEX ( `keyword` )
)  ENGINE=InnoDB default charset=utf8 ;

-- Add bet selection table
CREATE TABLE `tbdb_bet_selection` (
`id` INT( 11 ) NOT NULL auto_increment,
`bet_id` INT( 11 ) NOT NULL ,
`selection_id` INT( 11 ) NOT NULL,
`position` TINYINT NULL,
PRIMARY KEY ( `id` ) ,
INDEX ( `bet_id` ),
INDEX ( `selection_id` )
)  ENGINE=InnoDB default charset=utf8 ;

-- Add bet origin table
CREATE TABLE `tbdb_bet_origin` (
`id` INT( 11 ) NOT NULL auto_increment,
`keyword` VARCHAR( 64 ) NOT NULL ,
`name` VARCHAR( 64 ) NOT NULL,
`description` TEXT NULL,
`created_date` DATETIME NOT NULL,
`updated_date` DATETIME NOT NULL,
PRIMARY KEY ( `id` ) ,
INDEX ( `keyword` )
)  ENGINE=InnoDB default charset=utf8 ;

-- populate bet origin table
INSERT INTO `tbdb_bet_origin` (
`id` ,
`keyword` ,
`name` ,
`description` ,
`created_date` ,
`updated_date`
)
VALUES (
'', 'tournament', 'Tournament Page', 'Bets are from Tournament pages.', NOW( ) , NOW( )
), (
'', 'betting', 'Race Betting Page', 'Bets are from race betting pages.', NOW( ) , NOW( )
);

-- set up new next to jump module
DELETE FROM `tbdb_modules` WHERE `id` = 17;
DELETE FROM `tbdb_modules_menu` WHERE `moduleid` = 17;
INSERT INTO  `tbdb_modules` (
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
NULL ,  'Next To Jump',  '',  '1',  'nexttojump',  '0',  '0000-00-00 00:00:00',  '1',  'mod_nexttojump',  '0',  '0',  '0',  '',  '0',  '0',  ''
);
SET @module_id = LAST_INSERT_ID();
INSERT INTO `tbdb_modules_menu` (
`moduleid`,
`menuid`
)
VALUES (
@module_id, 0
);

-- set up new upcoming tournament module
DELETE FROM `tbdb_modules` WHERE `id` = 16;
DELETE FROM `tbdb_modules_menu` WHERE `moduleid` = 16;
INSERT INTO  `tbdb_modules` (
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
NULL ,  'Upcoming Tournaments',  '',  '1',  'uctournaments',  '0',  '0000-00-00 00:00:00',  '1',  'mod_uctournaments',  '0',  '0',  '0',  '',  '0',  '0',  ''
);
SET @module_id = LAST_INSERT_ID();
INSERT INTO `tbdb_modules_menu` (
`moduleid`,
`menuid`
)
VALUES (
@module_id, 0
);


-- Add bet type back
CREATE TABLE `tbdb_bet_type` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(64) NOT NULL,
`description` TEXT NULL,
`status_flag` TINYINT(1) UNSIGNED NOT NULL ,
`created_date` DATETIME NOT NULL,
`updated_date` DATETIME NOT NULL,
PRIMARY KEY (`id`),
INDEX ( `name` )
) ENGINE=InnoDB default charset=utf8;

-- Added exotic bet types
INSERT INTO `tbdb_bet_type` (
`id` ,
`name` ,
`description` ,
`status_flag` ,
`created_date` ,
`updated_date`
)
VALUES  (
1 , 'win', 'win bet', '1', NOW( ) , NOW( )
), (
2 , 'place', 'place bet', '1', NOW( ) , NOW( )
), (
3 , 'eachway', 'each way bet', '1', NOW( ) , NOW( )
),(
NULL , 'quinella', 'quinella exotic bet', '1', NOW( ) , NOW( )
), (
NULL , 'exacta', 'exacta exotic bet', '1', NOW( ) , NOW( )
), (
NULL , 'trifecta', 'trifecta exotic bet', '1', NOW( ) , NOW( )
), (
NULL , 'firstfour', 'first four exotic bet', '1', NOW( ) , NOW( )
);

UPDATE `tbdb_bet_type` SET `name` = 'eachway' WHERE `id` =3 LIMIT 1 ;

-- Add betting component
INSERT INTO `tbdb_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(null, 'Betting', 'option=com_betting', 0, 0, 'option=com_betting', 'Betting', 'com_betting', 0, 'js/ThemeOffice/component.png', 0, '', 1);

-- Modify tournament_racing_bet table, now tournament_bet
ALTER TABLE `tbdb_tournament_racing_bet`
  RENAME TO `tbdb_tournament_bet`,
  DROP `race_id`,
  ADD `bet_product_id` INT UNSIGNED NOT NULL AFTER `bet_type_id`,
  ADD `fixed_odds` FLOAT(8,2) NULL AFTER `win_amount`,
  ADD `flexi_flag` TINYINT(1) UNSIGNED NOT NULL AFTER `fixed_odds`;
  
-- A flag to mark this record is from 3.0 merge; can be removed after merge script run
ALTER TABLE `tbdb_tournament_bet` ADD `from_merge_flag` TINYINT( 1 ) NOT NULL DEFAULT '0'
COMMENT ' A flag to mark this record is from 3.0 merge; can be removed after merge script run' AFTER `flexi_flag` ;
  
-- Update bet_product_id of existing racing bet to 'unitab' 
UPDATE `tbdb_tournament_bet` SET `bet_product_id` = 2;

-- Create tournament_bet_selection table
CREATE TABLE `tbdb_tournament_bet_selection` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_bet_id` int(10) unsigned NOT NULL,
  `selection_id` int(10) unsigned NOT NULL,
  `position` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `tournament_racing_bet_id` (`tournament_bet_id`),
  KEY `racing_runner_id` (`selection_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- add wagering_api table and columns to required tables

CREATE TABLE `tbdb_wagering_api` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `keyword`   		VARCHAR(64) NOT NULL,
  `name`        	VARCHAR(64) NOT NULL,
  `description`		TEXT NOT NULL,
  `updated_date`	DATETIME NOT NULL,
  `created_date`	DATETIME NOT NULL,
  PRIMARY KEY ( `id` ),
  INDEX( `keyword` )
) ENGINE=InnoDB default charset=utf8 ;

INSERT INTO `tbdb_wagering_api` (
	`keyword`,
	`name`,
	`description`,
	`created_date` 
) VALUES (
	'tastab',
	'TasTab',
	'Tasmania TAB API',
	NOW() 
);
INSERT INTO `tbdb_wagering_api` (
	`keyword`,
	`name`,
	`description`,
	`created_date` 
) VALUES (
	'unitab',
	'UNiTAB',
	'UNiTAB XML Feeds',
	NOW()
);
SET @unitab_id =  LAST_INSERT_ID();

INSERT INTO `tbdb_wagering_api` (
	`keyword`,
	`name`,
	`description`,
	`created_date` 
) VALUES (
	'legacy',
	'Legacy Data',
	'Old date which pre-dates using external_id and wagering_api_id',
	NOW()
);

SET @legacy_id = LAST_INSERT_ID();

ALTER TABLE  `tbdb_event_group` ADD  `wagering_api_id` TINYINT NOT NULL AFTER  `external_event_group_id` ;
ALTER TABLE  `tbdb_event` ADD  `wagering_api_id` TINYINT NOT NULL AFTER  `external_event_id` ;
ALTER TABLE  `tbdb_market` ADD  `wagering_api_id` TINYINT NOT NULL AFTER  `external_market_id` ;
ALTER TABLE  `tbdb_selection` ADD  `wagering_api_id` TINYINT NOT NULL AFTER  `external_selection_id` ;

UPDATE `tbdb_event_group` SET `wagering_api_id` = @unitab_id;

UPDATE `tbdb_event` SET `wagering_api_id` = @unitab_id
	WHERE external_event_id IS NOT NULL;

UPDATE `tbdb_market` SET `wagering_api_id` = @unitab_id
	WHERE external_market_id IS NOT NULL;

UPDATE `tbdb_selection` SET `wagering_api_id` = @unitab_id
	WHERE external_selection_id IS NOT NULL;

-- SET @event_group_id = 0;
-- UPDATE `tbdb_event_group` SET `wagering_api_id` = @legacy_id, `external_event_group_id` =  (@event_group_id:=@event_group_id +1) 
--	WHERE external_event_group_id IS NULL;

SET @event_id = 1;
UPDATE `tbdb_event` SET `wagering_api_id` = @legacy_id, `external_event_id` =  (@event_id:=@event_id +1)
	WHERE external_event_id IS NULL;

SET @market_id = 1;
UPDATE `tbdb_market` SET `wagering_api_id` = @legacy_id, `external_market_id` =  (@market_id:=@market_id +1) 
	WHERE external_market_id IS NULL;

SET @selection_id = 1;
UPDATE `tbdb_selection` SET `wagering_api_id` = @legacy_id, `external_selection_id` =  (@selection_id:=@selection_id +1) 
	WHERE external_selection_id IS NULL;

ALTER TABLE `tbdb_event_group` ADD INDEX ( `external_event_group_id` );

-- Modify external_selection_id so that can be combination of number and event_id

ALTER TABLE  `tbdb_selection` CHANGE  `external_selection_id`  `external_selection_id` VARCHAR( 64 ) NOT NULL;

-- Add racing market type

INSERT INTO  `tbdb_market_type` (
	`id` ,
	`name` ,
	`description` ,
	`status_flag` ,
	`created_date` ,
	`updated_date`
)
VALUES (
	NULL ,  'Racing',  'This is the market for each racing event',  '1', NOW( ) ,  ''
);

INSERT INTO  `tbdb_event_status` (
	`id` ,
	`keyword` ,
	`name` ,
	`description`
) VALUES (
	NULL ,  'closed',  'Closed',  'Event is closed to betting'
),
(
	NULL ,  'interim',  'Interim',  'Interim event results'
);

-- populate bet product table
INSERT INTO `tbdb_bet_product` (
`id` ,
`keyword` ,
`name` ,
`created_date` ,
`updated_date`
)
VALUES (
NULL, 'legacy', 'Legacy', NOW( ) , NOW( )
), (
NULL, 'unitab', 'UniTAB', NOW( ) , NOW( )
), (
NULL, 'supertab', 'SuperTAB', NOW( ) , NOW( )
);

-- change postion to allow null values in selection_result
ALTER TABLE  `tbdb_selection_result` CHANGE  `position`  `position` INT( 10 ) UNSIGNED NULL;

-- alter table with metadata, so meta dad can be dropped
ALTER TABLE `tbdb_selection`
	ADD `number` TINYINT NULL AFTER `name`,
	ADD `associate` VARCHAR(64) NULL AFTER `number`,
	ADD `barrier` VARCHAR(64) NULL AFTER `associate`,
	ADD `handicap` VARCHAR(64) NULL AFTER `barrier`,
	ADD `ident` VARCHAR(64) NULL AFTER `handicap`;

ALTER TABLE `tbdb_event_group`
	ADD `meeting_code` VARCHAR(64) NULL AFTER `name`,
	ADD `state` VARCHAR(64) NULL AFTER `meeting_code`,
	ADD `events` TINYINT NULL AFTER `state`,
	ADD `track` VARCHAR(64) NULL AFTER `events`,
	ADD `weather` VARCHAR(64) NULL AFTER `track`,
	ADD `type_code` VARCHAR(64) NULL AFTER `weather`;
	
ALTER TABLE `tbdb_event`
	ADD `number` TINYINT NULL AFTER `name`,
	ADD `class` VARCHAR(64) NULL AFTER `number`,
	ADD `distance` VARCHAR(64) NULL AFTER `class`,
	ADD `trifecta_pool` FLOAT(10,2) NULL AFTER `distance`,
	ADD `firstfour_pool` FLOAT(10,2) NULL AFTER `trifecta_pool`,
	ADD `exacta_pool` FLOAT(8,2) NULL AFTER `firstfour_pool`,
	ADD `quinella_pool` FLOAT(8,2) NULL AFTER `exacta_pool`;

UPDATE `tbdb_bet_result_status` SET status_flag = 1 WHERE name = 'partially-refunded';

-- components changes
DELETE FROM `tbdb_components` WHERE `tbdb_components`.`id` =70 LIMIT 1 ;
UPDATE `tbdb_components` SET `name` = 'Tournaments',
`admin_menu_link` = 'option=com_tournament&=controller=tournament' WHERE `tbdb_components`.`id` =69 LIMIT 1 ;

ALTER TABLE  `tbdb_selection_result` ADD  `win_dividend` FLOAT( 8, 2 ) UNSIGNED NULL AFTER  `position` ,
ADD  `place_dividend` FLOAT( 8, 2 ) UNSIGNED NULL AFTER  `win_dividend` ;

ALTER TABLE  `tbdb_event` ADD  `trifecta_dividend` VARCHAR( 200 ) NULL DEFAULT NULL AFTER  `distance` ,
ADD  `firstfour_dividend` VARCHAR( 200 ) NULL DEFAULT NULL AFTER `trifecta_dividend` ,
ADD  `quinella_dividend` VARCHAR( 200 ) NULL DEFAULT NULL AFTER  `firstfour_dividend` ,
ADD  `exacta_dividend` VARCHAR( 200 ) NULL DEFAULT NULL AFTER  `quinella_dividend` ;

-- add racing flag

ALTER TABLE  `tbdb_tournament_sport` ADD  `racing_flag` TINYINT(1) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `status_flag` ;

UPDATE  `tbdb_tournament_sport` SET  `racing_flag` =  '1' WHERE  `tbdb_tournament_sport`.`id` =1 LIMIT 1 ;
UPDATE  `tbdb_tournament_sport` SET  `racing_flag` =  '1' WHERE  `tbdb_tournament_sport`.`id` =2 LIMIT 1 ;
UPDATE  `tbdb_tournament_sport` SET  `racing_flag` =  '1' WHERE  `tbdb_tournament_sport`.`id` =3 LIMIT 1 ;


-- insert racing betting to menu
INSERT INTO `tbdb_menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(NULL, 'mainmenu', 'Today''s Racing', 'racing-betting', 'betting/racing', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0);

-- update feedback module to use transparent background
UPDATE `tbdb_modules` SET `params` = 'community_domain=community.topbetta.com\r\nfastpass_key=zs5beoud5znq\r\nfastpass_secret=tdh0r8tp3ooctj1vs3gnnn4rdxlsdpp7\r\nwidget_js= <script type="text/javascript" charset="utf-8">\\n  var is_ssl = ("https:" == document.location.protocol);\\n  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";\\n  document.write(unescape("%3Cscript src=''" + asset_host + "javascripts/feedback-v2.js'' type=''text/javascript''%3E%3C/script%3E"));\\n</script>\\n\\n<script type="text/javascript" charset="utf-8">\\n  var feedback_widget_options = {};\\n\\n  feedback_widget_options.display = "overlay";  \\n  feedback_widget_options.company = "topbetta";\\n  feedback_widget_options.placement = "right";\\n  feedback_widget_options.color = "transparent";\\n  feedback_widget_options.style = "idea";\\nfeedback_widget_options.fastpass = "[%FAST_PASS_URL]";\\n  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);\\n</script>\r\ncache=1\r\n\r\n' WHERE id = 69;

