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

    /**
     * Places the bet
     * @param $user
     * @param $amount
     * @param $type
     * @param $origin
     * @param $selections
     * @param bool $freeCreditFlag
     * @return mixed
     * @throws BetPlacementException
     * @throws BetSelectionException
     */
    public function placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        if( ! UserAccountBalanceService::hasSufficientFunds($user, $this->getTotalAmountForBet($amount, $selections), $freeCreditFlag) ) {
            throw new BetPlacementException("Insufficient funds");
        }

        //get the selection models and validate
        $selectionModels = $this->betSelectionService->getAndValidateSelections($selections);

        //validate the bet data
        $this->validateBet($user, $amount, $type, $selectionModels);

        //create the bet and selection records
        return $this->_placeBet($user, $amount, $type, $origin, $selectionModels, $freeCreditFlag);
    }

    /**
     * Creates the bet, transaction and selection records
     * @param $user
     * @param $amount
     * @param $type
     * @param $origin
     * @param $selections
     * @param bool $freeCreditFlag
     * @return mixed
     * @throws BetPlacementException
     */
    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        //create transaction
        $transactions = $this->betTransactionService->createBetPlacementTransaction($user, $amount, $freeCreditFlag);

        if(empty($transactions)) {
            throw new BetPlacementException("Error creating transactions");
        }

        //create the bet
        try {
            $bet = $this->createBet($user, $transactions, $type, $origin, $selections);
        } catch (\Exception $e) {
            Log::error("BET PLACEMENT ERROR : " . $e->getMessage() );
            $this->betTransactionService->refund($user, array_get($transactions, 'account.amount', 0), array_get($user, array_get($transactions,'free_credit.amount', 0)));
            throw new BetPlacementException("Error storing bet");
        }

        //create selections
        try {
            $betSelections = $this->betSelectionService->createSelections($bet, $selections);
        } catch (\Exception $e) {
            Log::error("BET SELECTION ERROR : " . $e->getMessage() );
            $this->betTransactionService->refundBet($bet['id']);
            throw new BetPlacementException("Error storing bet selections");
        }

        //send bet to risk
        $this->riskBetService->sendBet($bet['id']);

        return $bet;
    }

    /**
     * Creates the bet record
     * @param $user
     * @param $transactions
     * @param $type
     * @param $origin
     * @param $selections
     * @param array $extraData
     * @return mixed
     */
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

        //merge any extra data from parent classes
        $data = array_merge($extraData, $data);

        $bet = $this->betRepository->create($data);

        return $bet;
    }

    /**
     * Checks the bet data is valid (should be overridden in inheritors)
     * @param $user
     * @param $amount
     * @param $type
     * @param $selections
     */
    public function validateBet($user, $amount, $type, $selections)
    {
        $this->checkBetLimit($user, $amount, $type, $selections);
    }

    /**
     * Calculate the total amount based on the type of bet
     * @param $amount
     * @param $selections
     * @return mixed
     */
    abstract public function getTotalAmountForBet($amount, $selections);

    /**
     * Check the user and system bet limits
     * @param $user
     * @param $amount
     * @param $betType
     * @param $selections
     * @return mixed
     */
    abstract public function checkBetLimit($user, $amount, $betType, $selections);

}