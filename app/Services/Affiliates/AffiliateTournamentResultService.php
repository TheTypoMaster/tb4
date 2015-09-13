<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 12:19 PM
 */

namespace TopBetta\Services\Affiliates;


use TopBetta\Repositories\Contracts\AffiliateEndpointRepositoryInterface;
use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;
use TopBetta\Repositories\Contracts\AffiliateTypeRepositoryInterface;
use TopBetta\Services\Affiliates\Exceptions\AffiliateMessageException;
use TopBetta\Services\Affiliates\Exceptions\AffiliateResponseException;
use TopBetta\Services\Tournaments\Resulting\TournamentResultService;

class AffiliateTournamentResultService {

    private static $notifyResultsTypes = array(
        AffiliateTypeRepositoryInterface::AFFILIATE_TYPE_TOURNAMENT_ONLY,
    );

    /**
     * @var AffiliateRepositoryInterface
     */
    private $affiliateRepository;
    /**
     * @var TournamentResultService
     */
    private $resultService;
    /**
     * @var AffiliateMessageService
     */
    private $affiliateMessageService;

    public function __construct(AffiliateRepositoryInterface $affiliateRepository,
                                TournamentResultService $resultService,
                                AffiliateMessageService $affiliateMessageService)
    {
        $this->affiliateRepository = $affiliateRepository;
        $this->resultService = $resultService;
        $this->affiliateMessageService = $affiliateMessageService;
    }

    public function sendResultNotifications($tournament)
    {
        $affiliates = $this->affiliateRepository->getAffiliatesInTournamentByTypes($tournament->id, self::$notifyResultsTypes);

        if (!$affiliates->count()) {
            return $this;
        }

        $results = $this->resultService->getTournamentResults($tournament);

        foreach($affiliates as $affiliate) {
            $affiliateResults = $results->filter(function ($v) use ($affiliate) { return $v->getTicket()->user->affiliate_id == $affiliate->affiliate_id; });
            $this->sendResults($tournament, $affiliate, $affiliateResults);
        }

        return $this;
    }

    private function sendResults($tournament, $affiliate, $affiliateResults)
    {
        $data = array(
            "tournament_id" => $tournament->id,
            "results" => array(),
        );

        foreach ($affiliateResults as $result) {
            $data['results'][] = (new AffiliateTournamentResult($result))->toArray();
        }

        try {
            $response = $this->affiliateMessageService->sendMessage($affiliate, AffiliateEndpointRepositoryInterface::TYPE_TOURNAMENT_RESULTS, $data);
        } catch (AffiliateMessageException $e) {
            \Log::error("AffiliateTournamentResultService: Error sending result notifcation to " . $affiliate->affiliate_id . " for tournament " . $result->getTicket()->tournament->id);
        } catch (AffiliateResponseException $e) {
            \Log::error("AffiliateTournamentResultService: Response error sending result notifcation to " . $affiliate->affiliate_id . " for tournament " . $result->getTicket()->tournament->id);
            \Log::error("AffiliateTournamentResultService: Response " . $e->getResponse());
        }

        return $response;

    }
}