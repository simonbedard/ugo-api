<?php
namespace App\Search;
use Spatie\Async\Pool;
use App\Ugo\Tasks\AsyncTask;

class SearchQuerySingle
{
    function __construct($queryOptions) {
        // Contruct the Search Class
        $this->name = $queryOptions['name'];
        $this->id = $queryOptions['id'];
        $this->provider = $queryOptions['provider'];
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

        /**
         * Validate that there is at lease 1 provider to fetch assets
         */
        if(empty($providers)){
            array_push($this->fatals, "The list of provier is empty. You must provide at least one.");
            return $this;
        }

        // $pool = Pool::create();

        $provierClass = (new $providers[$this->provider]['provider']($providers[$this->provider])); 

        if(config('ugo.api.fake_data')){
            try {
                $fake = $provierClass->fakeSingle()->formatSingle();
                $this->body = array_merge($this->body, $fake);

            } catch (\Throwable $th) {
                array_push($this->errors, "Error with '{$this->provider}' provider: {$th->getMessage()}");
            }
        }else{

            $this->body = $provierClass->getSingleFile($this->id)->formatSingle();
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
            "errors" => $this->errors,
            "warnings" => $this->warnings,
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