<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 12:10 PM
 */

namespace TopBetta\Services\Betting\BetResults;

use Log;
use TopBetta\Models\BetModel;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetDividend\BetDividendService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Betting\MarketService;
use TopBetta\Services\Betting\MultiBetService;
use TopBetta\Services\DashboardNotification\BetDashboardNotificationService;
use TopBetta\Services\UserAccount\UserAccountService;

class BetResultService {

    /**
     * @var BetRepositoryInterface
     */
    protected $betRepository;
    /**
     * @var BetTransactionService
     */
    private $betTransactionService;
    /**
     * @var MultiBetService
     */
    private $multiBetService;
    /**
     * @var BetDividendService
     */
    private $betDividendService;
    /**
     * @var UserAccountService
     */
    private $userAccountService;
    /**
     * @var BetDashboardNotificationService
     */
    private $betDashboardNotificationService;
    /**
     * @var MarketService
     */
    private $marketService;
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;
    /**
     * @var BetResultStatusRepositoryInterface
     */
    private $betResultStatusRepository;


    public function __construct(BetTransactionService $betTransactionService,
                                BetRepositoryInterface $betRepository,
                                MultiBetService $multiBetService,
                                BetDividendService $betDividendService,
                                UserAccountService $userAccountService,
                                BetDashboardNotificationService $betDashboardNotificationService,
                                MarketService $marketService,
                                EventService $eventService,
                                BetTypeRepositoryInterface $betTypeRepository,
                                BetResultStatusRepositoryInterface $betResultStatusRepository)
    {
        $this->betRepository = $betRepository;
        $this->betTransactionService = $betTransactionService;
        $this->multiBetService = $multiBetService;
        $this->betDividendService = $betDividendService;
        $this->userAccountService = $userAccountService;
        $this->betDashboardNotificationService = $betDashboardNotificationService;
        $this->marketService = $marketService;
        $this->eventService = $eventService;
        $this->betTypeRepository = $betTypeRepository;
        $this->betResultStatusRepository = $betResultStatusRepository;
    }

    public function resultBetsForMarket($market)
    {
        if( ! $this->marketService->isMarketPaying($market) ) {
            throw new \Exception("Market not paying");
        }

        $bets = $this->betRepository->getBetsForMarketByStatus(
            $market->id,
            $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id
        );

       return $this->resultBets($bets);
    }

    public function resultBetsForEvent($event)
    {
        //get bets to result
        if ( $this->eventService->isEventInterim($event) ) {
            $bets = $this->betRepository->getBetsForEventByStatus(
                $event->id,
                $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id,
                $this->betTypeRepository->getBetTypeByName(BetTypeRepositoryInterface::TYPE_WIN)->id
            );
        } else if ( $this->eventService->isEventPaying($event) ) {
            $bets = $this->betRepository->getBetsForEventByStatus(
                $event->id,
                $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED)->id
            );
        } else {
            throw new \Exception("Event not paying or interim");
        }

        return $this->resultBets($bets);

    }

    public function resultBets($bets)
    {
        //result each bet
        foreach($bets as $bet) {
            //only result multis when they are finished
            if( ! $this->multiBetService->isBetTypeMulti($bet->type->name) || $this->multiBetService->isBetPaying($bet) ) {
                $this->resultBet($bet);
            }
        }

        return true;
    }

    public function resultBet(BetModel $bet)
    {

        Log::info("RESULTING BET " . $bet->id);

        //get the dividend
        $dividend = $this->betDividendService->getResultedDividendForBet($bet);

        //get the amount
        $amount = $this->calculateBetWin($bet, $dividend);

        //create transaction
        $transaction = null;
        if ( $amount ) {
            Log::info("WINNING BET " . $bet->id ." AMOUNT " . $amount);
            $transaction = $this->betTransactionService->createBetWinTransaction($bet, $amount);
            $this->updateUserTurnOverBalance($bet, $dividend);
        } else {
            $this->updateUserTurnOverBalance($bet);
        }

        //set the bet to resulted state
        $this->setBetResulted($bet);

        //send bet to risk
        \TopBetta\RiskManagerAPI::sendBetResult($bet);

        //send bet to dashboard
        if( $transaction ) {
            $this->betDashboardNotificationService->notify(array('id' => $bet->id, "transactions" => array($transaction['id'])));
        } else {
            $this->betDashboardNotificationService->notify(array('id' => $bet->id));
        }

        return $bet;
    }

    public function calculateBetWin($bet, $dividend)
    {
        if( $bet->flexi_flag ) {
            //flexi bet use percentage
            $amount = bcmul($bet->percentage, $dividend);
        } else {
            //non flexi, use amount
            $amount = bcmul($bet->bet_amount, $dividend);
        }

        //don't return negative results
        return max($amount - $bet->bet_freebet_amount, 0);
    }

    public function calculateFreeBetWin($bet, $dividend)
    {
        if( $bet->flexi_flag ) {
            //flexi bet use percentage
            $percentage = $bet->bet_freebet_amount / $bet->combinations;
            $amount = bcmul($percentage, $dividend);
        } else {
            //non flexi, use amount
            $amount = bcmul($bet->bet_freebet_amount, $dividend);
        }

        //don't return negative results
        return max($amount - $bet->bet_freebet_amount, 0);
    }

    public function setBetResulted($bet)
    {
        //set resulted flag and update status
        return $this->betRepository->updateWithId($bet->id, array(
            'bet_result_status_id' => $this->betResultStatusRepository->getByName(BetResultStatusRepositoryInterface::RESULT_STATUS_PAID)->id,
            'resulted_flag' => true
        ));
    }

    public function updateUserTurnOverBalance($bet, $dividend = null)
    {
        //TODO: ORDER FOR TURNOVER??
        //decrease balance
        if( ! $dividend || $dividend >= 1.5 ) {
            if( $bet->bet_amount - $bet->bet_freebet_amount > 0 ) {
                $this->userAccountService->decreaseBalanceToTurnOver($bet->user_id, $bet->bet_amount - $bet->bet_freebet_amount);
            }
        }

        //increase balance
        if( $amount = $this->calculateFreeBetWin($bet, $dividend) ) {
            $this->userAccountService->addBalanceToTurnOver($bet->user_id, $amount);
        }
    }

}