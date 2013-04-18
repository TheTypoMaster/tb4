DROP TABLE IF EXISTS `racing_meeting`;
CREATE TABLE `racing_meeting` (
  `id` int(11) NOT NULL auto_increment,
  `tab_meeting_id` varchar(32) NOT NULL COMMENT 'TAB Meeting ID',
  `name` varchar(64) NOT NULL COMMENT 'Meeting Name',
  `events` int(11) NOT NULL COMMENT 'number of events in meeting',
  `type` varchar(25) NOT NULL COMMENT 'type of meeting gallops/greyhouds/harness',
  `track` varchar(25) NOT NULL COMMENT 'track condition for meeting',
  `weather` varchar(25) NOT NULL COMMENT 'Weather conditions for meeting',
  `date` varchar(64) NOT NULL,
  `atp` tinyint(1) NOT NULL default '0' COMMENT 'IS meeting in ATP',
  `odds_type` varchar(25) NOT NULL,
  `jumpoffset` int(11) NOT NULL default '0' COMMENT 'jump time offset in seconds',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY  (`tab_meeting_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
