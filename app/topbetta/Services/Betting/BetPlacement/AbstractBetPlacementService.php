<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/05/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;

use Log;
use Carbon\Carbon;
use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Accounting\UserAccountBalanceService;
use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Risk\AbstractRiskBetService;

abstract class AbstractBetPlacementService {

    /**
     * @var BetRepositoryInterface
     */
    protected $betRepository;
    /**
     * @var BetTypeRepositoryInterface
     */
    protected $betTypeRepository;
    /**
     * @var AbstractBetSelectionService
     */
    protected $betSelectionService;
    /**
     * @var BetTransactionService
     */
    protected $betTransactionService;
    /**
     * @var BetLimitRepo
     */
    protected $betLimitRepo;
    /**
     * @var AbstractRiskBetService
     */
    protected $riskBetService;

    public function __construct(AbstractBetSelectionService $betSelectionService, BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, AbstractRiskBetService $riskBetService)
    {
        $this->betRepository = $betRepository;
        $this->betTypeRepository = $betTypeRepository;
        $this->betSelectionService = $betSelectionService;
        $this->betTransactionService = $betTransactionService;
        $this->betLimitRepo = $betLimitRepo;
        $this->riskBetService = $riskBetService;
    }

    public function placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        if( ! UserAccountBalanceService::hasSufficientFunds($user, $this->getTotalAmountForBet($amount, $selections), $freeCreditFlag) ) {
            throw new BetPlacementException("Insufficient funds");
        }

        $selectionModels = $this->betSelectionService->getAndValidateSelections($selections);

        $this->validateBet($user, $amount, $type, $selectionModels);

        return $this->_placeBet($user, $amount, $type, $origin, $selectionModels, $freeCreditFlag);
    }

    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        //create transaction
        $transactions = $this->betTransactionService->createBetPlacementTransaction($user, $amount, $freeCreditFlag);

        if(empty($transactions)) {
            throw new BetPlacementException("Error creating transactions");
        }

        try {
            $bet = $this->createBet($user, $transactions, $type, $origin, $selections);
        } catch (\Exception $e) {
            Log::error("BET PLACEMENT ERROR : " . $e->getMessage() );
            $this->betTransactionService->refund($user, array_get($transactions, 'account.amount', 0), array_get($user, array_get($transactions,'free_credit.amount', 0)));
            throw new BetPlacementException("Error storing bet");
        }

        try {
            $betSelections = $this->betSelectionService->createSelections($bet, $selections);
        } catch (\Exception $e) {
            Log::error("BET SELECTION ERROR : " . $e->getMessage() );
            $this->betTransactionService->refundBet($bet['id']);
            throw new BetPlacementException("Error storing bet selections");
        }

        $this->riskBetService->sendBet($bet['id']);

        return $bet;
    }

    protected function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $data = array(
            'user_id' => $user->id,
            'bet_amount' => abs(array_get($transactions, 'account.amount', 0)) + abs(array_get($transactions, 'free_credit.amount', 0)),
            'bet_type_id' => $this->betTypeRepository->getBetTypeByName($type)->id,
            'bet_result_status_id' => 1,

            //what to do here?
            'bet_origin_id' => $origin,
            'bet_product_id' => $origin,

            'bet_transaction_id' => array_get($transactions, 'account.id', 0),
            'bet_freebet_transaction_id' => array_get($transactions, 'free_credit.id', 0),
            'created_date' => Carbon::now(),
            'updated_date' => Carbon::now(),
            'bet_freebet_flag' => isset($transactions['free_credit']),
            'bet_freebet_amount' => abs(array_get($transactions, 'free_credit.amount', 0)),

            //bet source?
        );

        $data = array_merge($extraData, $data);

        $bet = $this->betRepository->create($data);

        return $bet;
    }

    public function validateBet($user, $amount, $type, $selections)
    {
        $this->checkBetLimit($user, $amount, $type, $selections);
    }

    abstract public function getTotalAmountForBet($amount, $selections);

    abstract public function checkBetLimit($user, $amount, $betType, $selections);

}