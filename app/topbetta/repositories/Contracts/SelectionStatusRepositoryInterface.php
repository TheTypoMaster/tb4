<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/02/2015
 * Time: 4:16 PM
 */
namespace TopBetta\Repositories\Contracts;

interface SelectionStatusRepositoryInterface
{
    public function getSelectionStatusIdByName($statusName);
}