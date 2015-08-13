<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:17 AM
 */

namespace TopBetta\Services\Resources;


use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class SelectionResourceService {

    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepositoryInterface;

    public function __construct(SelectionRepositoryInterface $selectionRepositoryInterface)
    {
        $this->selectionRepositoryInterface = $selectionRepositoryInterface;
    }

    public static function getDefaultRelations()
    {
        return array(
            'result',
            'price',
            'runner',
            'runner.owner',
            'runner.trainer',
            'form',
            'lastStarts'
        );
    }
}