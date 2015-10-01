<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/03/2015
 * Time: 4:15 PM
 */

namespace TopBetta\Services\Country;

use TopBetta\Repositories\Contracts\CountryRepositoryInterface;

class CountryService {

    public function __construct(CountryRepositoryInterface $countryRepository) {
        $this->countryRepository = $countryRepository;
    }

    /**
     * get all countries to array
     * @return array
     */
    public function getCountryList() {
        $country_collection = $this->countryRepository->getCountryList();
        $country_list = array();
        foreach($country_collection as $key => $country) {
            $country_list[$country->code] = $country->name;
        }

        return $country_list;
    }
}