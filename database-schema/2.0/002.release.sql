-- Add bet type ID to the market table
ALTER TABLE `tbdb_tournament_market`
  DROP COLUMN `name`,
  ADD `bet_type_id` INT UNSIGNED NOT NULL AFTER `tournament_match_id`,
  ADD INDEX ( `bet_type_id` );

-- Create the event and bet type mapping
DROP TABLE IF EXISTS `tbdb_tournament_event_bet_type`;
CREATE TABLE `tbdb_tournament_event_bet_type` (
  `tournament_sport_event_id` INT UNSIGNED NOT NULL,
  `bet_type_id`               INT UNSIGNED NOT NULL,
  PRIMARY KEY ( `tournament_sport_event_id`, `bet_type_id` )
) ENGINE=MyISAM default charset=utf8 ;
