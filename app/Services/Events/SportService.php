<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/06/2015
 * Time: 4:00 PM
 */

namespace TopBetta\Services\Events;


use TopBetta\Repositories\Contracts\SportRepositoryInterface;

class SportService {

    public static function getRacingCode($sport)
    {
        switch($sport->name)
        {
            case SportRepositoryInterface::SPORT_GALLOPING:
                return 'R';
            case SportRepositoryInterface::SPORT_HARNESS:
                return 'H';
            case SportRepositoryInterface::SPORT_GREYHOUNDS:
                return 'G';
        }

        throw new \Exception("Invalid sport");
    }
}