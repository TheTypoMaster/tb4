--
-- Table structure for table `meeting`
--

DROP TABLE IF EXISTS `meeting`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `meeting` (
  `id` int(11) NOT NULL auto_increment,
  `tab_meeting_id` varchar(32) NOT NULL COMMENT 'TAB Meeting ID',
  `name` varchar(64) NOT NULL COMMENT 'Meeting Name',
  `events` int(11) NOT NULL COMMENT 'number of events in meeting',
  `type` varchar(25) NOT NULL COMMENT 'type of meeting gallops/greyhouds/harness',
  `track` varchar(25) NOT NULL COMMENT 'track condition for meeting',
  `weather` varchar(25) NOT NULL COMMENT 'Weather conditions for meeting',
  `date` varchar(64) NOT NULL,
  `atp` tinyint(1) NOT NULL default '0' COMMENT 'IS meeting in ATP',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `tab_meeting_id` (`tab_meeting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19242 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `next2jump`
--

DROP TABLE IF EXISTS `next2jump`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `next2jump` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(32) NOT NULL,
  `tab_race_id` varchar(32) NOT NULL,
  `start_time` varchar(32) NOT NULL,
  `venue_name` varchar(32) NOT NULL,
  `time2jump` varchar(32) NOT NULL,
  `date` varchar(32) NOT NULL,
  `dump_time_stamp` varchar(32) NOT NULL,
  `unix_time_stamp_start` varchar(32) NOT NULL COMMENT 'unix timestamp for race start',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1722741 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `race`
--

DROP TABLE IF EXISTS `race`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `race` (
  `id` int(32) NOT NULL auto_increment,
  `meeting_id` int(11) NOT NULL COMMENT 'ID of meeting record races are in',
  `tab_race_id` varchar(64) NOT NULL COMMENT 'TAB Race ID',
  `type` varchar(255) NOT NULL COMMENT 'Type of Meeting',
  `location` varchar(255) NOT NULL COMMENT 'Meeting location',
  `number` varchar(255) NOT NULL COMMENT 'Race Number',
  `name` varchar(255) NOT NULL COMMENT 'Race Name',
  `win_odds` varchar(512) NOT NULL COMMENT 'Latest approx win odds for race runners',
  `place_odds` varchar(512) NOT NULL COMMENT 'Latest approx place odds for race runners',
  `time` varchar(255) NOT NULL default '0' COMMENT 'Time Race Starts',
  `date` int(32) NOT NULL default '0' COMMENT 'Date race is on',
  `distance` varchar(32) NOT NULL COMMENT 'Race distance ',
  `class` varchar(32) NOT NULL COMMENT 'Race Class',
  `time2jump` varchar(32) NOT NULL COMMENT 'time till race starts',
  `status` varchar(32) NOT NULL COMMENT 'race status',
  `dump_timestamp` varchar(32) NOT NULL COMMENT 'timestamp of when the race was written to the DB',
  `start_unixtimestamp` int(11) NOT NULL COMMENT 'unixtimestamp of race start time',
  `start_datetime` datetime NOT NULL COMMENT 'Race Start Time in Mysql DATETIME format',
  `dividends_paid` tinyint(1) NOT NULL COMMENT 'whether race has had dividends paid - Not Interim',
  `clients_paid` tinyint(1) NOT NULL default '0' COMMENT 'Set to 1 when clients are paid ',
  `whatDay` tinyint(4) NOT NULL COMMENT 'What day is the race on',
  `sortOrder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tab_race_id` (`tab_race_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=167181 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `results` (
  `tab_race_id` varchar(32) NOT NULL COMMENT 'TAB raceID',
  `status` varchar(32) NOT NULL default '0' COMMENT 'Status of results - Interim or Final Dividends',
  `first` varchar(24) NOT NULL COMMENT 'Runner Number of 1st place',
  `second` varchar(24) NOT NULL COMMENT 'Runner Number of 2nd place',
  `third` varchar(24) NOT NULL COMMENT 'Runner Number of 3rd place',
  `fourth` varchar(24) NOT NULL COMMENT 'runner number of 4th place',
  `firstwindiv` varchar(48) NOT NULL COMMENT 'Win dividends for 1st place runner',
  `firstplacediv` varchar(48) NOT NULL COMMENT 'Place dividends for 1st place runner',
  `seconddiv` varchar(48) NOT NULL COMMENT 'Place divend for second runner',
  `thirddiv` varchar(48) NOT NULL COMMENT 'Place divend for 3rd placed runner',
  `first_runner` varchar(48) NOT NULL COMMENT 'name of winning runner',
  `second_runner` varchar(32) NOT NULL COMMENT '2nd placed runner',
  `third_runner` varchar(32) NOT NULL COMMENT 'third placed runner',
  `fourth_runner` varchar(32) NOT NULL COMMENT '4th placed runner',
  `quinella` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `exacta` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `duet1` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `duet2` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `duet3` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `trifecta` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `first4` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `running_double` varchar(32) NOT NULL COMMENT 'store results in comma seperated fields',
  `quinellaDividends` varchar(64) NOT NULL,
  `duetDividends` varchar(64) NOT NULL,
  `trifectaDividends` varchar(64) NOT NULL,
  `exactaDividends` varchar(64) NOT NULL,
  `runningdDividends` varchar(64) NOT NULL,
  `first4Dividends` varchar(64) NOT NULL,
  `clients_paid` tinyint(1) NOT NULL default '0' COMMENT 'set to one when results have been paid to clients that one',
  PRIMARY KEY  (`tab_race_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `runner`
--

DROP TABLE IF EXISTS `runner`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `runner` (
  `id` int(32) NOT NULL auto_increment,
  `race_id` int(32) NOT NULL default '0' COMMENT 'ID of RACE record runners are in',
  `number` varchar(255) NOT NULL COMMENT 'Runner  Number',
  `name` varchar(255) NOT NULL COMMENT 'Runner Name',
  `associate` varchar(50) NOT NULL COMMENT 'Jockey on Rider or Trainer',
  `status` varchar(64) NOT NULL COMMENT 'Status of Runner',
  `barrier` varchar(64) NOT NULL COMMENT 'Barrier runner starts from',
  `handicap` varchar(64) NOT NULL COMMENT 'Runners handicap',
  `ident` varchar(255) NOT NULL COMMENT 'Runner name ident',
  `date` int(32) NOT NULL COMMENT 'Date runner is running',
  `tab_race_id` varchar(32) NOT NULL COMMENT 'what race the runner is in',
  `one_runner_id` varchar(24) NOT NULL,
  PRIMARY KEY  (`one_runner_id`),
  UNIQUE KEY `id` (`id`),
  KEY `tab_race_id` (`tab_race_id`),
  KEY `ident` (`ident`)
) ENGINE=MyISAM AUTO_INCREMENT=1741997 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- Table structure for table `atp_race`
--

DROP TABLE IF EXISTS `atp_race`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `atp_race` (
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

--
-- Table structure for table `atp_runner`
--

DROP TABLE IF EXISTS `atp_runner`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `atp_runner` (
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
  KEY `tab_race_id` (`tab_race_id`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- Table structure for table `atp_meeting`
--

DROP TABLE IF EXISTS `atp_meeting`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `atp_meeting` (
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

