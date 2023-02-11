<?php
namespace App\Ugo\Tasks;

use Spatie\Async\Task;

class AsyncTask extends Task
{

    function __construct($provider, $searchQuery) {
        $this->config = $provider;
        $this->searchQuery = $searchQuery;
    }
    public function configure()
    {
        // Setup eg. dependency container, load config,...
    }

    public function run()
    {

        $time_start = microtime(true);

        $provierClass = (new $this->config['provider']($this->config));

        $body = $provierClass->get($this->searchQuery->term, $this->searchQuery->page, $this->searchQuery->filters)->format();

        $warnings = $provierClass->warnings();
        $errors = $provierClass->errors();

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        return [
            "body" => $body,
            "warning" => $warnings,
            "errors" => $errors,
            "time" => $execution_time,
        ];
    }
}