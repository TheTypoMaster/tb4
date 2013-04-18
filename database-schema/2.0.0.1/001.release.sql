-- Add date fields to tournament_market

ALTER TABLE  `tbdb_tournament_market` ADD  `created_date` DATETIME NOT NULL AFTER  `refund_flag` ,
ADD  `updated_date` DATETIME NOT NULL AFTER  `created_date` ;

-- Set current date time
UPDATE `tbdb_tournament_market` SET `created_date` = NOW(), `updated_date` = NOW() WHERE `id` > 0;
