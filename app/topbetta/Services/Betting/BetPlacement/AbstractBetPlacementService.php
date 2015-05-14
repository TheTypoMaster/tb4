<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/05/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Betting\MarketService;
use TopBetta\Services\Betting\SelectionService;

abstract class AbstractBetPlacementService {

    /**
     * @var SelectionService
     */
    private $selectionService;
    /**
     * @var MarketService
     */
    private $marketService;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;

    public function __construct(SelectionService $selectionService, MarketService $marketService, EventService $eventService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository)
    {
        $this->selectionService = $selectionService;
        $this->marketService = $marketService;
        $this->eventService = $eventService;
        $this->betRepository = $betRepository;
        $this->betTypeRepository = $betTypeRepository;
    }

    /**
     * TODO: Abstract somewhere else
     * @param $user
     * @param $amount
     * @param bool $freeCreditFlag
     * @return bool
     */
    public function checkSufficientFunds($user, $amount, $freeCreditFlag = false)
    {
        if ( $freeCreditFlag ) {
            //free credit so check user has enough free credit or account balance to cover
            $freeCreditBalance = $user->freeCreditBalance();

            if( $freeCreditBalance >= $amount ) { return true; }
            else if ($freeCreditBalance + $user->accountBalance() >= $amount ) { return true; }

        } else {
            //not free credit so just check account balance
            if( $user->accountBalance() > $amount ) { return true; }
        }

        return false;
    }

    public function validateSelection($selection)
    {
        if( ! $this->selectionService->isSelectionAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.selection_scratched"));
        }

        if ( ! $this->marketService->isSelectionMarketAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.market_closed"));
        }

        if( ! $this->eventService->isSelectionEventAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.market_closed"));
        }
    }

    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        //create transaction
        $transactions = $this->betTransactionService->createBetPlacementTransaction($user, $amount, $freeCreditFlag);

        $bet = $this->createBet($user, $transactions, $type, $origin);

        $betSelections = $this->createSelections($bet, $selections);

        return $bet;
    }

    protected function createBet($user, $transactions, $type, $origin, $extraData = array())
    {
        $data = array(
            'user_id' => $user->id,
            'bet_amount' => array_get($transactions, 'account.amount', 0) + array_get($transaction, 'free_credit.amount', 0),
            'bet_type_id' =>
        );
        return array();
    }

    abstract public function getSportOrRacing();

    abstract public function validateSelections($amount, $selections);

    abstract public function placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false);

}