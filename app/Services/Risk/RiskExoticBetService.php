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
use TopBetta\Services\Feeds\Racing\BetTypeMapper;

class RiskExoticBetService extends AbstractRiskBetService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var ExoticRacingBetSelectionService
     */
    private $betSelectionService;
    /**
     * @var BetTypeMapper
     */
    private $betTypeMapper;

    public function __construct(BetRepositoryInterface $betRepository, ExoticRacingBetSelectionService $betSelectionService, BetTypeMapper $betTypeMapper)
    {
        $this->betRepository = $betRepository;
        $this->betSelectionService = $betSelectionService;
        $this->betTypeMapper = $betTypeMapper;
    }

    public function sendBet($bet)
    {
        $riskBet = array(
            'ReferenceId' => $bet->id,
            'EventId' => $bet->event->external_event_id,
            'BetDate' => is_string($bet->created_at) ? $bet->created_at : $bet->created_at->toDateTimeString() ,
            'ClientId' => $bet->user->id,
            'ClientUsername' => $bet->user->username,
            'Btag' => $bet->user->topbettauser->btag,
            'Amount' => $bet->bet_amount,
            'FreeCredit' => $bet->bet_freebet_flag,
            'FreeBetAmount' => $bet->bet_freebet_amount,
            'Type' => 'exotic',
            'BetList' => array('BetType' => $this->betTypeMapper->getBetTypeShort($bet->type->name), 'PriceType' => $bet->productProviderMatch ? $bet->productProviderMatch->provider_product_name : null),
            'FlexiFlag' => $bet->flexi_flag,
            'BoxedFlag' => $bet->boxed_flag,
            'Combinations' => $bet->combinations,
            'Percentage' => $bet->percentage,
            'SelectionString' => $bet->selection_string,

        );

        return RiskManagerAPI::sendRacingBet($riskBet);
    }
}