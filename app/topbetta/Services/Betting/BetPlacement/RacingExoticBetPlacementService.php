<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 10:27 AM
 */

namespace TopBetta\Services\Betting\BetPlacement;


use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetSelection\ExoticRacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\Factories\ExoticBetLibraryFactory;
use TopBetta\Services\Risk\RiskExoticBetService;

class RacingExoticBetPlacementService extends AbstractBetPlacementService {

    public function __construct(ExoticRacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitRepo $betLimitRepo, RiskExoticBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitRepo, $riskBetService);
    }

    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount;
    }

    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        //format selections for old bet limit stuff
        $selectionsArray = array();
        foreach($selections as $position => $positionSelections) {
            $selectionsArray[$position] = array_map(function($v) { return $v->id; }, $positionSelections);
        }

        $result = $this->betLimitRepo->checkExceedBetLimitForBetData(array(
            'id' => $selections['first'][0]->market->event->id,
            'race_id' => $selections['first'][0]->market->event->id,
            'value' => $amount,
            'selection' => $selectionsArray,
            'bet_type_id' => $this->betTypeRepository->getBetTypeByName($betType)->id,
        ), 'racing');

        return ! $result['result'];
    }

    public function isBetValid($user, $amount, $type, $selections)
    {
        if ( ! ExoticBetLibraryFactory::make($type, $amount, $selections)->getCombinationCount() ) {
            return false;
        }

        return parent::isBetValidse($user, $amount, $type, $selections);
    }

    protected function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $library = ExoticBetLibraryFactory::make($type, abs(array_get($transactions, 'account.amount', 0)) + abs(array_get($transactions, 'free_credit.amount', 0)), $selections);

        $data = array(
            'boxed_flag' => $library->isBoxed(),
            'combinations' => $library->getCombinationCount(),
            'percentage' => $library->getFlexiPercentage(),
            'selection_string' => $this->betSelectionService->getSelectionString($selections),
            'flexi_flag' => true,
            'event_id' => $selections['first'][0]->market->event->id
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, $data);
    }


}