<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 4:01 PM
 */

namespace TopBetta\Processes;


use TopBetta\Repositories\Contracts\ProcessParamsRepositoryInterface;

abstract class AbstractProcess {

    protected $serviceName;
    /**
     * @var
     */

    protected $processParamsRepository;

    public function __construct(ProcessParamsRepositoryInterface $processParamsRepository)
    {
        $this->processParamsRepository = $processParamsRepository;
    }

    public abstract function run();

    public function getParams()
    {
        return $this->processParamsRepository->getProcessParamsByName($this->serviceName)->process_params;
    }

    public function updateParams($params)
    {
        $processParam = $this->processParamsRepository->getProcessParamsByName($this->serviceName);
        $processParam->setProcessParamsAttribute($params);

        return $processParam->save();
    }

    public function isRunning()
    {
        return $this->processParamsRepository->getProcessParamsByName($this->serviceName)->is_running_flag;
    }

    public function start()
    {
        $processParam = $this->processParamsRepository->getProcessParamsByName($this->serviceName);
        $processParam->is_running_flag = true;

        return $processParam->save();
    }

    public function end()
    {
        $processParam = $this->processParamsRepository->getProcessParamsByName($this->serviceName);
        $processParam->is_running_flag = false;

        return $processParam->save();
    }
}