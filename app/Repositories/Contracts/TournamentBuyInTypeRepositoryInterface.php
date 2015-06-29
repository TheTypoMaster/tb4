<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 12:42 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentBuyInTypeRepositoryInterface
{
    const TOURNAMENT_BUYIN_TYPE_BUYIN = 'buyin';
    const TOURNAMENT_BUYIN_TYPE_REBUY = 'rebuy';
    const TOURNAMENT_BUYIN_TYPE_TOPUP = 'topup';

    public function getIdByKeyword($keyword);
}