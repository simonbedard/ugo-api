<?php
namespace App\Search;

use App\Ugo\Terms\Term;
use App\Search\SearchQuery;
use App\Search\SearchQuerySingle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Search
{
    function __construct() {
    }
    /**
     * Search Api's by single terms
     */
    public function byTerms(Request $request, string $term, string $page){
        /*
        $time_start = microtime(true);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);*/

        // Create the search query options
        $queryOptions = [
            "name" => "byTerms",
            "term" => (new Term($term)),
            "page" => $page,
            "filters" => $request->all(),
        ];


        // Create and run the query
        $searchQuery = (new SearchQuery($queryOptions))->run();
        
        // Get the response onject and set the header;
        $response = $searchQuery->response()->header('X-Ugo-Cache', 'miss');
        // Cache the response
        // $this->setCache($request, $searchQuery);
        

        // $this->SetAnalytics($execution_time);
        return $response;

    }

    public function test(Request $request, string $term, string $page){
        /*
        $time_start = microtime(true);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);*/

        // Create the search query options
        $queryOptions = [
            "name" => "byTerms",
            "term" => (new Term('baseball')),
            "page" => $page,
            "filters" => $request->all(),
        ];

       


        // Create and run the query
        $searchQuery = (new SearchQuery($queryOptions))->run();
        
        // Get the response onject and set the header;
        $response = $searchQuery->response()->header('X-Ugo-Cache', 'miss');
        // Cache the response
        // $this->setCache($request, $searchQuery);
        

        // $this->SetAnalytics($execution_time);
        return $response;

    }

    public function byId(Request $request, $provider, $id){

        $queryOptions = [
            "name" => "byId",
            "id" => $id,
            "provider" => $provider,
        ];
        // Create and run the query
        $searchQuery = (new SearchQuerySingle($queryOptions))->run();
        $response = $searchQuery->response()->header('X-Ugo-Cache', 'miss');
        
        $this->setCache($request, $searchQuery);
    
        return $response;
    }

    private function setCache(Request $request, SearchQuery|SearchQuerySingle $searchQuery){

        // REFACTOR IF CACHE IS ENABLE
        Cache::put($request->fullUrl(), $searchQuery); 
    }

    

    /**
     * Set analitycs data after request is done
     */
    private function SetAnalytics($execution_time){
        // Store request Analytics data (Request time)
        log::info("Search by terms take: {$execution_time} sec");
    }

}