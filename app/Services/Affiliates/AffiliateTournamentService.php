<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 10:55 AM
 */

namespace TopBetta\Services\Affiliates;


use TopBetta\Repositories\Contracts\AffiliateEndpointRepositoryInterface;
use TopBetta\Repositories\Contracts\AffiliateTypeRepositoryInterface;
use TopBetta\Services\Affiliates\Exceptions\AffiliateMessageException;
use TopBetta\Services\Affiliates\Exceptions\AffiliateResponseException;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;
use TopBetta\Services\Tournaments\TournamentService;

class AffiliateTournamentService {

    private static $externallyResulted = array(
        AffiliateTypeRepositoryInterface::AFFILIATE_TYPE_TOURNAMENT_ONLY,
    );

    private static $externallyEntered = array(
        AffiliateTypeRepositoryInterface::AFFILIATE_TYPE_TOURNAMENT_ONLY,
    );

    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var AffiliateMessageService
     */
    private $messageService;

    public function __construct(TournamentService $tournamentService, AffiliateMessageService $messageService)
    {
        $this->tournamentService = $tournamentService;
        $this->messageService = $messageService;
    }

    public function enterUserInTournament($user, $tournament)
    {
        if (! is_object($tournament)) {
            $tournament = $this->tournamentService->getTournament($tournament);
        }

        try {
            $this->messageService->sendMessage(
                $user->affiliate,
                AffiliateEndpointRepositoryInterface::TYPE_TOURNAMENT_ENTRY,
                array(
                    "tournament_username" => $user->username,
                    "external_unique_identifier" => $user->external_user_id,
                    "purchase_amount" => $tournament->buy_in + $tournament->entry_fee,
                    "tournament_id" => $tournament->id,
                )
            );
        } catch (AffiliateMessageException $e) {
            throw new TournamentEntryException("Unable to enter tournament:  " . array_get($e->getResponse(), 'response', 'no response'));
        } catch (AffiliateResponseException $e) {
            throw new TournamentEntryException("Unable to enter tournament: " . $e->getResponse());
        }

        return $this->tournamentService->createTicketAndLeaderboardRecordForUser($tournament, $user);
    }

    public function affiliateIsExternallyResulted($affiliate)
    {
        return in_array($affiliate->type->affiliate_type_name, self::$externallyResulted);
    }
}