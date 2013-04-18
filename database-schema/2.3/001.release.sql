-- update topbetta user params by adding multiple email content params

UPDATE `tbdb_components` SET `params` = 'mailFrom=help@topbetta.com\nfromName=TopBetta Admin\nwelcomeEmailText=Hello [name],\\n\\nThank you for registering at TopBetta. Your account has been created and must be activated before you can use it.\\n\\nTo activate the account click on the following link or copy-paste it in your browser:\\n[activation_link]\\n\\nAfter activation you may login to http://www.topbetta.com using the following username and the password you specified.\\n\\nUsername: [username]\\n\\nOnce your account is active, you may deposit funds into your account at any time. To withdraw money, you will need to fill out the provided Identity Form on our site\\n(http://www.topbetta.com/help/3)\\nand send it back to us. (This is a legal requirement and you will only have to do it once.)\\n\\nRegards,\\n\\nThe TopBetta Team\\nhelp@topbetta.com\nforgotPasswordEmailText=Hello [name],\\n\\nA request has been made to reset your TopBetta account password. To reset your password, click the link below and follow the prompts to create a new one.\\n\\nhttp://www.topbetta.com/user/reset/confirm\\n\\nThe token is [token].\\n\\nIf you did not request the change, please disregard this email.\\n\\nThank you,\\nThe TopBetta Team\\nhelp@topbetta.com\nforgotUsernameEmailText=Hello [name],\\n\\nA username reminder has been requested for your TopBetta account.\\n\\nYour username is [username].\\n\\nVisit the TopBetta site – http://www.topbetta.com – to login with your details.\\n\\nThank you,\\n\\nThe TopBetta Team\\nhelp@topbetta.com\nreferFriendEmailText=[custom message]\\n<h4 style="color:#0097E9; font-size: 1.5em">TopBetta - Licensed Online Betting</h4>\\nTopBetta runs live Racing & Sports Tournament Betting 7 days a week offering FREE and paid tournaments. Registration is FREE.\\n\\nEnter Racing, AFL, NRL, Soccer, Cricket, Rugby tournaments and more!\\n\\nWIN daily cash prizes or progress in a jackpot tournament for a chance to win the BIG one!\\n\\nYou can create your own exclusive Private Tournaments and invite your mates!\\n\\n<strong>To register for FREE at TopBetta just click the link below.</strong>\\n\\n<a href="[custom link]">[custom link]</a>\\n\\n<strong>Important! Make sure the referral ID is [userid]</strong>\\n\\nEnjoy the tournaments!\nwinnerNotificationEmailText=Congratulations [username]! You''re a winner in [tournament name]. [prize text]\\n\\nLogin to check out the details https://www.topbetta.com\\n\\nThanks for playing Topbetta!\\n\\nRegards,\\n\\nThe TopBetta Team\\nhelp@topbetta.com\nprivateTournamentInvitationEmailText=[promo_email_content]\\n\\nTournament name: [name]\\nTournament code: [private_identifier]\\nCategory: [category]\\n\\nSteps to enter my Private Tournament:\\n\\nStep 1\\nRegister for FREE at <a href="https://www.topbetta.com">www.topbetta.com</a>. Registration is quick and easy. Be sure to enter my Referral ID ([user_id]) - I get a reward if you do!\\n\\nStep 2\\nAfter registering, enter this tournament code "[private_identifier]" in the "Private Tournaments" box on the home page and then click "Find". You will then be taken to my Private Tournament page to enter the tournament.\\n\\nAlternatively, just click this link:\\n\\n<a href="https://www.topbetta.com/private/[private_identifier]">www.topbetta.com/private/[private_identifier]</a>\\n\\nYou must be 18 years of age and over to enter.\\n[password_protected]\\nPlease notify the sender of this communication if you do not wish to be receive further commercial messages.\nexcludeEmailText=Hello [username],\\n\\nAs per your self-exclusion request, you will not be able to log in to the Topbetta site for a period of one week. The exclusion will be lifted on [date].\\n\\nRegards,\\n\\nThe Topbetta team\\n\\nhelp@topbetta.com\nbettingReminderEmailText=Hi [username]!\\n\\nThis is a reminder from TopBetta that your jackpot tournament "[tournament name]" is now open for betting. Just click this link to go straight to the game: [link to tournament]\\n\\nIf you''d rather not receive these reminders, just go to Account Settings to turn them off.\\n\\nBest of luck in your tournament!\\n\\nRegards,\\n\\nThe TopBetta Team\\nhelp@topbetta.com\ndisclaimerText=Terms and Conditions - http://www.topbetta.com/terms-and-conditions\\nAny prices and times contained in this email are correct at the time of publication.\\n\\nThis email and any files transmitted with it are confidential and intended solely for the use of the individual or entity it is addressed. If you have received this email in error, please notify TopBetta Customer Service. The recipient should check any attachments in this email for the presence of viruses. TopBetta accepts no liability for any damage caused by any virus. \\n\\nIf you feel you may have a problem with gambling, click here: http://www.topbetta.com/responsible-gambling. If you want to talk to someone who can help with information, counselling and referral, call G-Line Australia on 1800 858 858 or Lifeline on 131114.\nblacklistWords=cock\\ncunt\\ndyke\\nfaggot\\nfag\\nfuck\\nnigger\\nprick\\nasshole\nreferral_payment=1000\nreferral_payout_threshold=5000\n\n' WHERE `id`=67;

