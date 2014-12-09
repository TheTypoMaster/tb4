<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 19/11/14
 * File creation time: 16:46
 * Project: tb4
 */

use TopBetta\TournamentSport;

class DbSportsRepository extends BaseEloquentRepository{

    protected $sports;

    function __construct(TournamentSport $sports) {
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
        return $this->model->lists('name', 'id');
    }

} 