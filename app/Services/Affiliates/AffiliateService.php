<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 10:22 AM
 */

namespace TopBetta\Services\Affiliates;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;
use TopBetta\Services\Affiliates\Exceptions\AffiliateNotFoundException;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class AffiliateService {

    /**
     * @var AffiliateRepositoryInterface
     */
    private $affiliateRepository;

    public function __construct(AffiliateRepositoryInterface $affiliateRepository)
    {
        $this->affiliateRepository = $affiliateRepository;
    }

    public function findAffiliateFromInput($input, $codeField = 'source_name')
    {
        if (! $affiliateCode = array_get($input, $codeField)) {
            throw new ValidationException("Validation Error", "No affiliate code supplied");
        }

        return $this->findAffiliate($affiliateCode);
    }

    public function findAffiliate($affiliateCode)
    {
        try {
            $affiliate = $this->affiliateRepository->getByCodeOrFail($affiliateCode);
        } catch (ModelNotFoundException $e) {
            throw new AffiliateNotFoundException;
        }

        return $affiliate;
    }
}