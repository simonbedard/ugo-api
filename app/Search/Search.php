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

    protected mixed $time_start;
    protected mixed $time_end;

    function __construct() {


    }
    /**
     * Search Api's by single terms
     */
    public function byTerms(Request $request, string $term, string $page){
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
        $this->setCache($request, $searchQuery);
        
        return $response;

    }

    /**
     * Search file by unique id
     * @param $provider
     * @param $id 
     */
    public function byId(Request $request, $provider, $id){

        /**
         * Initialise Analitycs
         */
        $this->time_start = microtime(true);
        $this->time_end = microtime(true);
        /**
         * Single Search query options
         */
        $queryOptions = [
            "name" => "byId",
            "id" => $id,
            "provider" => $provider,
        ];

        /**
         * Create the query and run it
         */
        $searchQuery = (new SearchQuerySingle($queryOptions))->run();
        
        /**
         * Format the response
         */
        $response = $searchQuery->response()->header('X-Ugo-Cache', 'miss');
        
        /**
         * Set query in cache
         */
        $this->setCache($request, $searchQuery);
    
        /**
         * Log query analitycs
         */
        $execution_time = ($this->time_end - $this->time_start);
        dump($execution_time);
        $this->SetAnalytics($execution_time);

        return $response;
    }

    private function setCache(Request $request, SearchQuery|SearchQuerySingle $searchQuery){

        // REFACTOR IF CACHE IS ENABLE
        Cache::put($request->fullUrl(), $searchQuery); 
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

    
    /**
     * Set analitycs data after request is done
     */
    private function SetAnalytics($execution_time){
        // Store request Analytics data (Request time)
        log::info("Search by terms take: {$execution_time} sec");
    }

}