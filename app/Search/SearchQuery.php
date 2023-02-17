<?php
namespace App\Search;
use Spatie\Async\Pool;
use App\Ugo\Tasks\AsyncTask;

class SearchQuery
{
    function __construct($queryOptions) {
        // Contruct the Search Class
       
        $this->name = $queryOptions['name'];
        $this->term = $queryOptions['term'];
        $this->page = $queryOptions['page'];
        $this->filters = $queryOptions['filters'];
        $this->body = [];
        $this->warnings = [];
        $this->errors = [];
        $this->fatals = [];
        $this->times = [];

    }

    /**
     * Run the query
     */
    public function run(){
    
        // Make request to the external api baby
        $providers = config('ugo.api.provider');
        $defaultOutputLength = 102400;

        /**
         * Validate that there is at lease 1 provider to fetch assets
         */
        if(empty($providers)){
            array_push($this->fatals, "The list of provier is empty. You must provide at least one.");
            return $this;
        }

        $pool = Pool::create();

    
        if(config('ugo.api.fake_data')){
            foreach ($providers as $key => $provider) {
                if(isset($provider['provider'])){
                    try {
                        $fake = (new $provider['provider']($provider))->fake()->format();
                        $this->body = array_merge($this->body, $fake);

                    } catch (\Throwable $th) {
                        array_push($this->errors, "Error with '{$key}' provider: {$th->getMessage()}");
                    }
               
                }
            }
        }else{
            foreach ($providers as $key => $provider) {  
                
                /**
                 *  Create a new task/job
                 */ 
                $task = new AsyncTask($provider, $this);

                /**
                 * Add the task to the async Pool
                 */
                $pool->add($task, $defaultOutputLength)->then(function ($data) {
                    // On success, `$data` is returned by the process or callable you passed to the queue.
                    array_push($this->body, ...$data['body']);
                    array_push($this->warnings, ...$data['warning']);
                    array_push($this->errors, ...$data['errors']);
                    array_push($this->times, $data['time']);

                })->catch(function ($exception) {
                    // When an exception is thrown from within a process, it's caught and passed here.
                    array_push($this->errors, "Error with  provider: {$exception->getMessage()}");
                    throw $exception;
                })->timeout(function () {
                    // A process took too long to finish.
                    dd('timeout');
                });
            }
            $responses = $pool->wait();
        }

        return $this;
    }

    /**
     * Response to the qery builder
     */
    public function response(){

        /**
         * Default response object
         */
        $response = [
            "name" => $this->name,
            "term" => $this->term,
            "filters" => $this->filters,
            "errors" => $this->errors,
            "warnings" => $this->warnings,
            "fake" => config('ugo.api.fake_data'),
            "page" => [
                "current" => $this->page,
                "next" => $this->page+1,
                "next_url" => route('search.terms', ['page' => ($this->page+1), 'terms' => $this->term->get()['term']]),
                "previous_url" => route('search.terms', ['page' => ($this->page-1), 'terms' => $this->term->get()['term']]),
            ],
        ];

        
        if(!empty($this->fatals)){
            $response['fatals'] = $this->fatals;
            $status = 400;
        }else{
            // Build json response
            $response['assets'] = $this->body;
            $status = 200;
        }

        return response()->json($response, $status);

    }
}