<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 10:42 AM
 */

namespace TopBetta\Services\Risk;


use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Helpers\RiskManagerAPI;
use TopBetta\Services\Betting\BetSelection\ExoticRacingBetSelectionService;

class RiskExoticBetService extends AbstractRiskBetService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var ExoticRacingBetSelectionService
     */
    private $betSelectionService;

    public function __construct(BetRepositoryInterface $betRepository, ExoticRacingBetSelectionService $betSelectionService)
    {
        $this->betRepository = $betRepository;
        $this->betSelectionService = $betSelectionService;
    }

    public function sendBet($bet)
    {
        $bet = $this->betRepository->find($bet);

        $riskBet = array(
            'ReferenceId' => $bet->id,
            'EventId' => $bet->event->external_event_id,
            'BetDate' => $bet->created_at,
            'ClientId' => $bet->user->id,
            'ClientUsername' => $bet->user->username,
            'Btag' => $bet->user->topbettauser->btag,
            'Amount' => $bet->amount,
            'FreeCredit' => $bet->bet_freebet_flag,
            'FreeBetAmount' => $bet->bet_freebet_amount,
            'Type' => 'exotic',
            'BetList' => array('BetType' => $bet->type->id, 'PriceType' => 'TOP'),
            'FlexiFlag' => $bet->flexi_flag,
            'BoxedFlag' => $bet->boxed_flag,
            'Combinations' => $bet->combinations,
            'Percentage' => $bet->percentage,
            'SelectionString' => $bet->selection_string,

        );

        return RiskManagerAPI::sendRacingBet($riskBet);
    }
}