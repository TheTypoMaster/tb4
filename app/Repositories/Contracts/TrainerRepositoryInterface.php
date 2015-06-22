<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 2:49 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TrainerRepositoryInterface
{
    public function getByExternalId($externalId);
}