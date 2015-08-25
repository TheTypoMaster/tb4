<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 1:10 PM
 */

namespace TopBetta\Presenters;


class AbstractPresenter {

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

}