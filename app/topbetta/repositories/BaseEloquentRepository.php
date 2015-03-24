<?php namespace TopBetta\Repositories;
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 3:00 PM
 */

use TopBetta\Services\Validation\Exceptions\ValidationException;

class BaseEloquentRepository {

	protected $validator = null;

    protected $model;

    protected $order;

	/**
	 * Find the model given an ID
	 * @param $id
	 * @return mixed
	 */
	public function find($id) {
		return $this->model->find($id);
	}

	/**
	 * Find all models
	 * @return mixed
	 */
	public function findAll() {
		return $this->model->all();
	}

    /**
     * @param array $relations Relation to eager load
     * @param int $paginate
     * @return mixed
     */
    public function findAllPaginated($relations = array(), $paginate = 15)
    {
        $model = $this->model;
        if($this->order != null) {
            $model = $model->orderBy($this->order[0], $this->order[1]);
        }

        //eager load relations
        foreach($relations as $relation) {
            $model->with($relation);
        }

        return $model->paginate($paginate);
    }

	/**
	 * Update record with the given id and data
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function updateWithId($id, $data) {
		$model = $this->model->findOrFail($id);
		return $model->update($data);
	}

	/**
	 * Create
	 * @param $data
	 * @return mixed
	 */
	public function create($data) {
		$this->validate($data);
		$model =  $this->model->create($data);
		return $model->toArray();
	}

	public function createNew($data) {
		return new $this->model($data);
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
        $resource->fill($input);

        // Save data to db
        if (! $resource->save()) return false;

        return $resource->toArray();
    }


	public function validate($input) {
		return $this->validator ? $this->validator->validateForCreation($input) : true;
	}

	public function validateUpdate($input) {
		return $this->validator ? $this->validator->validateForUpdate($input) : true;
	}


} 