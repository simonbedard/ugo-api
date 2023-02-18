<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CompletedSearchQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $request;
    public $query;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $searchQuery)
    {
        $this->request = $request;
        $this->query = $searchQuery;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Set query in cache
         */
        $this->setCache();

    }

    /**
     * Set the result of the query to the cache layer
     */
    private function setCache(): void
    {
        if(!$this->query->isEmpty()){
            // REFACTOR IF CACHE IS ENABLE and prevent cache when empty response. Dont need to cache empty response
            Cache::put($this->request->fullUrl(), $this->query);
        }

    }

}
