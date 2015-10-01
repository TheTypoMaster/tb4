<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 12:57 PM
 */

namespace TopBetta\Services\Resources\Cache;


use TopBetta\Repositories\Cache\RacingSelectionRepository;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\SelectionResourceService;

class CachedSelectionResourceService extends CachedResourceService {

    /**
     * @var RacingSelectionRepository
     */
    private $selectionRepository;

    public function __construct(SelectionResourceService $resourceService, RacingSelectionRepository $selectionRepository)
    {
        $this->resourceService = $resourceService;
        $this->selectionRepository = $selectionRepository;
    }

    public function getSelectionsForRace($race)
    {
        $selections = $this->selectionRepository->getSelectionsForRace($race);

        if (!$selections || !$selections->count()) {
            return $this->resourceService->getSelectionsForRace($race);
        }

        return $selections;
    }

}