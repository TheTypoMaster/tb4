<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/03/2015
 * Time: 5:01 PM
 */

namespace TopBetta\Services\Icons;


use TopBetta\Repositories\Contracts\IconRepositoryInterface;
use TopBetta\Repositories\Contracts\IconTypeRepositoryInterface;

class IconService {

    /**
     * @var IconRepositoryInterface
     */
    private $iconRepository;
    /**
     * @var IconTypeRepositoryInterface
     */
    private $iconTypeRepository;

    public function __construct(IconRepositoryInterface $iconRepository, IconTypeRepositoryInterface $iconTypeRepository)
    {
        $this->iconRepository = $iconRepository;
        $this->iconTypeRepository = $iconTypeRepository;
    }

    public function getIcons($iconType = null)
    {
        if( isset($iconType) ) {
            return $this->iconRepository->findAll();
        }

        $iconType = $this->iconTypeRepository->getIconTypeByName($iconType);
        return $this->iconRepository->getIconsByType($iconType->id);
    }

}