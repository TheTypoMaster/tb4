<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/06/2015
 * Time: 2:55 PM
 */

namespace TopBetta\Services\Betting;

use TopBetta\Repositories\DbBetLimitRepository;

class BetLimitService {

    /**
     * @var DbBetLimitRepository
     */
    private $betLimitRepository;

    public function __construct(DbBetLimitRepository $betLimitRepository)
    {
        $this->betLimitRepository = $betLimitRepository;
    }

    public function checkBetLimit($user, $betAmount, $selections, $betType, $source)
    {
        if ( $source == 'racing' ) {
            return $this->checkRacingBetLimit($user, $betAmount, $selections, $betType, $source);
        }
    }

    public function checkRacingBetLimit($user, $betAmount, $selections, $betType)
    {
        $limit = $this->betLimitRepository->getLimitForUserAndBetType($user->id, $betType);


    }
}