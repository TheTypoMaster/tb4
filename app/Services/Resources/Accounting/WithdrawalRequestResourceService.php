<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 1:05 PM
 */

namespace TopBetta\Services\Resources\Accounting;


use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;
use TopBetta\Resources\Accounting\WithdrawalRequestResource;

class WithdrawalRequestResourceService {

    /**
     * @var WithdrawalRequestRepositoryInterface
     */
    private $requestRepository;

    public function __construct(WithdrawalRequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function findRequest($request)
    {
        $withdrawal = $this->requestRepository->find($request);

        $withdrawal->load(array('type', 'paypal'));

        return new WithdrawalRequestResource($withdrawal);
    }
}