<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 9:25 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\ACLModel;
use TopBetta\Repositories\Contracts\ACLRepositoryInterface;

class DbACLRepository extends BaseEloquentRepository implements ACLRepositoryInterface
{

    public function __construct(ACLModel $model)
    {
        $this->model = $model;
    }

    public function getACLByAffiliateAndACLCode($affiliate, $aclCode)
    {
        return $this->model
            ->from('tb_website_acls as wa')
            ->join('tb_affiliates_acls as aa', 'aa.acl_id', '=', 'wa.acl_id')
            ->join('tb_affiliates as a', 'a.affiliate_id', '=', 'aa.affiliate_id')
            ->where('a.affiliate_code', $affiliate)
            ->where('wa.acl_code', $aclCode)
            ->first(array('wa.*', 'a.affiliate_name', 'a.affiliate_description'));
    }
}