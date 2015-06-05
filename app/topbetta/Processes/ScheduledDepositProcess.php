<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 1:49 PM
 */

namespace TopBetta\Processes;

use Log;
use TopBetta\Processes\Exceptions\ProcessAlreadyRunningException;
use TopBetta\Repositories\Contracts\ProcessParamsRepositoryInterface;
use TopBetta\Services\Accounting\ScheduledDepositService;

class ScheduledDepositProcess extends AbstractProcess{

    protected $serviceName = "scheduled_deposit_process";

    /**
     * @var ScheduledDepositService
     */
    private $depositService;

    public function __construct(ScheduledDepositService $depositService, ProcessParamsRepositoryInterface $paramsRepositoryInterface)
    {
        parent::__construct($paramsRepositoryInterface);
        $this->depositService = $depositService;
    }

    public function run()
    {
        //this should be abstracted to AbstractProcess
        if( $this->isRunning() ) {
            throw new ProcessAlreadyRunningException("The process is already running");
        }
        $this->start();

        try {
            $this->depositService->processScheduledPayments();
        } catch( \Exception $e ) {
            Log::error("SCHEDULED PAYMENTS ERROR: " . $e->getMessage());
        }

        $this->end();
    }
}