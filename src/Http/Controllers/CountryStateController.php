<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\CountryRepository;
use BagistoPackages\Shop\Repositories\CountryStateRepository;

class CountryStateController extends Controller
{
    /**
     * CountryRepository object
     *
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * CountryStateRepository object
     *
     * @var CountryStateRepository
     */
    protected $countryStateRepository;

    /**
     * Create a new controller instance.
     *
     * @param CountryRepository $countryRepository
     * @param CountryStateRepository $countryStateRepository
     * @return void
     */
    public function __construct(CountryRepository $countryRepository, CountryStateRepository $countryStateRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->countryStateRepository = $countryStateRepository;
    }

    /**
     * Function to retrieve states with respect to countries with codes and names for both of the countries and states.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function getCountries()
    {
        $countries = $this->countryRepository->all();

        $states = $this->countryStateRepository->all();

        $nestedArray = [];

        foreach ($countries as $keyCountry => $country) {
            foreach ($states as $keyState => $state) {
                if ($country->code == $state->country_code) {
                    $nestedArray[$country->name][$state->code] = $state->default_name;
                }
            }
        }

        return view('shop::test')->with('statesCountries', $nestedArray);
    }

    /**
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function getStates($country)
    {
        $countries = $this->countryRepository->all();

        $states = $this->countryStateRepository->all();

        $nestedArray = [];

        foreach ($countries as $keyCountry => $country) {
            foreach ($states as $keyState => $state) {
                if ($country->code == $state->country_code) {
                    $nestedArray[$country->name][$state->code] = $state->default_name;
                }
            }
        }

        return view('shop::test')->with('statesCountries', $nestedArray);
    }
}
