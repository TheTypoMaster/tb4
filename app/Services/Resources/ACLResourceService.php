<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 9:32 AM
 */

namespace TopBetta\Services\Resources;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\ACLRepositoryInterface;
use TopBetta\Resources\ACLResource;

class ACLResourceService {

    /**
     * @var ACLRepositoryInterface
     */
    private $ACLRepository;

    public function __construct(ACLRepositoryInterface $ACLRepository)
    {
        $this->ACLRepository = $ACLRepository;
    }

    public function getACLByAffiliateAndACLCode($affiliate, $aclCode)
    {
        $acl = $this->ACLRepository->getACLByAffiliateAndACLCode($affiliate, $aclCode);

        if( ! $acl ) {
            throw new ModelNotFoundException;
        }

        return new ACLResource($acl);
    }
}