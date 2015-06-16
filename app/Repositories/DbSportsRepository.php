<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 19/11/14
 * File creation time: 16:46
 * Project: tb4
 */

use TopBetta\Models\SportModel;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;

class DbSportsRepository extends BaseEloquentRepository implements SportRepositoryInterface{

    protected $sports;

    protected $order = array('name', 'ASC');

    function __construct(SportModel $sports) {
        $this->model = $sports;
    }

    /**
     * @param $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->model
            ->orderBy('name', 'ASC')
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('description', 'LIKE', "%$search%")
            ->orWhere('short_name', 'LIKE', "%$search%")
            ->paginate();
    }

    /**
     * @return mixed
     */
    public function allSports()
    {
        return $this->model
            ->orderBy('name', 'ASC')
            ->paginate();
    }

    public function selectList(){
        return $this->model->lists('name', 'id')->all();
    }

    public function sportsFeed(){
        $sports =  $this->model->where('display_flag', '1')
            ->whereNotIn('id', array(1,2,3))
            ->select(array('id as sport_id','name as sport_name'))
            ->get();
        if(!$sports) return null;

        return $sports->toArray();
    }

} 