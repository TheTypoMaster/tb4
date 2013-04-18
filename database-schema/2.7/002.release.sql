-- add mobile field to pre registration

ALTER TABLE `tbdb_user_pre_registration` ADD `msisdn` VARCHAR( 15 ) NOT NULL COMMENT 'mobile number' AFTER `email` ;