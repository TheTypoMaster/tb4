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
use TopBetta\Services\Feeds\Racing\BetTypeMapper;
use User;

class RiskRacingWinPlaceBetService extends AbstractRiskBetService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var BetTypeMapper
     */
    private $betTypeMapper;

    public function __construct(BetRepositoryInterface $betRepository, BetTypeMapper $betTypeMapper)
    {
        $this->betRepository = $betRepository;
        $this->betTypeMapper = $betTypeMapper;
    }

    public function sendBet($bet)
    {
        $bet = $this->betRepository->find($bet);

        $riskBet = array(
            'ReferenceId' => $bet['id'],
            'BetDate' => is_string($bet->created_at) ? $bet->created_at : $bet->created_at->toDateTimeString(),
            'ClientId' => $bet->user->id,
            'ClientUsername' => $bet->user->username,
            'Btag' => $bet->user->topbettauser->btag,
            'Amount' => $bet->bet_amount,
            'FreeCredit' => $bet->bet_freebet_flag,
            'FreeBetAmount' => $bet->bet_freebet_amount,
            'Type' => 'racing',
            'BetList' => array(
                'BetType' => $this->betTypeMapper->getBetTypeShort($bet->type->name),
                'PriceType' => $bet->productProviderMatch ? $bet->productProviderMatch->provider_product_name : null,
                'Selection' => $bet->selection->first()->external_selection_id,
                'Position' => $bet->betselection->first()->position
            )
        );

        return RiskManagerAPI::sendRacingBet($riskBet);
    }
}