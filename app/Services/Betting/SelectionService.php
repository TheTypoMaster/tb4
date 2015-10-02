<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/02/2015
 * Time: 3:47 PM
 */

namespace TopBetta\Services\Betting;


use TopBetta\Models\BetModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionStatusRepositoryInterface;

class SelectionService {

    //do in DB
    const SELECTION_NOT_SCRATCHED = "not scratched";

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var SelectionStatusRepositoryInterface
     */
    private $selectionStatusRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository, SelectionStatusRepositoryInterface $selectionStatusRepository, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->selectionRepository = $selectionRepository;
        $this->selectionStatusRepository = $selectionStatusRepository;
        $this->competitionRepository = $competitionRepository;
    }

    /**
     * Gets the selection model by id
     * @param $selectionId
     * @return mixed
     */
    public function getSelection($selectionId)
    {
        return $this->selectionRepository->find($selectionId);
    }

    /**
     * Checks if selection is open and not scratched
     * @param $selection
     * @return bool
     */
    public function isSelectionAvailableForBetting($selection)
    {
        if( is_int($selection ) ) {
            $selection = $this->getSelection($selection);
        }

        if( $selection->selection_status_id == $this->selectionStatusRepository->getSelectionStatusIdByName(self::SELECTION_NOT_SCRATCHED) && $selection->display_flag ) {
            return true;
        }

        return false;
    }

    public function isSelectionRacing($selectionId)
    {
        $event = $this->competitionRepository->getCompetitionBySelection($selectionId);

        return $event->sport_id == 0;
    }

    public function isSelectionSports($selectionId)
    {
        $event = $this->competitionRepository->getCompetitionBySelection($selectionId);

        return $event->sport_id > 0;
	}

    public function selectionsBelongToSameEvent($selections)
    {
        $event = null;

        foreach($selections as $selection) {

            if( is_int($selection) ) {
                $selection = $this->getSelection($selection);
            }

            if( is_null($event ) ) {
                $event = $selection->market->event;
            } else if( $event->id != $selection->market->event->id ) {
                return false;
            }
        }

        return true;
    }
	
    public function calculatePrice($selectionPrice, $overrideOdds, $overrideType)
    {
        if ($overrideType == 'percentage') {
            return bcmul(2 - $overrideOdds, $selectionPrice, 2);
        } else if ($overrideType == 'promo') {
            return $overrideOdds;
        } else if ($overrideType == 'price') {
            return min($selectionPrice, $overrideOdds);
        }

        return $selectionPrice;
    }

    public function calculatePriceForSelection($selectionId)
    {
        $selection = $this->selectionRepository->find($selectionId);

        return $this->calculatePrice($selection->price->win_odds, $selection->price->override_odds, $selection->price->override_type);
    }

    public function oddsChanged($selectionId, $price)
    {
        return $this->calculatePriceForSelection($selectionId) != $price;
    }

    public function totalDeduction($market, $betType)
    {
        $deductionsField = $this->getDeductionField($betType);

        $selections = $this->selectionRepository->getSelectionsByMarket($market);

        $deductions = 0;
        foreach ($selections as $selection) {
            $deductions += $selection->{$deductionsField};
        }

        return min(100, $deductions);
    }

    protected function getDeductionField($betType)
    {
        if ($betType == BetTypeRepositoryInterface::TYPE_WIN) {
            return  'win_deductions';
        } else if ($betType == BetTypeRepositoryInterface::TYPE_PLACE) {
            return 'place_deductions';
        }

        throw new \InvalidArgumentException("SelectionService: Invalid deduction type " . $betType);
    }

    /**
     * @param $bet BetModel
     * @return int
     */
    public function totalDeductionsForBet($bet)
    {
        $deductionsField = $this->getDeductionField($bet->type->name);

        $selections = $this->selectionRepository->getSelectionsByMarket($bet);

        $selections = $this->selectionRepository->filter(function ($v) use ($bet) {
             return $v->scratching_time && $v->scratchin_time >= $bet->created_at;
        });

        $deductions = 0;
        foreach ($selections as $selection) {
            $deductions += $selection->{$deductionsField};
        }

        return min(100, $deductions);
    }


}