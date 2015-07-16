<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/03/2015
 * Time: 3:01 PM
 */

namespace TopBetta\Services\Markets;


use TopBetta\Repositories\Contracts\MarketOrderingRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;

class MarketOrderingService {

    /**
     * @var MarketOrderingRepositoryInterface
     */
    private $marketOrderingRepository;
    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;

    public function __construct(MarketOrderingRepositoryInterface $marketOrderingRepository, MarketTypeRepositoryInterface $marketTypeRepository)
    {
        $this->marketOrderingRepository = $marketOrderingRepository;
        $this->marketTypeRepository = $marketTypeRepository;
    }

    public function getDefaultMarketTypes($competitionId = 0)
    {
        $types = $this->getMarketTypes($competitionId);

        if( empty($types) ) {
            return $this->getMarketTypes();
        }

        return $types;
    }

    public function createOrUpdateForCompetition($marketTypes, $competitionId = 0)
    {
        $marketOrdering = $this->marketOrderingRepository->getMarketOrdering($competitionId);

        if( ! $marketOrdering ) {
            return $this->marketOrderingRepository->create(array(
                "base_competition_id" => $competitionId,
                "market_types" => json_encode($marketTypes),
            ));
        }

        return $this->marketOrderingRepository->updateWithId($marketOrdering->id, array(
            "market_types" => json_encode($marketTypes)
        ));
    }

    public function getMarketTypesForUser($userId ,$competitionId)
    {
       $types = $this->getMarketTypes($competitionId, $userId);

        if( empty($types) ) {
            return $this->getDefaultMarketTypes($competitionId);
        }

        return $types;
    }

    public function getMarketTypeIds($competitionId = 0)
    {
        $types = $this->marketOrderingRepository->getMarketOrdering($competitionId);

        if( ! $types && $competitionId ) {
            return $this->getMarketTypeIds();
        } else if ( ! $types ) {
            return array();
        }

        return json_decode($types->market_types);
    }

    /**
     * Gets the market types to display for competition and user
     * @param int $competitionId
     * @param int $userId
     * @return array
     */
    private function getMarketTypes($competitionId = 0, $userId = 0)
    {
        $marketOrderingModel = $this->marketOrderingRepository->getMarketOrdering($competitionId, $userId);

        if( ! $marketOrderingModel ) {
            return array();
        }

        $marketTypes = json_decode($marketOrderingModel->market_types);

        if(empty($marketTypes)) {
            return array();
        }

        return $this->marketTypeRepository->getMarketTypesIn($marketTypes);
    }
}