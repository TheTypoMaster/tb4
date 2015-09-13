<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/08/2015
 * Time: 2:39 PM
 */

namespace TopBetta\Services\Betting\BetProduct\Exceptions;


class ProductNotAvailableException extends \Exception {

    private $competition;

    public function __construct($competition, $message)
    {
        parent::__construct($message);
        $this->competition = $competition;
    }

    /**
     * @return \TopBetta\Models\CompetitionModel | null
     */
    public function getCompetition()
    {
        return $this->competition;
    }

}