<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 6:15 PM
 */

namespace TopBetta\Resources\Sports;


use TopBetta\Resources\AbstractEloquentResource;

abstract class IconResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\IconModel';

    protected $icon;

    public function __construct($model = null)
    {
        if ($model) {
            parent::__construct($model);
            $this->loadIcon();
        }

    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    abstract public function loadIcon();
}