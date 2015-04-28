<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 1:14 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\FreeCreditTransactionService;

class TournamentBuyInService {

    /**
     * @var TournamentBuyInTypeRepositoryInterface
     */
    private $buyInTypeRepository;
    /**
     * @var TournamentTicketBuyInHistoryRepositoryInterface
     */
    private $buyInHistoryRepository;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var FreeCreditTransactionService
     */
    private $freeCreditTransactionService;

    public function __construct(TournamentBuyInTypeRepositoryInterface $buyInTypeRepository,
                                TournamentTicketBuyInHistoryRepositoryInterface $buyInHistoryRepository,
                                AccountTransactionService $accountTransactionService,
                                FreeCreditTransactionService $freeCreditTransactionService)
    {

        $this->buyInTypeRepository = $buyInTypeRepository;
        $this->buyInHistoryRepository = $buyInHistoryRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->freeCreditTransactionService = $freeCreditTransactionService;
    }

    public function getTotalRebuysForTicket($ticketId)
    {
        $typeId = $this->buyinTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        return $this->buyInHistoryRepository->getTotalByTicketAndType($ticketId, $typeId);
    }

    public function getTotalTopupsForTicket($ticketId)
    {
        $typeId = $this->buyinTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP);

        return $this->buyInHistoryRepository->getTotalByTicketAndType($ticketId, $typeId);
    }
}