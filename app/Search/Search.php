<?php

namespace App\Search;

use App\Ugo\Terms\Term;
use App\Search\SearchQuery;
use App\Search\SearchQuerySingle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\JsonResponse;
use App\Jobs\CompletedSearchQuery;


class Search
{

    protected mixed $time_start;
    protected mixed $time_end;

    function __construct()
    {

        /**
         * Initialise Analitycs
         */
        $this->time_start = microtime(true);
    }

    /**
     * Search Api's by single terms
     * @param Request
     * @param string
     * @param string
     */
    public function byTerms(Request $request, string $term, string $page): JsonResponse
    {
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

        CompletedSearchQuery::dispatchAfterResponse($request, $searchQuery);

        return $response;
    }

    /**
     * Search file by unique id
     * @param $provider
     * @param $id 
     * @return JsonResponse
     */
    public function byId(Request $request, $provider, $id): JsonResponse
    {

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

        CompletedSearchQuery::dispatchAfterResponse($request, $searchQuery);

        /**
         * Log query analitycs
         */
        $execution_time = (microtime(true) - $this->time_start);
        $this->SetAnalytics($execution_time, __FUNCTION__);
        
        return $response;
    }



    /**
     * Test query without async process
     * @param Request
     * @param string
     * @param string
     */
    public function test(Request $request, string $term, string $page): JsonResponse
    {

        $queryOptions = [
            "name" => "byTerms",
            "term" => (new Term('baseball')),
            "page" => $page,
            "filters" => $request->all(),
        ];

        $searchQuery = (new SearchQuery($queryOptions))->run();

        $response = $searchQuery->response()->header('X-Ugo-Cache', 'miss');
        return $response;
    }

    /**
     * Set analitycs data after request is done
     * @param float
     * @param string
     */
    private function SetAnalytics(float $execution_time, string $name): void
    {
        $channelName = "analytics";
        // Store request Analytics data (Request time)
        log::channel($channelName)->info("Search: {$name} take: {$execution_time} sec");
    }
}
