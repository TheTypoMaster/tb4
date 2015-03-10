<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/03/2015
 * Time: 11:01 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\PromotionModel;
use TopBetta\Repositories\Contracts\PromotionRepositoryInterface;
use TopBetta\Services\Validation\PromotionValidator;

class DbPromotionRepository extends BaseEloquentRepository implements PromotionRepositoryInterface
{

    public function __construct(PromotionModel $promotion, PromotionValidator $validator)
    {
        $this->model = $promotion;
        $this->validator = $validator;
    }

    public function getAll()
    {
        return $this->model->with("user")->paginate(15);
    }

    public function find($id)
    {
        return $this->model->where("pro_id", $id)->first();
    }

    public function getByPromoCode($code)
    {
        return $this->model->where("pro_code", $code)->first();
    }

    public function search($search)
    {
        return $this->model
            ->where('pro_description', 'LIKE', "%$search%")
            ->orWhere('pro_code', 'LIKE', "%$search%")
            ->orWhere('pro_entered_by', 'LIKE', "%$search%")
            ->paginate(15);
    }


}