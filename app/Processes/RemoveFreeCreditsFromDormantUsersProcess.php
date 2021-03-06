<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 4:26 PM
 */

namespace TopBetta\Processes;


use TopBetta\Processes\Exceptions\ProcessAlreadyRunningException;
use TopBetta\Repositories\Contracts\ProcessParamsRepositoryInterface;
use TopBetta\Services\UserAccount\UserFreeCreditService;
use Carbon\Carbon;

class RemoveFreeCreditsFromDormantUsersProcess extends AbstractProcess {

    protected $serviceName = "remove_free_credit_from_dormant_account";
    /**
     * @var UserFreeCreditService
     */
    private $userFreeCreditService;

    private $dormantDays;

    public function __construct(UserFreeCreditService $userFreeCreditService, ProcessParamsRepositoryInterface $processParamsRepository)
    {
        $this->userFreeCreditService = $userFreeCreditService;
        parent::__construct($processParamsRepository);
    }

    public function run()
    {
        //this should be abstracted to AbstractProcess
        if( $this->isRunning() ) {
            throw new ProcessAlreadyRunningException("The process is already running");
        }
        $this->start();

        $params = $this->getParams();

        if( ! $this->dormantDays ) {
            throw new \Exception("Dormant Days must be set");
        }

        //get start and dates for use activity
        $start = Carbon::createFromFormat("Y-m-d H:i:s", $params['last_run_date'])->subDays($params['last_run_days']);
        $end = Carbon::now()->subDays($this->dormantDays)->format("Y-m-d H:i:s");

        $this->userFreeCreditService->removeCreditsFromInactiveUsers($start, $end);

        //update params
        $params['last_run_date'] = Carbon::now()->format("Y-m-d H:i:s");
        $params['last_run_days'] = $this->dormantDays;
        $this->updateParams($params);

        //this should be abstracted to AbstractProcess
        $this->end();

    }

    public function setDormantDays($days)
    {
        $this->dormantDays = $days;
        return $this;
    }
}