<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 4:20 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentPlacesPaidRepositoryInterface
{
    public function getByPlacesPaid($placesPaid);

    public function getByEntrants($entrants);
}