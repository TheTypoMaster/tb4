<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 11:10 AM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentPrizeFormatRepositoryInterface
{
    const PRIZE_FORMAT_ALL = 'all';

    const PRIZE_FORMAT_TOP3 = 'top3';

    const PRIZE_FORMAT_MULTIPLE = 'multiple';
}