-- Flag for jackpot email reminder
ALTER TABLE `tbdb_topbetta_user` ADD `email_jackpot_reminder_flag` TINYINT UNSIGNED NOT NULL DEFAULT '1';

-- Add simulator to menu
INSERT INTO  `tbdb_components` (
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
'73',  'Simulator',  '',  '0',  '68',  'option=com_tournament&controller=tournamentsimulator',  'Simulator',  'com_tournament',  '1',  'js/ThemeOffice/component.png',  '0',  '',  '1'
);

-- added state table
CREATE TABLE `tbdb_meeting_state` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 100 ) NOT NULL ,
INDEX ( `name` )
) ENGINE = MYISAM;

-- populated state table
INSERT INTO `tbdb_meeting_state` (`name`)
SELECT DISTINCT `state` FROM `tbdb_meeting_venue`;

-- added territory table
CREATE TABLE `tbdb_meeting_territory` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 100 ) NOT NULL ,
INDEX ( `name` )
) ENGINE = MYISAM;

-- added Australia as the first record
INSERT INTO `tbdb_meeting_territory` (
`id` ,
`name`
)
VALUES (
NULL , 'Australia'
);

SET @territory_id = LAST_INSERT_ID();

-- added state_id and territory_id into tbdb_meeting_venue
ALTER TABLE `tbdb_meeting_venue` ADD `meeting_state_id` INT( 11 ) NULL ,
ADD `meeting_territory_id` INT( 11 ) NULL ;

ALTER TABLE `tbdb_meeting_venue` ADD INDEX ( `meeting_state_id` , `meeting_territory_id` ) ;

-- populate territory_id to Australia
UPDATE `tbdb_meeting_venue` SET `meeting_territory_id` = @territory_id;

-- populate state_id
UPDATE `tbdb_meeting_venue`, `tbdb_meeting_state` SET `tbdb_meeting_venue`.meeting_state_id = `tbdb_meeting_state`.id
WHERE `tbdb_meeting_venue`.state = `tbdb_meeting_state`.name;

-- drop state field
ALTER TABLE `tbdb_meeting_venue` DROP `state`;

-- setup "Racing Meeting Venue Manager" 
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
NULL , 'Racing Meeting Venue Manager', '', '0', '68', 'option=com_tournament&controller=tournamentmeetingvenue', 'Racing Meeting Venue Manager', 'com_tournament', '0', 'js/ThemeOffice/component.png', '0', '', '1'
);

-- Creating new table for tournament comments
CREATE TABLE `tbdb_tournament_comment` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tournament_id` INT UNSIGNED NOT NULL ,
`user_id` INT UNSIGNED NOT NULL ,
`comment` TEXT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

