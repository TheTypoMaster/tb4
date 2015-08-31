<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/07/2015
 * Time: 1:32 PM
 */

namespace TopBetta\Services\Racing;


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

    /**
     * @var SelectionResultRepositoryInterface
     */
    private $resultRepository;


    public function __construct(ResultPricesRepositoryInterface $resultRepository)
    {
        $this->resultRepository = $resultRepository;
    }

    public function loadResultsForRaces($races)
    {
        foreach($races as $race) {
            $this->loadResultForRace($race);
        }

        return $races;
    }

    public function loadResultForRace($race)
    {
        if( $this->raceHasResults($race) ) {
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
            "result_string" => $this->getResultString($results),

            "results" => $this->getPositionResult($results->filter(function ($v) {
                return ! is_null($v->name);
            })),

            "exotic_results" => $this->getExoticResult($results->filter(function ($v) {
                return is_null($v->name);
            })),
        );

        return $results;
    }

    public function getResultString($results)
    {
        $string = '';
        $prevPosition = 1;

        foreach($results as $result) {
            if( $results->first() != $result ) {
                $string .= $result->position == $prevPosition ? ',' : '/';
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
                "name" => $result->name,
                "position" => (int)$result->position,
                "number" => (int)$result->number,
                "product_id" => (int) $result->product_id,
                "bet_type" => $result->bet_type,
                "dividend" => $result->dividend,
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
}