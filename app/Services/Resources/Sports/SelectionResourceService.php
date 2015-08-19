<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 11:50 AM
 */

namespace TopBetta\Services\Resources\Sports;


use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class SelectionResourceService {

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository)
    {
        $this->selectionRepository = $selectionRepository;
    }

    public function getDefaultRelations()
    {
        return array(
            'price',
            'result',
            'team',
            'player'
        );
    }

    public function getSelectionsForEvent($event)
    {
        $selections = $this->selectionRepository->getSelectionsForEvent($event);

        return new EloquentResourceCollection($selections, 'TopBetta\Resources\Sports\SelectionResource');
    }

    public function getSelectionsForMarkets($markets)
    {
        $selections = $this->selectionRepository->getSelectionsForMarkets($markets);

        return new EloquentResourceCollection($selections, 'TopBetta\Resources\Sports\SelectionResource');
    }
}