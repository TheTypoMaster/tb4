<?php

namespace TopBetta\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Bus\SelfHandling;
use TopBetta\Models\TournamentModel;
use TopBetta\Services\Affiliates\AffiliateTournamentResultService;

class AffiliateTournamentResultNotifier extends Command implements SelfHandling, ShouldQueue
{
    use SerializesModels;

    protected $queue;
    /**
     * @var TournamentModel
     */
    private $tournament;

    /**
     * Create a new command instance.
     * @param TournamentModel $tournament
     */
    public function __construct(TournamentModel $tournament)
    {
        $this->tournament = $tournament;
        $this->queue = \Config::get('externalsource.queue');
    }

    /**
     * Execute the command.
     *
     * @param AffiliateTournamentResultService $resultService
     * @return bool
     */
    public function handle(AffiliateTournamentResultService $resultService)
    {
        try {
            $resultService->sendResultNotifications($this->tournament);
        } catch (\Exception $e) {
            \Log::error("AffiliateTournamentResultNotifier: Error sending affiliate results for tournament " . $this->tournament->id . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }
    }
}
