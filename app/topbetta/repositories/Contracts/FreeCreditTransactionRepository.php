<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:01 AM
 */

namespace TopBetta\Repositories\Contracts;


interface FreeCreditTransactionRepository {

    public function getFreeCreditBalanceForUser($userId);


}