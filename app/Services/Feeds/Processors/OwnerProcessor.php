<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 3:13 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\OwnerRepositoryInterface;

class OwnerProcessor extends AbstractFeedProcessor {

    /**
     * @var OwnerRepositoryInterface
     */
    private $ownerRepository;

    /**
     * @var string
     */
    private $logprefix;

    public function __construct(OwnerRepositoryInterface $ownerRepository)
    {
        $this->ownerRepository = $ownerRepository;
        $this->logprefix = "OwnerProcessor: ";
    }

    public function process($data)
    {
        if( ! $owner = array_get($data, 'external_owner_id', null) ) {
            Log::error($this->logprefix . "No owner id specified");
            return 0;
        }

        Log::info($this->logprefix . "Processing Owner external id " . $owner);

        $ownerData = array(
            "external_owner_id" => $owner,
            "name" => array_get($data, 'owner_name', ''),
        );

        $owner = $this->ownerRepository->updateOrCreate($ownerData, 'external_owner_id');

        return $owner['id'];
    }
}