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
        $marketOrderingModel = $this->marketOrderingRepository->getMarketOrdering($competitionId);

        if( ! $marketOrderingModel ) {
            return array();
        }

        $marketTypes = json_decode($marketOrderingModel->market_types);

        if(empty($marketTypes)) {
            return array();
        }

        return $this->marketTypeRepository->getMarketTypesIn($marketTypes);
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
}