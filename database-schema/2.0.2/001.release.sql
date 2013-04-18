-- Alter session_tracking table, add some indexing
ALTER TABLE  `tbdb_session_tracking` ADD INDEX  `user_id-session_id` (  `user_id` ,  `session_id` );
ALTER TABLE  `tbdb_session_tracking` ADD INDEX  `session_start` (  `session_start` );
