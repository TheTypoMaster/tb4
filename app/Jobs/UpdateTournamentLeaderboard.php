<?php

namespace TopBetta\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use TopBetta\Models\TournamentLeaderboardModel;
use TopBetta\Repositories\Cache\Tournaments\TournamentLeaderboardRepository;

class UpdateTournamentLeaderboard extends Command implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const QUEUE_TUBE = 'leaderboard-update';

    public $queue;
    /**
     * @var TournamentLeaderboardModel
     */
    private $leaderboardModel;
    /**
     * @var TournamentLeaderboardRepository
     */
    private $leaderboardRepository;

    /**
     * Create a new command instance.
     *
     * @param TournamentLeaderboardModel $leaderboardModel
     */
    public function __construct(TournamentLeaderboardModel $leaderboardModel)
    {
        $this->leaderboardModel = $leaderboardModel;
        $this->queue = self::QUEUE_TUBE;
    }

    /**
     * Execute the command.
     *
     * @param TournamentLeaderboardRepository $leaderboardRepository
     */
    public function handle(TournamentLeaderboardRepository $leaderboardRepository)
    {
        $leaderboardRepository->updateCacheLeaderboard($this->leaderboardModel);
    }
}
