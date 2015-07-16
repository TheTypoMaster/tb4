<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/05/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Services\Exceptions;


class InvalidFormatException extends \Exception {

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data, $message = "")
    {
        $this->data = $data;
        parent::__construct($message);
    }

    public function getData()
    {
        return $this->data;
    }
}