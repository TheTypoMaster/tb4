<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/08/2015
 * Time: 4:01 PM
 */

namespace TopBetta\Services\Affiliates;

use TopBetta\Repositories\Contracts\AffiliateEndpointRepositoryInterface;
use TopBetta\Services\Affiliates\Exceptions\AffiliateMessageException;
use TopBetta\Services\Affiliates\Exceptions\AffiliateNotFoundException;
use TopBetta\Services\Affiliates\Messaging\AffiliateMessage;
use TopBetta\Services\Affiliates\Messaging\GuzzleAffiliateMessenger;

class AffiliateMessageService {

    /**
     * @var AffiliateEndpointRepositoryInterface
     */
    private $affiliateEndpointRepository;
    /**
     * @var GuzzleAffiliateMessenger
     */
    private $affiliateMessenger;

    public function __construct(AffiliateEndpointRepositoryInterface $affiliateEndpointRepository, GuzzleAffiliateMessenger $affiliateMessenger)
    {
        $this->affiliateEndpointRepository = $affiliateEndpointRepository;
        $this->affiliateMessenger = $affiliateMessenger;
    }

    public function sendMessage($affiliate, $type, $data)
    {
        $endpoint = $this->affiliateEndpointRepository->getByAffiliateAndType($affiliate->affiliate_id, $type);

        if (! $endpoint) {
            throw new AffiliateMessageException(null, null, "Validation could not be sent");
        }

        $message = new AffiliateMessage($affiliate, $data);

        return $this->affiliateMessenger->send($endpoint, $message);
    }

}