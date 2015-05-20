<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 11:09 AM
 */

namespace TopBetta\Repositories;

use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Models\TournamentPrizeFormat;

class DbTournamentPrizeFormatRepository extends BaseEloquentRepository implements TournamentPrizeFormatRepositoryInterface
{

    public function __construct(TournamentPrizeFormat $model)
    {
        $this->model = $model;
    }
}