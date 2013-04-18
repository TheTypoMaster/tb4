-- update article link to terms-and-conditions

UPDATE `tbdb_menu` SET `link` = 'terms-and-conditions' WHERE `link` = 'content/article/2';

-- add tournament result freezing table

DROP TABLE IF EXISTS `tbdb_tournament_payout_final`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_payout_final` (
  `id` int(11) NOT NULL auto_increment,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` int(11) NOT NULL COMMENT 'final ranking position',
  `tournament_payout_type_id` int(11) NOT NULL,
  `win_amount` int(11) NOT NULL COMMENT 'amount user was paid out',
  PRIMARY KEY  (`id`),
  KEY `tournament_id` (`tournament_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- add tournament result freezing table payout types

DROP TABLE IF EXISTS `tbdb_tournament_payout_type`;
CREATE TABLE IF NOT EXISTS `tbdb_tournament_payout_type` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ticket/cash/tournament dollars',
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- setup payout types

INSERT INTO `tbdb_tournament_payout_type` 
(`id` ,`keyword` ,`name` ,`description`)
VALUES 
(NULL ,  'cash',  'Cash Payout',  'User paid in cash'), 
(NULL ,  'tournamentticket',  'Tournament Ticket',  'User paid tournament dollars to purchase ticket into next tournament'), 
(NULL ,  'tournamentdollar',  'Tournament Dollars',  'User paid in tournament dollars, usually as a remainder of if they already have a ticket into next tournament');

