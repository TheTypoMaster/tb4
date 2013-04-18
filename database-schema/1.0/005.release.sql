-- adding extra fields to topbetta_user
ALTER TABLE
  `tbdb_topbetta_user`
ADD
  `identity_verified_flag` INT UNSIGNED NOT NULL DEFAULT 0,
ADD
  `bsb_number` VARCHAR(50) NULL,
ADD
  `bank_account_number` VARCHAR(100) NULL;

-- --------------------------------------------------------

--
-- Table structure for table `racing_runner`
--

DROP TABLE IF EXISTS `racing_runner`;
CREATE TABLE IF NOT EXISTS `racing_runner` (
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
  UNIQUE KEY `tab_race_id-ident` (`race_id`,`ident`),
  KEY `race_id` (`race_id`),
  KEY `ident` (`ident`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

TRUNCATE TABLE racing_meeting;
TRUNCATE TABLE racing_race;

TRUNCATE TABLE tbdb_tournament;
TRUNCATE TABLE tbdb_tournament_bet_limit;
TRUNCATE TABLE tbdb_tournament_leaderboard;
TRUNCATE TABLE tbdb_tournament_racing;
TRUNCATE TABLE tbdb_tournament_racing_bet;
TRUNCATE TABLE tbdb_tournament_racing_bet_selection;
TRUNCATE TABLE tbdb_tournament_ticket;
