<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 10:18 AM
 */

namespace TopBetta\Services\Risk;

use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Helpers\RiskManagerAPI;
use User;

class RiskRacingWinPlaceBetService extends AbstractRiskBetService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function sendBet($bet)
    {
        $bet = $this->betRepository->find($bet);

        $riskBet = array(
            'ReferenceId' => $bet['id'],
            'BetDate' => $bet['created_at'],
            'ClientId' => $bet->user->id,
            'ClientUsername' => $bet->user->username,
            'Btag' => $bet->user->topbettauser->btag,
            'Amount' => $bet->amount,
            'FreeCredit' => $bet->bet_freebet_flag,
            'FreeBetAmount' => $bet->bet_freebet_amount,
            'Type' => 'racing',
            'BetList' => array(
                'BetType' => $bet->type->name,
                'PriceType' => $bet->product->name,
                'Selection' => $bet->betselection->first()->selection_id,
                'Position' => $bet->betselection->first()->position
            )
        );

        return RiskManagerAPI::sendRacingBet($riskBet);
    }
}