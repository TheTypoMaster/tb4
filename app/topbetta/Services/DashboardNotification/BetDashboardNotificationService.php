<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/03/2015
 * Time: 4:15 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\Contracts\BetRepositoryInterface;

class BetDashboardNotificationService extends AbstractTransactionDashboardNotificationService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function getEndpoint()
    {
        return "bets";
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function formatPayload($data)
    {
        if( ! array_get($data, 'id') ) {
            \Log::error("No bet id provided for dashboard notification " . print_r($data, true));
            return array();
        }

        $bet = $this->betRepository->getWithUserAndTransactions($data['id']);

        $payload = array(
            "bet_amount" => array_get($bet, 'amount', 0),
            "bet_username" => array_get($bet, 'user.username', null),
            "bet_resulted" => (bool) array_get($bet, "resulted_flag", false),
            "bet_bonus_bet" => (bool) array_get($bet, 'free_bet_flag', false),
            "bet_selection_string" => array_get($bet, "selection_string", null),
            "bet_type_name" => array_get($bet, 'type.name', null),
            "external_id" => array_get($bet, 'id', 0),
            "bet_dividend" => null, //TODO: work out what to actually pass,
            "transactions" => array(),
        );

        if($transaction = array_get($bet, 'bet_transaction', null)) {
            $payload['transactions'][] = $this->formatTransaction($transaction);
        }

        if($transaction = array_get($bet, 'result', null)) {
            $payload['transactions'][] = $this->formatTransaction($transaction);
        }

        if($transaction = array_get($bet, 'refund_transaction', null)) {
            $payload['transactions'][] = $this->formatTransaction($transaction);
        }

        return $payload;
    }

}