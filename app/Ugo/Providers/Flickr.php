<?php

namespace App\Ugo\Providers;

use App\Ugo\Providers\ImageProvider;
use App\Ugo\Terms\Term;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Flickr extends ImageProvider
{
    public static String $name = "Flickr";
    public static array $validColors = [];
    public static array $validSafety = [1, "true", "high"];
    private Client $client;
    private array $formatedJson;

    function __construct($config)
    {
        // Call the parent controller to set the configuration
        parent::__construct($config);
        $this->client = new Client(['base_uri' => 'https://www.flickr.com/services/rest/']);
    }
    /**
     * Get real api request
     */
    public function get(Term $term, $page, array $filters)
    {

        
        $client = new Client(['base_uri' => 'https://www.flickr.com/services/rest/']);

        try {

            $response = $client->get('', $this->getRequestOptions($term, $page, $filters));
           
            /**
             * Format the response to json
             */
            $this->data = json_decode($response->getBody()->getContents(), true);

    
            if ($response->getStatusCode() != 200) {
                array_push($this->errors, ...$this->data["errors"]);
                $this->failed = true;
            } else {
                $this->failed = false;
            }

        } catch (ClientException $e) {

            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $this->failed = true;
        }

        return $this;
    }


    /**
     * Format API response from Unsplash provider
     */
    public function format()
    {
        $this->formatedJson = [];

        /**
         * Request must not be failed and result must be available
         */
        if (!$this->failed && isset($this->data['photos'])) {
            foreach ($this->data['photos']['photo'] as $key => $value) {
                array_push($this->formatedJson, [
                    "provider" => self::$name,
                    "width" => $value['width_l'] ?? null,
                    "height" => $value['height_l'] ?? null,
                    "id" => $value['id'],
                    "src" => $value['url_l'] ?? null,
                ]);
            }
        }
       
        return $this->formatedJson;
    }

    /**
     * Format array filter to be ready for unsplash request
     * This function will get the default filters in request and format it to unsplash 
     * @param Term
     * @param string
     * @param Array
     */
    public function getRequestOptions(Term $term, $page, array $filters)
    {
        $newArray = [
            'http_errors' => false,
            'headers' => [
                'Accept-Version' => 'v1',
            ],
            'query' => [
                'method' => "flickr.photos.search",
                'api_key' => $this->config['auth'],
                'text' => $term->term,
                'page' => $page,
                'license' => "9",
                'extras' => "views, date, license, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l",
                "per_page" => $this->config['per_page'],
                'sort' => "relevance",
                'format' => 'json',
                'nojsoncallback' => "?",
            ]

        ];

        // Return by default id the filters array is empty
        if (empty($filters)) return $newArray;

        /*
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'color':
                    $color = strtolower($value);
                    if (in_array($color, $this->validColors)) {
                        // Color value is good
                        $newArray['query']['color'] = $color;
                    } else {
                        // Add warning to request but do not break request
                        array_push($this->warnings, self::$name . ": The color param is not a valide Unsplash attribute");
                    }
                    break;
                case 'safety':

                    if (in_array($value, $this->validSafety)) {
                        $newArray['query']['content_filter'] = "high";
                    } else {

                        // Add warning to request but do not break request
                        array_push($this->warnings, self::$name . ": The safety param is not a valide attribute");
                    }
                    break;
                default:
                    // Add warning $key not found to request but do not break request
                    array_push($this->warnings, self::$name . ": The {$key} param is not a valide attribute");
                    break;
            }
        }*/



        return $newArray;
    }

    /**
     * Get single file from provider
     */
    public function getSingleFile($id)
    {

        $response = $this->client->get("photos/{$id}", $this->geSingletRequestOptions());
        /**
         * Format the response to json
         */
        $this->data = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() != 200) {
            array_push($this->errors, ...$this->data["errors"]);
            $this->failed = true;
        } else {
            $this->failed = false;
        }
        return $this;
    }

    public function geSingletRequestOptions()
    {
        $newArray = [
            'http_errors' => false,
            'headers' => [
                'Accept-Version' => 'v1',
                'Authorization' => "Client-ID {$this->config['auth']}"
            ],
        ];

        return $newArray;
    }

    public function formatSingle()
    {
        $this->formatedJson = [];

        /**
         * Request must not be failed and result must be available
         */
        if (!$this->failed && isset($this->data)) {

            //$exif = self::exif($this->data['urls']['raw']);
            $this->formatedJson = [
                "provider" => self::$name,
                "id" => $this->data['id'],
                "views" => $this->data['views'],
                "downloads" => $this->data['downloads'],
                "likes" => $this->data['likes'],
                "description" => $this->data['description'],
                "width" => $this->data['width'],
                "height" => $this->data['height'],
                "size" => "Should scrap HTML or exif scraper",
                "date" => [
                    "created_at" => $this->data['created_at'],
                    "updated_at" => $this->data['updated_at']
                ],
                "src" => [
                    "full" => $this->data['urls']['full'],
                    "regular" => $this->data['urls']['regular'],
                    "small" => $this->data['urls']['small'],
                ],
                "exif" => [],
                "user" => [
                    "id" => $this->data['user']['id'],
                    "name" => $this->data['user']['name'],
                    "profile_image" => $this->data['user']['name'],
                    "links" => [
                        "profile" => $this->data['user']["profile_image"]["medium"]
                    ]
                ],
                "links" => [
                    "html" => $this->data['links']['html'],
                    "download" => $this->data['links']['download']
                ],

            ];
        }
                
        return $this->formatedJson;
    }
}
