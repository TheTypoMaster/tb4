<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:44 AM
 */

namespace TopBetta\Services\Affiliates;


use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;
use TopBetta\Services\Authentication\MessageAuthenticationService;

class AffiliateMessageAuthenticationService {

    /**
     * @var MessageAuthenticationService
     */
    private $authenticationService;
    /**
     * @var AffiliateService
     */
    private $affiliateService;

    public function __construct(AffiliateService $affiliateService, MessageAuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        $this->affiliateService = $affiliateService;
    }

    public function authenticateMessage($input)
    {
        try {
            $affiliate = $this->affiliateService->findAffiliateFromInput($input);
        } catch (\Exception $e) {
            return false;
        }

        return $this->authenticationService->authenticateMessage($input, $affiliate->affiliate_api_key);
    }

    public function createHashedMessage($input, $affiliateCode)
    {
        $affiliate = $this->affiliateService->findAffiliate($affiliateCode);

        return $this->authenticationService->createHashedMessage($input, $affiliate->affiliate_api_key);
    }
}