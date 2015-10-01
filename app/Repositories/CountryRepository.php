<?php

namespace TopBetta\Repositories;

use TopBetta\Models\CountryModel;
use TopBetta\Models\Tournament;
use TopBetta\Repositories\Contracts\CountryRepositoryInterface;

/**
 * Tournament Repo for admin interface
 *
 * @author mic
 */
class CountryRepository implements CountryRepositoryInterface
{

 public function __construct(CountryModel $countryModel) {
    $this->countryModel = $countryModel;
 }

    /**
     * get all countries
     * @return mixed
     */
    public function getCountryList() {
        return $this->countryModel->all();
    }

}
