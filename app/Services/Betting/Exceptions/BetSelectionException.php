<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:08 AM
 */

namespace TopBetta\Services\Betting\Exceptions;


class BetSelectionException extends \Exception{

    /**
     * @var string
     */
    private $selection;

    public function __construct($selection, $message)
    {
        parent::__construct($message);
        $this->selection = $selection;
    }

    public function getSelection()
    {
        return $this->selection;
    }
}