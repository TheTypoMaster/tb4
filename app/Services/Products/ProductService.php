<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/08/2015
 * Time: 11:55 AM
 */

namespace TopBetta\Services\Products;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class ProductService {

    /**
     * @var BetProductRepositoryInterface
     */
    private $betProductRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(BetProductRepositoryInterface $betProductRepository, CompetitionRepositoryInterface $competitionRepository, BetTypeRepositoryInterface $betTypeRepository, UserRepositoryInterface $userRepository)
    {
        $this->betProductRepository = $betProductRepository;
        $this->competitionRepository = $competitionRepository;
        $this->betTypeRepository = $betTypeRepository;
        $this->userRepository = $userRepository;
    }

    public function setMeetingProductsForBetType($meetingId, $totes, $betType)
    {
        $meeting = $this->competitionRepository->find($meetingId);

        if (! $meeting) {
            throw new ModelNotFoundException("Meeting " . $meetingId . " not found");
        }

        $totes = $this->betProductRepository->getProductsByCodes($totes);

        $betType = $this->betTypeRepository->getBetTypeByName($betType);

        return $this->competitionRepository->syncProductsForBetType($meeting, $totes->lists('id')->all(), $betType->id);
    }

    public function setProductsForUser($userId, $products, $betType,  $venue = 0)
    {
        $user = $this->userRepository->find($userId);

        if (! $user) {
            throw new ModelNotFoundException("User " . $userId . " not found");
        }

        $totes = $this->betProductRepository->getProductsByCodes($products);

        $betType = $this->betTypeRepository->getBetTypeByName($betType);

        return $this->userRepository->syncProductsForBetType($user, $totes->lists('id')->all(), $betType->id, $venue);
    }
}