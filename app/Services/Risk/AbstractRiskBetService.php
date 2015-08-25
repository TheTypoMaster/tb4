<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 10:16 AM
 */

namespace TopBetta\Services\Risk;


abstract class AbstractRiskBetService {

    abstract function sendBet($bet);
}