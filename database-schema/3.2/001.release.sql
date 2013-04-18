-- tbdb_user_country

CREATE TABLE `tbdb_user_country` (
	`id` 					INT(11) UNSIGNED NOT NULL auto_increment,
	`code`					VARCHAR(2) 	NOT NULL,
	`name`					VARCHAR(128) NOT NULL,
	`mobile_validation`		VARCHAR(64) NULL,
	`phone_validation`		VARCHAR(64) NULL,
	`postcode_validation`	VARCHAR(64) NULL,
	`created_date`			DATETIME NOT NULL,
	`updated_date`			DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	INDEX(`code`)
)  ENGINE=InnoDB default charset=utf8 ;


INSERT INTO tbdb_user_country
(`code`,`name`,`mobile_validation`, `phone_validation`, `postcode_validation`, `created_date`, `updated_date`)  
VALUES
('AU','Australia','^04[(\\D\\s)]?[\\d]{2}[(\\D\\s)]?[\\d]{3}[(\\D\\s)]?[\\d]{3}$', '^\\({0,1}0(2|3|7|8)\\){0,1}(\\ |-){0,1}[\\d]{4}(\\ |-){0,1}[\\d]{4}$', '^(0[289][\\d]{2})|([1345689][\\d]{3})|(2[0-8][\\d]{2})|(290[\\d])$', NOW(), NOW()),
('GB','United Kingdom','^((\\+44)\\s?|0)7([\\d]{3})[(\\D\\s)]?[\\d]{3}[(\\D\\s)]?[\\d]{3}$', '^((\\+44)\\s?|\\(?0)[1-9]{4}\\)?\\s?[\\d]{5,6}$', '^[a-zA-Z]{1,2}[0-9][0-9A-Za-z]{0,1} {0,1}[0-9][A-Za-z]{2}$', NOW(), NOW()); 
