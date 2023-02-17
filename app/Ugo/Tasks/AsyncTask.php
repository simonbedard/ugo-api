<?php

namespace App\Ugo\Tasks;

use Spatie\Async\Task;

class AsyncTask extends Task
{

    public array $config;
    public $searchQuery;
    public float $time_start;

    function __construct($provider, $searchQuery)
    {
        $this->time_start = microtime(true);
        $this->config = $provider;
        $this->searchQuery = $searchQuery;
    }
    public function configure(): void
    {
        // Setup eg. dependency container, load config,...
    }

    /**
     * Run asyncronous task
     */
    public function run(): array
    {


        $provierClass = (new $this->config['provider']($this->config));

        $body = $provierClass->get($this->searchQuery->term, $this->searchQuery->page, $this->searchQuery->filters)->format();

        $warnings = $provierClass->warnings();
        $errors = $provierClass->errors();
        $execution_time = (microtime(true) - $this->time_start);

        return [
            "body" => $body,
            "warning" => $warnings,
            "errors" => $errors,
            "time" => $execution_time,
        ];
    }
}
