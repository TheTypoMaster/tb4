<?php

namespace TopBetta\Console\Commands\DevTools;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TopBetta\Repositories\Cache\Tournaments\TournamentCommentRepository;
use TopBetta\Repositories\Cache\Tournaments\TournamentLeaderboardRepository;
use TopBetta\Repositories\Cache\Tournaments\TournamentRepository;
use TopBetta\Repositories\Contracts\TournamentCommentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;

class PopulateTournamentCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:populate-tournament-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var TournamentLeaderboardRepositoryInterface
     */
    private $leaderboardRepository;
    /**
     * @var TournamentCommentRepositoryInterface
     */
    private $commentRepository;
    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $dbLeaderboardRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TournamentRepository $tournamentRepository, TournamentLeaderboardRepository $leaderboardRepository, TournamentCommentRepository $commentRepository, DbTournamentLeaderboardRepository $dbLeaderboardRepository)
    {
        parent::__construct();
        $this->tournamentRepository = $tournamentRepository;
        $this->leaderboardRepository = $leaderboardRepository;
        $this->commentRepository = $commentRepository;
        $this->dbLeaderboardRepository = $dbLeaderboardRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tournaments = \TopBetta\Models\TournamentModel::where('end_date', '>=', Carbon::now()->startOfDay())->get();

        foreach ($tournaments as $tournament) {
            $this->tournamentRepository->makeCacheResource($tournament);

            $leaderboard = $this->dbLeaderboardRepository->getTournamentLeaderboardInOrder($tournament->id);

            $this->leaderboardRepository->insertLeaderboard($tournament, $leaderboard);

            $comments = $tournament->comments()->orderBy('created_at', 'DESC')->get();

            $this->commentRepository->insertComments($tournament, $comments);
        }
    }
}
