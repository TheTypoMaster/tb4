-- add bank_name and account_name to topbetta user table

ALTER TABLE `tbdb_topbetta_user` ADD `account_name` VARCHAR( 100 ) NULL AFTER `bank_account_number` ,
ADD `bank_name` VARCHAR( 100 ) NULL AFTER `account_name` ;

-- add two new plugins to prevent user deletion

INSERT INTO  `tbdb_plugins` (
`id` ,
`name` ,
`element` ,
`folder` ,
`access` ,
`ordering` ,
`published` ,
`iscore` ,
`client_id` ,
`checked_out` ,
`checked_out_time` ,
`params`
)
VALUES (NULL ,  'User - Admin',  'admin',  'user',  '0',  '0',  '1',  '0',  '0',  '0',  '0000-00-00 00:00:00',  ''),
(NULL ,  'User - Get Satisfaction',  'getsatisfaction',  'user',  '0',  '0',  '1',  '0',  '0',  '0',  '0000-00-00 00:00:00',  '');


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
NULL ,  'Get Satisfaction',  '',  '1',  'getsatisfaction',  '0',  '0000-00-00 00:00:00',  '0',  'mod_getsatisfaction',  '0',  '0',  '0',  '',  '0',  '0',  ''
);

