<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:32 AM
 */
namespace TopBetta\Repositories\Contracts;

interface BetTypeRepositoryInterface
{
    const TYPE_WIN = 'win';
    const TYPE_PLACE = 'place';

    public function getBetTypeByName($name);
}