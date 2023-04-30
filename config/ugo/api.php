<?php

return [

    'debug' => env('UGO_DEBUG', false),
    'fake_data' => env('UGO_FAKE_DATA', false),
    'skip_cache' => env('UGO_SKIP_CACHE', false),
    'provider' => [
        
        'unsplash' => [
            "name" => "Unsplash",
            "auth" => env('UNSPLASH_AUTH'),
            "per_page" => env('UNSPLASH_PER_PAGE', 20), // Number of image per request 
            "order_by" => env('UNSPLASH_ORDER_BUY', 'relevant'), // relevant || latest
            "content_filter" => env('UNSPLASH_SAFETY', 'low'), // Limit results by content safety. (Optional; default: low). Valid values are low and high
            "provider" => App\Ugo\Providers\Unsplash::class
        ],
        
        
        'pexel' => [
            "name" => "Pexel",
            "auth" => env('PEXEL_AUTH'),
            "per_page" => env('PEXEL_PER_PAGE', 20), // Number of image per request 
            "provider" => App\Ugo\Providers\Pexel::class
        ],

        'pixabay' => [
            "name" => "Pixabay",
            "auth" => env('PIXABAY_AUTH'),
            "per_page" => env('PIXABAY_PER_PAGE', 20), // Number of image per request 
            "order_by" => env('PIXABAY_ORDER_BUY', 'popular'), //"popular", "latest"
            "provider" => App\Ugo\Providers\Pixabay::class
        ],


        'deposite' => [
            "name" => "Deposite",
            "auth" => env('DEPOSITE_API_KEY'),
            "provider" => App\Ugo\Providers\Deposite::class
        ],
        
        /*
        'test' => [
            "provider" => App\Ugo\Providers\Pexl::class
            // fake provider for testing
        ],*/
    ],

];
