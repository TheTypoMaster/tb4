<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 28/04/2014
 * File creation time: 5:22 PM
 * Project: tb4
 */


class BaseEloquentRepository {

    public function find($id) {
        return $this->model->find($id);
    }

    public function findAll() {
        return $this->model->all();
    }

    public function firstOrNew(array $data) {
        return $this->model->firstOrNew($data)->toArray();
    }

    public function updateWithId($id, $data) {
        $model = $this->find($id);
        return $model->update($data);
    }

    public function create($data) {
        return $this->model->create($data);
    }

}