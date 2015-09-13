<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 11:00 AM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Services\Betting\BetSelection\AbstractBetSelectionService;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;

class TournamentBetSelectionService {

    /**
     * @var AbstractBetSelectionService
     */
    private $betSelectionService;


    public function __construct(AbstractBetSelectionService $betSelectionService)
    {
        $this->betSelectionService = $betSelectionService;
        $this->betSelectionRepository = \App::make('TopBetta\Repositories\DbTournamentBetSelectionRepository');
    }

    public function getAndValidateSelections($selections, $tournament)
    {
        $selections = $this->betSelectionService->getAndValidateSelections($selections);

        $this->validateTournamentSelections(array_unique(array_pluck($selections, 'selection')), $tournament);

        return $selections;
    }

    public function validateTournamentSelections($selections, $tournament)
    {
        foreach( $selections as $selection ) {
            $this->validateTournamentSelection($selection, $tournament);
        }
    }

    public function validateTournamentSelection($selection, $tournament)
    {
        if( ! $this->selectionBelongsToTournament($selection, $tournament) ) {
            throw new BetSelectionException($selection, "Selection not found in tournament " . $tournament->name);
        }
    }

    public function selectionBelongsToTournament($selection, $tournament)
    {
        return in_array($selection->market->event->id, $tournament->competition->events()->get()->lists('id')->all());
    }

    public function createBetSelections($bet, $selections)
    {
        foreach($selections as $selection) {
            $this->createBetSelection($bet, $selection);
        }
    }

    public function createBetSelection($bet, $selection)
    {
        $this->betSelectionRepository->create(array(
            'tournament_bet_id' => $bet,
            'selection_id' => $selection['selection']->id
        ));
    }

    public function setWinProduct($product)
    {
        $this->betSelectionService->setWinProduct($product);
        return $this;
    }

    public function setPlaceProduct($product)
    {
        $this->betSelectionService->setPlaceProduct($product);
        return $this;
    }

}