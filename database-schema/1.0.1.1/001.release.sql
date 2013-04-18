-- Renaming how to play menu item
UPDATE `tbdb_menu` SET `name` = 'How It Works' WHERE id = 9;

-- Reverting the menu item changes so that staging works as expected
UPDATE `tbdb_menu` SET `link` = 'index.php?option=com_tournament&task=upcomingtournaments&jackpot=0' WHERE `id` = 2;
UPDATE `tbdb_menu` SET `link` = 'index.php?option=com_tournament&task=upcomingtournaments&jackpot=1' WHERE `id` = 3;
