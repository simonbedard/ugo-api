<?php

namespace App\Search;

use Illuminate\Support\Facades\Facade;

class SearchFacade extends Facade
{
    protected static function getFacadeAccessor(): String
    {
        return 'search';
    }
}
