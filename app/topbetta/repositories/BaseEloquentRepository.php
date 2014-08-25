<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 3:00 PM
 */

namespace TopBetta\Repositories;


class BaseEloquentRepository {

	protected $model;
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
		return $this->model->create($data);
	}

	public function createNew($data) {
		return new $this->model($data);
	}
} 