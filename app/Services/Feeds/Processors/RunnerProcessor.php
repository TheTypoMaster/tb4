<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 3:18 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\OwnerRepositoryInterface;
use TopBetta\Repositories\Contracts\RunnerRepositoryInterface;
use TopBetta\Repositories\Contracts\TrainerRepositoryInterface;

class RunnerProcessor extends AbstractFeedProcessor {

    /**
     * @var RunnerRepositoryInterface
     */
    private $runnerRepository;

    /**
     * @var string
     */
    private $logprefix;
    /**
     * @var TrainerRepositoryInterface
     */
    private $trainerRepository;
    /**
     * @var OwnerRepositoryInterface
     */
    private $ownerRepository;

    public function __construct(RunnerRepositoryInterface $runnerRepository, TrainerRepositoryInterface $trainerRepository, OwnerRepositoryInterface $ownerRepository)
    {
        $this->runnerRepository = $runnerRepository;
        $this->logprefix = "RunnerProcessor: ";
        $this->trainerRepository = $trainerRepository;
        $this->ownerRepository = $ownerRepository;
    }

    public function process($data)
    {
        if( ! $runner = array_get($data, 'external_runner_id') ) {
            Log::error($this->logprefix . " No runner id specified");
            return 0;
        }

        Log::info($this->logprefix . "Processing Runner external id " . $runner);

        //runner data
        $runnerData = array(
            "external_runner_id" => $runner,
            "name"               => array_get($data, 'runner_name', ''),
            "colour"             => array_get($data, 'runner_colour', ''),
            "sex"                => array_get($data, 'runner_sex', ''),
            "age"                => array_get($data, 'runner_age', 0),
            "foal_date"          => array_get($data, 'runner_foal_date'),
            "sire"               => array_get($data, 'runner_sire'),
            "dam"                => array_get($data, 'runner_dam')
        );

        //get the trainer and the owner
        if( $trainer = array_get($data, 'external_trainer_id') ) {
            $trainer = $this->trainerRepository->getByExternalId($trainer);
        }

        if( $owner = array_get($data, 'external_owner_id') ) {
            $owner = $this->ownerRepository->getByExternalId($owner);
        }

        $runnerData['owner_id'] = $owner ? $owner->id : 0;
        $runnerData['trainer_id'] = $trainer ? $trainer->id : 0;

        $runner = $this->runnerRepository->updateOrCreate($runnerData, 'external_runner_id');

        return $runner['id'];
    }
}