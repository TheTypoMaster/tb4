<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/07/2015
 * Time: 1:32 PM
 */

namespace TopBetta\Services\Racing;


use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;

class RaceResultService extends RacingResourceService {

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


    public function __construct(SelectionResultRepositoryInterface $resultRepository)
    {
        $this->resultRepository = $resultRepository;
    }

    public function formatForResponse($race)
    {
        $results = $this->resultRepository->getResultsForRace($race->id);

        $results = array(
            "result_string" => $this->getResultString($results),
            "results" => $this->getPositionResult($results),
            "exotic_results" => $this->getExoticResult($race)
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
                "position" => $result->position,
                "number" => $result->number,
                "place_dividend" => $result->place_dividend
            );

            if( $result->position == 1 ) {
                $resultArray['win_dividend'] = $result->win_dividend;
            }

            $resultsArray[] = $resultArray;
        }

        return $resultsArray;
    }

    public function getExoticResult($race)
    {
        $exoticResults = array();

        foreach(self::$exoticResultFields as $name => $field) {

            $results = unserialize($race->{$field});

            if( $results ) {
                foreach ($results as $selectionString => $dividend) {
                    $exoticResults[] = array(
                        "name"       => $name,
                        "selections" => $selectionString,
                        "dividend"   => $dividend,
                    );
                }
            }
        }

        return $exoticResults;
    }
}