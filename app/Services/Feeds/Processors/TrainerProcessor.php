<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 3:01 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\TrainerRepositoryInterface;

class TrainerProcessor extends AbstractFeedProcessor {

    /**
     * @var TrainerRepositoryInterface
     */
    private $trainerRepository;

    public function __construct(TrainerRepositoryInterface $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
        $this->logprefix = 'RunnerDataService - TrainerProcessor: ';
    }

    public function process($data)
    {
        if( ! $trainer = array_get($data, 'external_trainer_id') ) {
            Log::error($this->logprefix . "No Trainer ID specified", $data);
            return 0;
        }

        Log::info($this->logprefix . "Processing Trainer external id " . $trainer);

        $trainerData = array(
            "external_trainer_id" => $trainer,
            "name"                => array_get($data, 'trainer_name', ''),
            "location"            => array_get($data, 'trainer_location'),
            "state"               => array_get($data, 'trainer_state'),
            "postcode"            => array_get($data, 'trainer_postcode'),
            "initials"            => array_get($data, 'trainer_initials'),
        );

        $trainer = $this->trainerRepository->updateOrCreate($trainerData, "external_trainer_id");

        return $trainer['id'];
    }
}