<?php

namespace App\Http\Controllers;
use Rinvex\Country\CountryLoader;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    //Controller used to fetch country data
    public function getCountries(){
        $countries = CountryLoader::countries();
        return response()->json($countries);
    }
}
