<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 15/01/15
 * File creation time: 19:48
 * Project: tb4
 */

use TopBetta\Models\BetSourceModel;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;

class DbBetSourceRepository extends BaseEloquentRepository implements BetSourceRepositoryInterface{

    protected $betsource;

    public function __construct(BetSourceModel $betsource)
    {
        $this->model = $betsource;
    }

    public function getSourceByKeyword($keyword){
        $source = $this->model->where('keyword', $keyword)->first();

        if ($source){
            return $source->toArray();
        }
        return null;
    }
}