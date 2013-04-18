-- Add new wagering API
INSERT INTO  `tbdb_wagering_api` (`id` ,`keyword` ,`name` ,`description` ,`updated_date` ,`created_date`) VALUES (NULL ,  'tob',  'The Odds Broker',  'The odds broker api', NOW( ) , NOW( ));

-- Alter race external_id type and add column
ALTER TABLE  `tbdb_event` CHANGE  `external_event_id`  `external_event_id` VARCHAR( 64 ) NOT NULL;
ALTER TABLE  `tbdb_event` ADD  `external_race_pool_id_list` TEXT NULL AFTER  `external_event_id` ;

INSERT INTO `tbdb_bet_result_status` (`id`, `name`, `description`, `status_flag`, `created_date`, `updated_date`) VALUES(5, 'pending', NULL, 1, now(), now());

INSERT INTO  `tbdb_bet_product` (`id` ,`keyword` ,`name` ,`created_date` ,`updated_date`) VALUES (NULL ,  'supertab-ob',  'SuperTab', NOW() , NOW());

ALTER TABLE  `tbdb_bet` ADD  `external_bet_error_message` VARCHAR( 200 ) NULL;
