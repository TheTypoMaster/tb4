<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 19/11/14
 * File creation time: 16:46
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\SportModel;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Traits\SportsResourceRepositoryTrait;

class DbSportsRepository extends BaseEloquentRepository implements SportRepositoryInterface{
    use SportsResourceRepositoryTrait;

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

    /**
     * get all sports without paginate
     */
    public function getAllSportsWithoutPaginate() {
        return $this->model->all();
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

    public function getVisibleSportsAndBaseCompetitions()
    {
        $model =  $this->getVisibleSportsEventBuilder()
             ->where('e.start_date', '>=', Carbon::now())
            ->groupBy('bc.id')
            ->get(array('tb_sports.*', 'bc.id as base_competition_id'));

        return $this->model->hydrate($model);
    }

    /**
     * check if the sport is race or not, if it is race, return the sport name
     * @param $sport_id
     * @return mixed
     */
    public function isRace($sport_id) {

        $sport_model = $this->model->find($sport_id);
        if(  $sport_model->isRacing()) {
            return $sport_model->name;
        } else {
            return false;
        }

    }

} 