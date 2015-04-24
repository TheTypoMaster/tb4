<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 18:16
 * Project: tb4
 */

interface SelectionRepositoryInterface {

    public function getByExternalIds($externalSelectionId, $externalMarketId, $externalEventId);
}