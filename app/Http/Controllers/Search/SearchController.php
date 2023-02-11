<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Search\SearchFacade as _Search;


class SearchController extends Controller
{
    /**
     * Search api by terms
     */
    public function SearchByTerms(Request $request, $terms, $page){
        $response = _Search::byTerms($request, $terms, $page);
        return $response; 
    }

    /**
     * Search single image by provider and Id
     */
     public function SearchByProviderAndId(Request $request, string $provider, string $id){
        $response = _Search::byId($request, $provider, $id);
        return $response; 
     }

     public function test(Request $request){
        $response = _Search::test($request, $terms, $page);
        return $response; 
     }
}
