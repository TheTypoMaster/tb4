-- clean out data tables

TRUNCATE racing_meeting;
TRUNCATE tbdb_tournament;
TRUNCATE tbdb_tournament_racing;
TRUNCATE tbdb_tournament_ticket;

-- racing race fixed
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
  UNIQUE KEY `tab_race_id` (`tab_race_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- racing runner fixed

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
