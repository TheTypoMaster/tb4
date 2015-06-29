<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 15/01/15
 * File creation time: 19:47
 * Project: tb4
 */


interface BetSourceRepositoryInterface {

    public function getSourceByKeyword($keyword);
}