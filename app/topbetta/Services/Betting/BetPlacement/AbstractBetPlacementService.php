<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/05/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;

abstract class AbstractBetPlacementService {

    /**
     * @var BetRepositoryInterface
     */
    protected $betRepository;
    /**
     * @var BetTypeRepositoryInterface
     */
    protected $betTypeRepository;

    protected $betSelectionService;

    public function __construct(AbstractBetSelectionService $betSelectionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository)
    {
        $this->betRepository = $betRepository;
        $this->betTypeRepository = $betTypeRepository;
        $this->betSelectionService = $betSelectionService;
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



    protected function _placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false)
    {
        //create transaction
        $transactions = $this->betTransactionService->createBetPlacementTransaction($user, $amount, $freeCreditFlag);

        $bet = $this->createBet($user, $transactions, $type, $origin);

        $betSelections = $this->betSelectionService->createSelections($bet, $selections);

        return $bet;
    }

    protected function createBet($user, $transactions, $type, $origin, $extraData = array())
    {
        $data = array(
            'user_id' => $user->id,
            'bet_amount' => array_get($transactions, 'account.amount', 0) + array_get($transactions, 'free_credit.amount', 0),
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
            'bet_freebet_amount' => array_get($transactions, 'free_credit.amount', 0),

            //bet source?
        );

        $data = array_merge($extraData, $data);

        $bet = $this->betRepository->create($data);

        return $bet;
    }

    abstract public function placeBet($user, $amount, $type, $origin, $selections, $freeCreditFlag = false);

}