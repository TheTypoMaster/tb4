--
-- Setup system income access plugin
--
INSERT INTO `tbdb_plugins` (`id`, `name`, `element`, `folder`, `access`,
`ordering`, `published`, `iscore`, `client_id`, `checked_out`,
`checked_out_time`, `params`) VALUES
(null, 'System - Income Access', 'incomeaccess', 'system', 0, 0, 1, 0, 0, 0,
'0000-00-00 00:00:00', '');