<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 3:10 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\CompetitionModel;
use TopBetta\Models\ProductProviderMatch;
use TopBetta\Repositories\Contracts\ProductProviderMatchRepositoryInterface;

class DbProductProviderMatchRepository extends BaseEloquentRepository implements ProductProviderMatchRepositoryInterface
{

    public function __construct(ProductProviderMatch $model)
    {
        $this->model = $model;
    }

    public function getProductAndBetTypeByCompetition(CompetitionModel $competition)
    {
        return $this->model
            ->from('tb_product_provider_match as ppm')
            ->join('tb_product_default as pd', 'pd.tb_product_id', '=', 'ppm.tb_product_id')
            ->where('pd.country', $competition->country)
            ->where('pd.region', $competition->meeting_grade)
            ->where('pd.type_code', $competition->type_code)
            ->get(array('ppm.provider_product_name as product_name', 'pd.bet_type as bet_type', 'ppm.id as id'));
    }
}