<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 1:46 PM
 */

namespace TopBetta\Repositories;

use DB;
use TopBetta\Models\PlayerModel;
use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;

class DbPlayersRepository extends BaseEloquentRepository implements PlayersRepositoryInterface
{

    public function __construct(PlayerModel $playerModel)
    {
        $this->model = $playerModel;
    }

    public function updateWithId($id, $data)
    {
        $model = $this->find($id);

        if($teams = array_get($data, 'teams', false)) {

            $currentTeams = $model->teams()->select('tb_teams.id as teamId')->lists('teamId')->all();

            if(count(array_diff($currentTeams, $teams))) {
                $model->teams()->detach(array_diff($currentTeams, $teams));
            }

            if(count(array_diff($teams, $currentTeams))) {
                $model->teams()->attach(array_diff($teams, $currentTeams));
            }
        }

        return $model->update(array_except($data, 'teams'));
    }

    public function updateOrCreate($input, $key = 'id')
    {
        // Instantiate new OR existing object
        if (! empty($input[$key])){
            $resource = $this->model->firstOrNew(array($key => $input[$key]));
        }
        else{
            $resource = $this->model; // Use a clone to prevent overwriting the same object in case of recursion
        }

        // Fill object with user input using Mass Assignment
        $resource->fill(array_except($input, 'teams'));

        // Save data to db
        if (! $resource->save()) return false;

        if($teams = array_get($input, 'teams', false)) {
            $resource->teams()->attach($teams);
        }

        return $resource->toArray();
    }

    public function search($searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%$searchTerm%")
            ->orWhere('short_name', 'LIKE', "%$searchTerm%")
            ->orWhere('default_name', 'LIKE', "%$searchTerm%")
            ->get();
    }

    public function findByExternalId($externalId)
    {
        return $this->model
            ->where('external_player_id', $externalId)
            ->first();
    }

}