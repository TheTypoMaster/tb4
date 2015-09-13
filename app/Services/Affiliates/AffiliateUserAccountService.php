<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 10:21 AM
 */

namespace TopBetta\Services\Affiliates;


use TopBetta\Repositories\Contracts\AffiliateRepositoryInterface;
use TopBetta\Repositories\Contracts\AffiliateTypeRepositoryInterface;
use TopBetta\Services\Affiliates\Exceptions\InvalidAccountTypeException;
use TopBetta\Services\UserAccount\UserAccountService;

class AffiliateUserAccountService {

    const TYPE_TOURNAMENT_ACCOUNT = 'tournamentaccount';

    /**
     * @var AffiliateService
     */
    private $affiliateService;
    /**
     * @var UserAccountService
     */
    private $accountService;

    public function __construct(AffiliateService $affiliateService, UserAccountService $accountService)
    {
        $this->affiliateService = $affiliateService;
        $this->accountService = $accountService;
    }

    public function createTournamentAccount($input)
    {
        $affiliate = $this->affiliateService->findAffiliateFromInput($input);

        if (! $this->canCreateAccountType($affiliate, self::TYPE_TOURNAMENT_ACCOUNT)) {
            throw new InvalidAccountTypeException("Cannot create tournament accounts");
        };

        $user = $this->accountService->createTournamentAccount($input, $affiliate);

        return array(
            "topbetta_id" => $user['id'],
            "external_unique_identifier" => array_get($input, 'external_unique_identifier'),
            "tournament_username" => $user['username'],
        );
    }

    public function canCreateAccountType($affiliate, $accountType)
    {
        switch ($accountType) {
            case self::TYPE_TOURNAMENT_ACCOUNT:
                return $affiliate->type->affiliate_type_name == AffiliateTypeRepositoryInterface::AFFILIATE_TYPE_TOURNAMENT_ONLY;
        }

        return false;
    }
}