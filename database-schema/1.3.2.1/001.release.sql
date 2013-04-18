ALTER TABLE `tbdb_result` ADD `payout_flag` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `position`;

-- update flemington
UPDATE
  `tbdb_tournament`
SET
  `start_date` = '2011-02-19 10:50:00',
  `end_date` = '2011-02-19 18:30:00'
WHERE
  id = 2943;

-- update moonee
UPDATE
  `tbdb_tournament`
SET
  `start_date` = '2011-02-18 17:30:00',
  `end_date` = '2011-02-18 22:30:00'
WHERE
  id = 2945;

-- update canterbury
UPDATE
  `tbdb_tournament`
SET
  `start_date` = '2011-02-18 18:30:00',
  `end_date` = '2011-02-18 23:00:00'
WHERE
  id = 2944;
