<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 3:56 PM
 */
namespace TopBetta\Repositories\Contracts;

interface ProcessParamsRepositoryInterface
{
    public function getProcessParamsByName($processName);
}