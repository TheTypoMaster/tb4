<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 12:06
 * Project: tb4
 */

use TopBetta\Models\BetOriginModel;
use TopBetta\Repositories\Contracts\BetOriginRepositoryInterface;

class DbBetOriginRepository extends BaseEloquentRepository implements BetOriginRepositoryInterface{

    protected $betorigins;

    public function __construct(BetOriginModel $betorigins)
    {
        $this->model = $betorigins;
    }

    public function getOriginByKeyword($keyword){
        $origin = $this->model->where('keyword', $keyword)->first();

        if ($origin){
            return $origin->toArray();
        }
        return false;
    }
}