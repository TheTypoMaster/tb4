<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 1:51 PM
 */
namespace TopBetta\Repositories\Contracts;

interface PlayersRepositoryInterface
{
    public function updateWithId($id, $data);
}