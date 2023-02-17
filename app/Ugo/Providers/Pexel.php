<?php

namespace App\Ugo\Providers;

use App\Ugo\Providers\ImageProvider;
use App\Ugo\Terms\Term;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class Pexel extends ImageProvider
{
    public static String $name = "Pexel";
    public static array $validColors = ["red", "orange", "yellow", "green", "turquoise", "blue", "violet", "pink", "brown", "black", "gray", "white"];
    private Client $client;
    private array $formatedJson;

    function __construct($config)
    {
        // Call the parent controller to set the configuration
        parent::__construct($config);
        $this->client = new Client(['base_uri' => 'https://api.pexels.com/']);
    }

    /**
     * Get real api request
     */
    public function get(Term $term, $page, array $filters)
    {

        try {
            $client = new Client(['base_uri' => 'https://api.pexels.com/']);
            $response = $client->get('v1/search', $this->getRequestOptions($term, $page, $filters));
            $this->data = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() != 200) {
                //array_push($this->errors, ...$this->data["errors"]);
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
     * Format API response from Pexel provider
     */
    public function format()
    {
        $this->formatedJson = [];
        if (!$this->failed && isset($this->data['photos'])) {
            foreach ($this->data['photos'] as $key => $value) {
                array_push($this->formatedJson, [

                    "provider" => self::$name,
                    "width" => $value['width'],
                    "height" => $value['height'],
                    "id" => $value['id'],
                    "src" => $value['src']['large'],
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
            'headers' => [
                'Authorization' => $this->config['auth']
            ],
            'query' => [
                'query' => $term->term,
                'page' => $page,
                "per_page" => $this->config['per_page'],
            ],

        ];

        // Return by default id the filters array is empty
        if (empty($filters)) return $newArray;


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


        $response = $this->client->get("v1/photos/{$id}", $this->geSingletRequestOptions());

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
                'Authorization' => $this->config['auth']
            ],
        ];

        return $newArray;
    }

    public function formatSingle()
    {
        $this->formatedJson = [];;

        /**
         * Request must not be failed and result must be available
         */
        if (!$this->failed && isset($this->data)) {

            array_push($this->formatedJson, [
                "provider" => self::$name,
                "id" => $this->data['id'],
                "views" => null,
                "downloads" => null,
                "likes" => null,
                "description" => $this->data['alt'],
                "width" => $this->data['width'],
                "height" => $this->data['height'],
                "size" => "Should scrap HTML or exif scraper",
                "date" => [
                    "created_at" => null,
                    "updated_at" => null,
                ],
                "src" => [
                    "full" => $this->data['src']['large2x'],
                    "regular" => $this->data['src']['large'],
                    "small" => $this->data['src']['medium'],
                ],

                "exif" => [], //exif_read_data($this->data['src']['original']),
                "links" => [
                    "html" => $this->data['url'],
                    "download" => $this->data['url'],
                ],

            ]);
        }

        return $this->formatedJson;
    }
}
