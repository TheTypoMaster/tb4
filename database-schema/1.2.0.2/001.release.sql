-- update winner list page
UPDATE `tbdb_content`
SET `title` = 'Winners List',
`alias` = 'winners-list',
`introtext` = '<div style="text-align: center; margin: auto; width: 930px; padding-top: 15px;"><img src="components/com_tournament/images/winners/winners_list.jpg" border="0" alt="Winner List" /></div>'
WHERE `tbdb_content`.`id` =7;