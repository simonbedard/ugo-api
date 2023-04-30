<?php

namespace App\Ugo\Providers;

use App\Ugo\Providers\ImageProvider;
use App\Ugo\Terms\Term;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class Deposite extends ImageProvider
{
    public static String $name = "Deposite";
    public static $validColors = ["blue", "green", "yellow", "orange", "red", "brown", "violet", "grey", "black", "white"];
    public static array $validSafety = [1, "true", "high"];
    private Client $client;
    private array $formatedJson;

    function __construct($config)
    {
        // Call the parent controller to set the configuration
        parent::__construct($config);
        $this->client = new Client(['base_uri' => 'https://api.depositphotos.com']);
    }
    /**
     * Get real api request
     */
    public function get(Term $term, $page, array $filters)
    {

        try {
            $response =  (new Client())->get('https://api.depositphotos.com/', $this->getRequestOptions($term, $page, $filters));

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
     * Format API response from Deposite provider
     */
    public function format()
    {
        $this->formatedJson = [];

        /**
         * Request must not be failed and result must be available
         */
        if (!$this->failed && isset($this->data['result'])) {
            foreach ($this->data['result'] as $key => $value) {
                array_push($this->formatedJson, [
                    "provider" => self::$name,
                    "width" => $value['width'],
                    "height" => $value['height'],
                    "id" => $value['id'],
                    "src" => $value['huge_thumb'],
                ]);
            }
        }

        return $this->formatedJson;
    }

    /**
     * Format array filter to be ready for unsplash request
     * This function will get the default filters in request and format it to unsplash 
     */
    public function getRequestOptions(Term $term, $page, array $filters)
    {
        $newArray = [
            'http_errors' => false,
            'headers' => [],
            'query' => [
                'dp_command' => "search",
                'dp_apikey' => $this->config['auth'],
                "dp_search_query" => $term->term,
                "dp_search_limit" => 40,

            ]
        ];

        // Return by default id the filters array is empty
        if (empty($filters)) return $newArray;

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'color':
                    $color = strtolower($value);
                    if (in_array($color, $this->validColors)) {
                        // Color value is good
                        $newArray['query']['dp_search_color'] = $color;
                    } else {
                        // Add warning to request but do not break request
                        array_push($this->warnings, self::$name . ": The color param is not a valide attribute");
                    }
                    break;
                default:
                    // Add warning $key not found to request but do not break request
                    array_push($this->warnings, self::$name . ": The {$key} param is not a valide attribute");
                    break;
            }
        }

        return $newArray;
    }

    /**
     * Get single file from provider
     */
    public function getSingleFile($id)
    {

        $response =  (new Client())->get('https://api.depositphotos.com/', $this->geSingleRequestOptions($id));
        /**
         * Format the response to json
         */
        $this->data = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() != 200) {
            array_push($this->errors, $this->data["error"]['errormsg']);
            $this->failed = true;
        } else {
            $this->failed = false;
        }
        return $this;
    }

    public function geSingleRequestOptions($id)
    {
        $newArray = [
            'http_errors' => false,
            'headers' => [],
            'query' => [
                'dp_command' => "getMediaData",
                'dp_apikey' => $this->config['auth'],
                "dp_media_id" => $id,
            ]
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
            array_push($this->formatedJson, [
                "provider" => self::$name,
                "id" => $this->data['id'],
                "views" => $this->data['views'],
                "downloads" => $this->data['downloads'],
                "likes" => null,
                "description" => $this->data['description'],
                "width" => $this->data['width'],
                "height" => $this->data['height'],
                "size" => $this->data['original_filesize'],
                "date" => [
                    "created_at" => $this->data['published'],
                    "updated_at" => $this->data['updated']
                ],
                "src" => [
                    "full" => $this->data['url_max_qa'],
                    "regular" => $this->data['large_thumb'],
                    "small" => $this->data['thumb'],
                ],
                "user" => [
                    "id" => $this->data['userid'],
                    "name" => $this->data['username'],
                    "profile_image" => $this->data['avatar'], 
                    "links" => [
                        "profile" => null, // No link profile provide by deposite image request
                    ]
                ],
                "exif" => [],
                "links" => [
                    "html" => $this->data['itemurl'],
                    "download" => "Deposite image are only available to download directly from the pixabay webpage",
                ],

            ]);
        }

        return $this->formatedJson;
    }
}
