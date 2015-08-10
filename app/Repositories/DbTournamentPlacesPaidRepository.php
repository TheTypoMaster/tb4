<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:51 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentPlacesPaidModel;
use TopBetta\Repositories\Contracts\TournamentPlacesPaidRepositoryInterface;

class DbTournamentPlacesPaidRepository extends BaseEloquentRepository implements TournamentPlacesPaidRepositoryInterface
{
    
    public function __construct(TournamentPlacesPaidModel $model)
    {
        $this->model = $model;
    }
    
    public function getByPlacesPaid($placesPaid)
    {
        return $this->model
            ->where('places_paid', $placesPaid)
            ->first();
    }

    public function getByEntrants($entrants)
    {
        return $this->model
            ->where('entrants', '>=', $entrants)
            ->orderBy('entrants')
            ->first();
    }
}