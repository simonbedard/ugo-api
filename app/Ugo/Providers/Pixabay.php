<?php
namespace App\Ugo\Providers;
use App\Ugo\Providers\ImageProvider;
use App\Ugo\Terms\Term;
use GuzzleHttp\Client;
use Illuminate\Http\Exception;

class Pixabay extends ImageProvider
{
    public static $name = "Pixabay";
    public $validColors = ["grayscale", "transparent", "red", "orange", "yellow", "green", "turquoise", "blue", "lilac", "pink", "white", "gray", "black", "brown"];
   
    function __construct($config){
        // Call the parent controller to set the configuration
        parent::__construct($config);
        $this->client = new Client(['base_uri' => 'https://pixabay.com/']);

    }

    /**
     * Get real api request
     */
    public function get(Term $term, $page, Array $filters){
       
        try {
            
            $query = $this->getRequestOptions($term, $page, $filters);
            
            $response = (new Client())->get('https://pixabay.com/api/', $query);

    
            $this->data = json_decode($response->getBody()->getContents(), true);

            if($response->getStatusCode() != 200){
                //array_push($this->errors, ...$this->data["errors"]);
                $this->failed = true;
            }else{
                $this->failed = false;
            }
        }
        catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $this->failed = true;
        }


        return $this;

    }

    /**
     * Format API response from Pexel provider
     */
    public function format(){
        $this->formatedJson = [];
    

        if(!$this->failed && isset($this->data['hits'])){

   

            foreach ($this->data['hits'] as $key => $value) {
                array_push($this->formatedJson,[
                    "provider" => self::$name,
                    "width" => $value['imageWidth'],
                    "height" => $value['imageHeight'],
                    "id" => $value['id'],
                    "src" => $value['webformatURL'],
                ]);
            }
        }


        return $this->formatedJson;
    }
        /**
     * Format array filter to be ready for unsplash request
     * This function will get the default filters in request and format it to unsplash 
     */
    public function getRequestOptions(Term $term, $page, Array $filters){

        $newArray = [
            'http_errors' => false,
            'headers'=> [],
            'query' => [
                'key' => $this->config['auth'],
                'safesearch' => true,
                'order' => $this->config['order_by'],
                'image_type' => 'photo',
                'q' => $term->term,
                'page' => $page,
                "per_page" => 20
            ],

        ];


        // Return by default id the filters array is empty
        if(empty($filters))return $newArray;

        
        foreach($filters as $key => $value){
            switch ($key) {
                case 'color':
                    $color = strtolower($value);
                    if(in_array($color, $this->validColors)){
                        // Color value is good
                        $newArray['query']['colors'] = $color;
                    }else{
                        // Add warning to request but do not break request
                        array_push($this->warnings, self::$name.": The color param is not a valide pixabay attribute");
                    }
                    break;
                default:
                    // Add warning $key not found to request but do not break request
                    array_push($this->warnings, self::$name.": The {$key} param is not a valide attribute");
                    break;
            }
        }

        return $newArray;
    }

    /**
     * Get single file from provider
     */
    public function getSingleFile($id){
        
        $query = $this->geSingleRequestOptions($id);
            
        $response = $this->client->get('api/', $query);


        /**
         * Format the response to json
         */
        $this->data = json_decode($response->getBody()->getContents(), true);
        if($response->getStatusCode() != 200){
            array_push($this->errors, ...$this->data["errors"]);
            $this->failed = true;
        }else{
            $this->failed = false;
        }
        return $this;
    }

    private function geSingleRequestOptions($id){
        $newArray = [
            'http_errors' => false,
            'headers'=> [],
            'query' => [
                'key' => $this->config['auth'],
                'id' => $id
            ]
        ];

        return $newArray;
    }

    public function formatSingle(){
        $this->formatedJson = [];;
        
        /**
         * Request must not be failed and result must be available
         */
        if(!$this->failed && isset($this->data)){
            $value = $this->data['hits'][0];
            array_push($this->formatedJson,[
                "provider" => self::$name,
                "id" => $value['id'],
                "views" => $value['views'],
                "downloads" => $value['downloads'],
                "likes" => $value['likes'],
                "description" => $value['tags'],
                "width" => $value['imageWidth'],
                "height" => $value['imageHeight'],
                "size" => $value['imageSize'],
                "date" => [
                    "created_at" => null,
                    "updated_at" => null,
                ],
                "src" => [
                    "full" => $value['largeImageURL'],
                    "regular" => $value['webformatURL'],
                    "small" => $value['previewURL'],
                ],
     
                "exif" => [], //exif_read_data($this->data['src']['original']),
                "links" => [
                    "html" => $value['pageURL'],
                    "download" => "Pixabay image are only available to download directly from the pixabay webpage",
                ],

            ]);
        
        }

        return $this->formatedJson;
    }
}