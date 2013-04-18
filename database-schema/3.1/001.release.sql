-- added bet_limit_flag to tournament
ALTER TABLE `tbdb_tournament` ADD `bet_limit_flag` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `reinvest_winnings_flag` ;
-- update bet_limit_flag to 1 on existing sports tournaments
UPDATE `tbdb_tournament` SET `bet_limit_flag` = 1 WHERE `tournament_sport_id` IN (
	SELECT `id` FROM `tbdb_tournament_sport` WHERE `racing_flag` = 0
);

-- add btag field to topbetta user
ALTER TABLE `tbdb_topbetta_user` ADD `btag` VARCHAR( 100 ) NULL ;

-- setup "Tournament Comment Manager" 
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
NULL , 'Tournament Comment Manager', '', '0', '68', 'option=com_tournament&controller=tournamentcomment', 'Tournament Comment Manager', 'com_tournament', '0', 'js/ThemeOffice/component.png', '0', '', '1'
);

-- add 'created date' and 'updated date' to tbdb_tournament_comment
ALTER TABLE `tbdb_tournament_comment` ADD `created_date` DATETIME NOT NULL ,
ADD `updated_date` DATETIME NOT NULL;

-- add identity_doc and identity_doc_id to tbdb_topbetta_user
ALTER TABLE `tbdb_topbetta_user` ADD `identity_doc` VARCHAR( 20 ) NULL AFTER `identity_verified_flag` ,
ADD `identity_doc_id` VARCHAR( 100 ) NULL AFTER `identity_doc` ;

-- add chargeback and promo as new account transaction types
INSERT INTO `tbdb_account_transaction_type` (
`id` ,
`keyword` ,
`name` ,
`description`
)
VALUES (
NULL , 'chargeback', 'Chargeback', 'Account balance is being decreased because of a chargeback.'
), (
NULL , 'promo', 'Promo', 'Account balance is being increased because of a promotion.'
);

