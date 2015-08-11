<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


class TournamentResult {

    /**
     * @var \TopBetta\Models\TournamentLeaderboardModel
     */
    private $leaderboard;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $jackpotTournament;

    public function __construct($leaderboard, $amount = 0, $jackpotTournament = null)
    {
        $this->leaderboard = $leaderboard;
        $this->amount = $amount;
        $this->jackpotTournament = $jackpotTournament;
    }

    /**
     * @return \TopBetta\Models\TournamentLeaderboardModel
     */
    public function getLeaderboard()
    {
        return $this->leaderboard;
    }

    /**
     * @param \TopBetta\Models\TournamentLeaderboardModel $leaderboard
     * @return $this
     */
    public function setLeaderboard($leaderboard)
    {
        $this->leaderboard = $leaderboard;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJackpotTournament()
    {
        return $this->jackpotTournament;
    }

    /**
     * @param mixed $jackpotTournament
     * @return $this
     */
    public function setJackpotTournament($jackpotTournament)
    {
        $this->jackpotTournament = $jackpotTournament;
        return $this;
    }
}