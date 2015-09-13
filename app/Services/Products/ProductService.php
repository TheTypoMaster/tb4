<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/08/2015
 * Time: 11:55 AM
 */

namespace TopBetta\Services\Products;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class ProductService {

    private static $avaiableProductBetTypes = array(
        BetTypeRepositoryInterface::TYPE_WIN,
        BetTypeRepositoryInterface::TYPE_PLACE,
        BetTypeRepositoryInterface::TYPE_QUINELLA,
        BetTypeRepositoryInterface::TYPE_EXACTA,
        BetTypeRepositoryInterface::TYPE_TRIFECTA,
        BetTypeRepositoryInterface::TYPE_FIRSTFOUR,
    );

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

    public function getAuthUserProductsForCompetitionId($id)
    {
        $competition = $this->competitionRepository->find($id);

        return $this->getAuthUserProductsForCompetition($competition);
    }

    public function getAuthUserProductsForCompetition($competition)
    {
        //default products if there is no authorised user
        if (!$user = \Auth::user()) {
            return $competition->products;
        }

        $userProducts = $this->betProductRepository->getProductsForUser($user->id, $competition->venue_id);

        $defaultProducts = $competition->products;

        $products = new Collection;

        foreach (self::$avaiableProductBetTypes as $betType) {
            //filter by bet type
            $betTypeProducts = $userProducts->where('bet_type', $betType);

            //no user product exists so get default
            if (! $betTypeProducts->count() ) {
                $betTypeProducts = $defaultProducts->where('bet_type', $betType);
            } else {
                //user product exists so filter by venue
                $venueProducts = $betTypeProducts->where('venue_id', $competition->venue_id);
                if ($venueProducts->count()) { $betTypeProducts = $venueProducts; }
            }

            //push products onto collection
            foreach ($betTypeProducts as $product) {
                $products->push($product);
            }
        }

        return $products;
    }
}