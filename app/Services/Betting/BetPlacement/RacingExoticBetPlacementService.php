<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 10:27 AM
 */

namespace TopBetta\Services\Betting\BetPlacement;

use Lang;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\Betting\BetLimitValidation\BetLimitValidationService;
use TopBetta\Services\Betting\BetProduct\BetProductValidator;
use TopBetta\Services\Betting\BetSelection\ExoticRacingBetSelectionService;
use TopBetta\Services\Betting\BetTransaction\BetTransactionService;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\Factories\ExoticBetLibraryFactory;
use TopBetta\Services\Risk\RiskExoticBetService;

class RacingExoticBetPlacementService extends AbstractBetPlacementService {

    protected $product;

    public function __construct(ExoticRacingBetSelectionService $betSelectionService,  BetTransactionService $betTransactionService, BetRepositoryInterface $betRepository, BetTypeRepositoryInterface $betTypeRepository, BetLimitValidationService $betLimitService, RiskExoticBetService $riskBetService)
    {
        parent::__construct($betSelectionService, $betTransactionService, $betRepository, $betTypeRepository, $betLimitService, $riskBetService);
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountForBet($amount, $selections)
    {
        return $amount;
    }

    /**
     * @inheritdoc
     */
    public function checkBetLimit($user, $amount, $betType, $selections)
    {
        $exoticBetLibrary = ExoticBetLibraryFactory::make($betType, $amount, $this->betSelectionService->formatSelectionsForExoticLibrary($selections));

        $betLimitData = array(
            'amount' => $amount,
            'user' => $user->id,
            'bet_type' => $this->betTypeRepository->getBetTypeByName($betType),
            'event' => $selections[0]['selection']->market->event_id,
            'selections' => $selections,
            'percentage' => $exoticBetLibrary->getFlexiPercentage(),
        );

        $this->betLimitService->validateBet($betLimitData);

    }

    /**
     * @inheritdoc
     */
    public function validateBet($user, $amount, $type, $selections)
    {
        $exoticBetLibrary = ExoticBetLibraryFactory::make($type, $amount, $this->betSelectionService->formatSelectionsForExoticLibrary($selections));

        //check valid combinations
        if ( ! $exoticBetLibrary->getCombinationCount() ) {
            throw new BetPlacementException("Invalid selections for " . $type);
        }

        //don't have too many selections
        if( ! $exoticBetLibrary->isBoxed() && $exoticBetLibrary->getPositionSelectionCount() < max(array_fetch($selections, 'position')) ) {
            throw new BetPlacementException("Too many positions selected for " . $type);
        }

        //no exotics on international events
        if( EventService::isEventInternational($selections[0]['selection']->market->event) ) {
            throw new BetPlacementException(Lang::get('bets.bet_type_not_valid_international'));
        }

        $this->validateProduct($selections);

        parent::validateBet($user, $amount, $type, $selections);
    }

    /**
     * @inheritdoc
     */
    protected function createBet($user, $transactions, $type, $origin, $selections, $extraData = array())
    {
        $library = ExoticBetLibraryFactory::make(
            $type,
            abs(array_get($transactions, 'account.amount', 0)) + abs(array_get($transactions, 'free_credit.amount', 0)),
            $this->betSelectionService->formatSelectionsForExoticLibrary($selections)
        );

        //add the combinations etc.
        $data = array(
            'boxed_flag' => $library->isBoxed(),
            'combinations' => $library->getCombinationCount(),
            'percentage' => $library->getFlexiPercentage(),
            'selection_string' => $this->betSelectionService->getSelectionString($selections),
            'flexi_flag' => true,
            'event_id' => $selections[0]['selection']->market->event->id
        );

        return parent::createBet($user, $transactions, $type, $origin, $selections, $data);
    }

    /**
     * Validate product
     * @param $selections
     */
    protected function validateProduct($selections)
    {
        $meeting = $selections[0]['selection']->market->event->competition->first();

        $validator = BetProductValidator::make($meeting);

        $validator->validateProduct($this->product, $this->betType);
    }

}