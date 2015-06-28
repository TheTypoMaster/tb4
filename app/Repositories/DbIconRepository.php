<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 9:24 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\IconModel;
use TopBetta\Repositories\Contracts\IconRepositoryInterface;

class DbIconRepository extends BaseEloquentRepository implements IconRepositoryInterface
{

    public function __construct(IconModel $iconModel)
    {
        $this->model = $iconModel;
    }

    public function getIconsByType($iconTypeId)
    {
        return $this->model->where('icon_type_id', '=', $iconTypeId)->get();
    }

    public function search($term)
    {
        return $this
            ->model
            ->where('name', 'LIKE', "%$term%")
            ->orWhereHas('iconType', function($q) use ($term) {
                $q->where('name', 'LIKE', "%$term%");
            })
            ->get();
    }
}