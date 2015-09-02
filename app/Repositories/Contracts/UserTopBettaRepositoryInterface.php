<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/1/15
 * File creation time: 21:27
 * Project: tb4
 */


interface UserTopBettaRepositoryInterface {

    public function updateBalanceToTurnOver($userId, $amount);

    public function updateFreeCreditWinsToTurnOver($userId, $amount);

    public function findByUserId($userId);

    public function getFullUserByEmail($email);
}