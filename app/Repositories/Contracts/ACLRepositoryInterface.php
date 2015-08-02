<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 9:33 AM
 */
namespace TopBetta\Repositories\Contracts;

interface ACLRepositoryInterface
{
    public function getACLByAffiliateAndACLCode($affiliate, $aclCode);
}