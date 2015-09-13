<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/09/2015
 * Time: 9:37 AM
 */

namespace TopBetta\Services\Resources\Cache\Sports;


use TopBetta\Services\Resources\Cache\CachedResourceService;
use TopBetta\Services\Resources\Sports\SelectionResourceService;

class CachedSelectionResourceService extends CachedResourceService {

    public function __construct(SelectionResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    public function filterSelections($selections)
    {
        return $selections->filter(function ($v) {
            return $v->getPrice() > 1 && $v->selection_status_id == 1;
        });
    }
}