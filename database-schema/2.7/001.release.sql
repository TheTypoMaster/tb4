-- Added user pre registration table

CREATE TABLE `tbdb_user_pre_registration` (
`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`username` VARCHAR( 150 ) NOT NULL ,
`email` VARCHAR( 100 ) NOT NULL ,
`registered_flag` TINYINT( 1 ) NOT NULL ,
`created_date` DATETIME NOT NULL ,
`updated_date` DATETIME NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `tbdb_user_pre_registration` ADD INDEX ( `username` );
ALTER TABLE `tbdb_user_pre_registration` ADD INDEX ( `email` );