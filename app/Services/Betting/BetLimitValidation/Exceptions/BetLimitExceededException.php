<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/08/2015
 * Time: 9:37 AM
 */

namespace TopBetta\Services\Betting\BetLimitValidation\Exceptions;


abstract class BetLimitExceededException extends \Exception
{

    /**
     * @var int
     */
    protected $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;

        $message = $this->constructMessage();

        parent::__construct($message);
    }

    abstract public function constructMessage();

}