<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 4:40 PM
 */

namespace TopBetta\Services\Sports;

use Carbon\Carbon;
use TopBetta\Services\Resources\Cache\Sports\CachedSportResourceService;
use TopBetta\Services\Resources\Sports\SportResourceService;

class SportsService {

    /**
     * @var CachedSportResourceService
     */
    private $sportResourceService;

    public function __construct(CachedSportResourceService $sportResourceService)
    {
        $this->sportResourceService = $sportResourceService;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if( $date ) {
            $date = Carbon::createFromFormat("Y-m-d", $date);
        }

        $sports = $this->sportResourceService->getVisibleSportsWithCompetitions($date);

        return $sports;
    }
}