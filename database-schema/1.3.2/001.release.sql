-- created meeting table
DROP TABLE IF EXISTS `tbdb_meeting`;

CREATE TABLE `tbdb_meeting` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `meeting_code` VARCHAR(255) NOT NULL COMMENT 'Identifier Code. UNITAB uses a similar system to TAB for their codes.',
  `name` VARCHAR(255) NOT NULL COMMENT 'Meeting/Venue Name',
  `state` VARCHAR(255) NOT NULL COMMENT 'The state in which the venue is located',
  `events` INT(11) NOT NULL COMMENT 'The number of races in the meeting',
  `meeting_type_id` INT(11) NOT NULL COMMENT 'Joins meeting_type table',
  `track` VARCHAR(255) NOT NULL COMMENT 'Track condition',
  `weather` VARCHAR(255) NOT NULL COMMENT 'Venue weather conditions',
  `meeting_date` DATE NOT NULL COMMENT 'The date of the meeting',
  `created_date` DATETIME NOT NULL COMMENT 'Created date',
  `updated_date` DATETIME NULL COMMENT 'Updated date',
  PRIMARY KEY(`id`),
  INDEX ( `meeting_code` ),
  INDEX ( `meeting_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- adding index on events as it will be used in joins
ALTER TABLE `tbdb_meeting` ADD INDEX ( `events` );

-- created meeting_type table
DROP TABLE IF EXISTS `tbdb_meeting_type`;

CREATE TABLE `tbdb_meeting_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(255) NOT NULL COMMENT 'The feed identifier code for the meeting type, i.e: R: Galloping, T: Harness, G:Greyhounds',
  `name` VARCHAR(255) NOT NULL COMMENT 'The name of the meeting type',
  `description` VARCHAR(255) NOT NULL COMMENT 'An optional long description for the type of meeting',
  PRIMARY KEY (`id`),
  INDEX (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- created race table
DROP TABLE IF EXISTS `tbdb_race`;

CREATE TABLE `tbdb_race` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` INT(11) NOT NULL COMMENT 'Joins meeting table',
  `number` INT(11) NOT NULL COMMENT 'The number of the race',
  `name` VARCHAR(255) NOT NULL COMMENT 'The name of the race',
  `start_date` DATETIME NOT NULL COMMENT 'Race start time',
  `distance` VARCHAR(255) NOT NULL COMMENT 'Distance of the track',
  `class` VARCHAR(255) NOT NULL COMMENT 'Runner class',
  `paid_flag` INT(11) NOT NULL DEFAULT '0' COMMENT 'Represents whether or not bets on a race have been processed',
  `race_status_id` INT(11) NOT NULL COMMENT 'Current real-world race status (i.e. selling, in progress, etc)',
  `created_date` DATETIME NOT NULL COMMENT 'Created date',
  `updated_date` DATETIME NULL COMMENT 'Updated date',
  PRIMARY KEY (`id`),
  INDEX (`meeting_id`),
  INDEX (`number`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create race status table
DROP TABLE IF EXISTS `tbdb_race_status`;

CREATE TABLE `tbdb_race_status`(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(255) NOT NULL COMMENT 'A short identifier for the current race status (e.g. selling)',
  `name` VARCHAR(255) NOT NULL COMMENT 'The name of the status',
  `description` VARCHAR(255) NOT NULL COMMENT 'An optional long description for the status',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create runner table
DROP TABLE IF EXISTS `tbdb_runner`;

CREATE TABLE `tbdb_runner` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `race_id` INT(11) NOT NULL COMMENT 'Joins race table',
  `number` INT(11) NOT NULL COMMENT 'Runner number',
  `name` VARCHAR(255) NOT NULL COMMENT 'Runner name',
  `associate` VARCHAR(255) NOT NULL COMMENT 'Jockey, passenger or trainer depending on the meeting type',
  `runner_status_id` INT(11) NOT NULL COMMENT 'Joins runner_status',
  `win_odds` FLOAT(8,2) NOT NULL COMMENT 'Odds of runner coming in first',
  `place_odds` FLOAT(8,2) NOT NULL COMMENT 'Odds of runner coming in first, second or third',
  `barrier` VARCHAR(255) NOT NULL COMMENT 'Runner barrier (i.e. field position)',
  `handicap` VARCHAR(255) NOT NULL COMMENT 'Runner handicap',
  `ident` VARCHAR(255) NOT NULL COMMENT 'A stripped out version of the runner name used to associate it with 12Follow ratings',  
  `created_date` DATETIME NOT NULL COMMENT 'Created date',
  `updated_date` DATETIME NULL COMMENT 'Updated date',
  PRIMARY KEY (`id`),
  INDEX (`race_id`),
  INDEX (`number`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create race status table
DROP TABLE IF EXISTS `tbdb_runner_status`;

CREATE TABLE `tbdb_runner_status`(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(255) NOT NULL COMMENT 'A short identifier for the status (e.g. “scratched”)',
  `name` VARCHAR(255) NOT NULL COMMENT 'The full status name',
  `description` VARCHAR(255) NOT NULL COMMENT 'An optional long description for the status', 
  PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create result table
DROP TABLE IF EXISTS `tbdb_result`;

CREATE TABLE `tbdb_result`(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `race_id` INT(11) NOT NULL COMMENT 'Joins race table',
  `runner_id` INT(11) NOT NULL COMMENT 'Joins runner table',
  `position` VARCHAR(255) NOT NULL COMMENT 'The position of the runner',
  `created_date` DATETIME NOT NULL COMMENT 'Created date',
  `updated_date` DATETIME NULL COMMENT 'Updated date',
  PRIMARY KEY (`id`),
  INDEX (`race_id`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create the meeting venue table
DROP TABLE IF EXISTS `tbdb_meeting_venue`;

CREATE TABLE `tbdb_meeting_venue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `state` VARCHAR(255) NOT NULL,
  PRIMARY KEY ( `id` ),
  KEY ( `name` ),
  KEY ( `state` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- change racing_meeting_id to meeting_id
ALTER TABLE `tbdb_tournament_racing` CHANGE `racing_meeting_id` `meeting_id` INT( 11 ) NOT NULL;

-- change racing_race_id to race_id
ALTER TABLE `tbdb_tournament_racing_bet` CHANGE `racing_race_id` `race_id` INT( 11 ) NOT NULL;

-- change racing_runner_id to runner_id
ALTER TABLE `tbdb_tournament_racing_bet_selection` CHANGE `racing_runner_id` `runner_id` INT( 11 ) NOT NULL;

-- populate tbdb_meeting_type table
INSERT INTO `tbdb_meeting_type` (`id`, `code`, `name`, `description`) VALUES
(1, 'R', 'Galloping', ''),
(2, 'T', 'Harness', ''),
(3, 'G', 'Greyhounds', '');

-- populate tbdb_race_status table
INSERT INTO  `tbdb_race_status` (`id` ,`keyword` ,`name` ,`description`) VALUES 
(NULL ,  'selling',  'Selling',  'Open for bets'), 
(NULL ,  'paying',  'Paying',  'All Paying'), 
(NULL ,  'interim',  'Interim',  'Interim results'),
(NULL,   'abandoned', 'Abandoned', 'Race abandoned'),
(NULL ,  'closed',  'Closed',  'Closed to bets');

-- populate tbdb_runner_status table
INSERT INTO `tbdb_runner_status` (
  `keyword`,
  `name`,
  `description`
) VALUES (
  'not scratched',
  'Not Scratched',
  'The runner is still active an taking bets'
),(
  'scratched',
  'Scratched',
  'The runner has been scratched ahead of the race'
),(
  'late scratching',
  'Late Scratching',
  'The runner has been scratched late'
);

