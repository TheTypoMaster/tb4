<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 3:45 PM
 */

namespace TopBetta\Services\Feeds\Racing;


use TopBetta\Services\Feeds\Processors\OwnerProcessor;
use TopBetta\Services\Feeds\Processors\RunnerProcessor;
use TopBetta\Services\Feeds\Processors\TrainerProcessor;

class RunnerDataService {

    /**
     * @var OwnerProcessor
     */
    private $ownerProcessor;
    /**
     * @var TrainerProcessor
     */
    private $trainerProcessor;
    /**
     * @var RunnerProcessor
     */
    private $runnerProcessor;

    public function __construct(OwnerProcessor $ownerProcessor, TrainerProcessor $trainerProcessor, RunnerProcessor $runnerProcessor)
    {
        $this->ownerProcessor = $ownerProcessor;
        $this->trainerProcessor = $trainerProcessor;
        $this->runnerProcessor = $runnerProcessor;
    }

    public function processOwner($data)
    {
        return $this->ownerProcessor->processArray($data);
    }

    public function processTrainer($data)
    {
        return $this->trainerProcessor->processArray($data);
    }

    public function processRunner($data)
    {
        return $this->runnerProcessor->processArray($data);
    }
}