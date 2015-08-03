<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 9:30 AM
 */

namespace TopBetta\Resources;


class ACLResource extends AbstractEloquentResource {

    protected $attributes = array(
        'acl' => 'acl'
    );

    public function getAcl()
    {
        return unserialize($this->model->acl_filter);
    }

    public function getMetaData()
    {
        return array(
            "acl_description" => $this->model->acl_description,
            "affiliate_name" => $this->model->affiliate_name,
            "affiliate_description" => $this->model->affiliate_description
        );
    }
}