<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/04/2015
 * Time: 1:47 PM
 */
namespace TopBetta\Repositories\Contracts;

interface ConfigurationRepositoryInterface
{
    public function getConfigByName($name, $asArray=false);
}