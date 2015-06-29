<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 08:33
 * Project: tb4
 */

interface EventStatusRepositoryInterface {

    const STATUS_SELLING = 'selling';
    const STATUS_PAYING = 'paying';
    const STATUS_PAID = 'paid';
    const STATUS_INTERIM = 'interim';
    const STATUS_CLOSED = 'closed';

    public function getByName($name);
} 