<?php

namespace App\Observers;

use Spatie\Crawler\Crawler;
use Psr\Http\Message\UriInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ShopifyBurstObserver extends CrawlObserver
{

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        // Create records
        
        $crawl = Crawler::create([
            'url' => $url
        ], [
            'status' => $response->getStatusCode()
        ]);

      
        dd($response);
        
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        Log::error('crawlFailed: ' . $url);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        //
    }
}