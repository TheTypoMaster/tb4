<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 11:09 AM
 */

namespace TopBetta\Repositories;

use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Models\TournamentPrizeFormat;

class DbTournamentPrizeFormatRepository extends BaseEloquentRepository implements TournamentPrizeFormatRepositoryInterface
{

    public function __construct(TournamentPrizeFormat $model)
    {
        $this->model = $model;
    }

    /**
     * get all prize formats
     * @return mixed
     */
    public function getPrizeFormatList() {
        return $this->model->paginate();
    }

    public function getPrizeFormatByID($id) {
        return $this->model->find($id);
    }

    public function update($id, $data) {
        $prize_model = $this->model->find($id);
        $prize_model->update($data);
    }
}