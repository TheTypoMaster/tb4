<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/07/2015
 * Time: 1:32 PM
 */

namespace TopBetta\Services\Racing;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;

class RaceResultService {

    private static $exoticResultFields = array(
        "quinella" => "quinella_dividend",
        "exacta" => "exacta_dividend",
        "trifecta" => "trifecta_dividend",
        "first_four" => "first_four_dividend",
    );

    private static $exoticTypes = array(
        BetTypeRepositoryInterface::TYPE_QUINELLA,
        BetTypeRepositoryInterface::TYPE_EXACTA,
        BetTypeRepositoryInterface::TYPE_TRIFECTA,
        BetTypeRepositoryInterface::TYPE_FIRSTFOUR,
    );

    /**
     * @var SelectionResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var SelectionResultRepositoryInterface
     */
    private $selectionResultRepository;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;


    public function __construct(ResultPricesRepositoryInterface $resultRepository,
                                SelectionResultRepositoryInterface $selectionResultRepository, BetTypeRepositoryInterface $betTypeRepository)
    {
        $this->resultRepository = $resultRepository;
        $this->selectionResultRepository = $selectionResultRepository;
        $this->betTypeRepository = $betTypeRepository;
    }

    public function loadResultsForRaces($races)
    {
        foreach($races as $race) {
            $this->loadResultForRace($race);
        }

        return $races;
    }

    public function loadResultForRace($race, $forceLoad = false)
    {
        if( $forceLoad || $this->raceHasResults($race) ) {
            $results = $this->formatForResponse($race);

            $race->setResultString($results['result_string']);
            $race->setResults($results['results']);
            $race->setExoticResults($results['exotic_results']);
        }

        return $race;
    }

    public function raceHasResults($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_INTERIM ||
        $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAYING ||
        $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAID;
    }

    public function formatForResponse($race)
    {
        $results = $this->resultRepository->getResultsForEvent($race->id);

        $results = array(
            "result_string" => $this->getResultString($race),

            "results" => $this->getPositionResult($results->filter(function ($v) {
                return ! is_null($v->name);
            })),

            "exotic_results" => $this->getExoticResult($results->filter(function ($v) {
                return is_null($v->name);
            })),
        );

        return $results;
    }

    public function getResultString($race)
    {
        $string = '';
        $prevPosition = 1;

        //get the position results
        $positionResults = $this->selectionResultRepository->getResultsForEvent($race->id);

        foreach($positionResults as $result) {
            if( $positionResults->first() != $result ) {
                $string .= $result->position== $prevPosition ? ',' : '/';
            }

            $string .= $result->number;

            $prevPosition = $result->position;
        }

        return $string;
    }

    public function getPositionResult($results)
    {

        $resultsArray = array();

        foreach($results as $result) {
            $resultArray = array(
                "position" => (int)$result->position,
                "number" => (int)$result->number,
                "product_id" => (int) $result->product_id,
                "bet_type" => $result->bet_type,
                "dividend" => $result->dividend,
                "name" => $result->name,
                'selection_id' => (int)$result->selection_id
            );

            $resultsArray[] = $resultArray;
        }

        return $resultsArray;
    }

    public function getExoticResult($results)
    {
        $exoticResults = array();

        foreach($results as $result) {

            $exoticResults[] = array(
                "name"       => $result->name,
                "selections" => $result->result_string,
                "dividend"   => $result->dividend,
                "bet_type"   => $result->bet_type,
                "product_id" => $result->product_id,
            );
       }

        return $exoticResults;
    }

    // --- STORING RESULTS ---

    public function storeDefaultPositionResults($race, $results)
    {
        $this->deleteWrongResults($race, $results);

        $products = $race->competition->first()->products;

        $betTypes = $this->betTypeRepository->getBetTypes(array(BetTypeRepositoryInterface::TYPE_WIN, BetTypeRepositoryInterface::TYPE_PLACE))->lists('id', 'name');

        foreach ($results as $result) {
            if (!($currentResult = $this->selectionResultRepository->getResultForSelectionId($result['selection']->id))) {
                $currentResult = $this->selectionResultRepository->createAndReturnModel(array(
                    "selection_id" => $result['selection']->id,
                    "position" => $result['position'],
                ));
            }

            $product = $products->filter(function ($v) use ($result) {
                return $v->bet_type == $result['bet_type'] && !$v->is_fixed_odds;
            })->first();

            if ($product) {
                $this->storePositionPrice($product, $betTypes->get($result['bet_type']), $result['dividend'], $currentResult, $race);
            }
        }
    }

    public function storeDefaultExoticResults($race, $results)
    {
        $this->deleteDefaultExoticResultsForRace($race);

        foreach ($results as $result) {

            $product = $race->competition->first()->products->filter(function ($v) use ($result) {
                return $v->bet_type == $result['bet_type'];
            })->first();

            if ($product) {
                $this->resultRepository->create(array(
                    "event_id" => $race->id,
                    "product_id" => $product->id,
                    "bet_type_id" => $product->bet_type_id,
                    "dividend" => $result['dividend'],
                    "result_string" => $result['result_string'],
                ));
            }
        }
    }

    public function deleteDefaultExoticResultsForRace($race)
    {
        $products = $race->competition->first()->products->filter(function($v) {
            return in_array($v->bet_type, self::$exoticTypes);
        });

        foreach ($products as $product) {
            $this->resultRepository->deletePricesForEventBetTypeAndProduct($race->id, $product->bet_type_id, $product->id);
        }

        return $products;
    }

    public function deleteWrongResults($race, $correctResults)
    {
        $currentResults = $this->selectionResultRepository->getResultsForEvent($race->id);

        $resultsToDelete = array();
        foreach ($currentResults as $result) {
            $correctResult = array_filter($correctResults, function ($v) use ($result) {
                return $v['selection']->id == $result->selection_id && $v['position'] == $result->position;
            });

            if (!count($correctResult)) {
                $resultsToDelete[] = $result->id;
            }
        }

        $this->resultRepository->deletePricesForResults($resultsToDelete);
    }

    public function storePositionPrice($product, $betType, $dividend, $currentResult, $race)
    {
        if ($price = $this->resultRepository->getPriceForResultByProductAndBetType($currentResult->id, $product->id, $betType)) {
            $price = $this->resultRepository->update($price, array(
                "dividend" => $dividend,
            ));
        } else {
            $price = $this->resultRepository->create(array(
                "event_id" => $race->id,
                "product_id" => $product->id,
                "bet_type_id" => $betType,
                "selection_result_id" => $currentResult->id,
                "dividend" => $dividend,
            ));
        }

        return $price;
    }
}