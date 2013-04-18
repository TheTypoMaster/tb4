-- Adding private flag in tbdb_tournament 

ALTER TABLE `tbdb_tournament` ADD `private_flag` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `tbdb_tournament` ADD INDEX `private_flag` ( `private_flag` );

-- Make display_identifier be an unique index

ALTER TABLE `tbdb_tournament_private` ADD UNIQUE (
`display_identifier`
);
ALTER TABLE `tbdb_tournament_private` ADD INDEX ( `display_identifier` );

-- set up index on tournament_id
ALTER TABLE `tbdb_tournament_private` ADD INDEX ( `tournament_id` );

-- set up index on user_id
ALTER TABLE `tbdb_tournament_private` ADD INDEX ( `user_id` );
