-- Change tables to use InnoDB

ALTER TABLE `tbdb_tournament_bet` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_bet_selection` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_audit` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_leaderboard` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_ticket` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_transaction` ENGINE = InnoDB ;
ALTER TABLE `tbdb_account_transaction` ENGINE = InnoDB ;
ALTER TABLE `tbdb_tournament_private` ENGINE = InnoDB ;