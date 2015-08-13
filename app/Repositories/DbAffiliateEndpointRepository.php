<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/08/2015
 * Time: 4:21 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\AffiliateEndpointModel;
use TopBetta\Repositories\Contracts\AffiliateEndpointRepositoryInterface;

class DbAffiliateEndpointRepository extends BaseEloquentRepository implements AffiliateEndpointRepositoryInterface
{

    public function __construct(AffiliateEndpointModel $model)
    {
        $this->model = $model;
    }

    public function getByAffiliateAndType($affiliate, $type)
    {
        return $this->model
            ->join('tb_affiliate_endpoint_types as aet', 'aet.id', '=', 'tb_affiliate_endpoints.affiliate_endpoint_type')
            ->where('aet.affiliate_endpoint_type_name', $type)
            ->where('affiliate_id', $affiliate)
            ->first();
    }
}