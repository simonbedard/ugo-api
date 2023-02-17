<?php

namespace App\Ugo\Providers;

class ImageProvider
{

    public array $warnings = [];
    public array $errors = [];
    public array $failed = false;
    public array $data = [];
    public array $config;
    public $fake;

    /**
     * Constructor Image Provider class
     */
    function __construct($config)
    {
        $this->config = $config;
    }
    
    /**
     * Retreave fake API request from providers
     */
    public function fake()
    {
        $path = app_path() . "/Ugo/Fake/Json/" . $this->config['name'] . ".json";
        $json = json_decode(file_get_contents($path), true);
        $this->fake = $json;
        $this->data = $json;
        return $this;
    }

    /**
     * Retreave fake API request from single image providers
     */
    public function fakeSingle()
    {
        $path = app_path() . "/Ugo/Fake/Json/Single/" . $this->config['name'] . ".json";
        $json = json_decode(file_get_contents($path), true);
        $this->fake = $json;
        $this->data = $json;
        return $this;
    }

    /**
     * Return warning'S
     */
    public function warnings()
    {
        return $this->warnings;
    }
    /**
     * Return warning'S
     */
    public function errors()
    {
        return $this->errors;
    }

    static public function exif($src)
    {
        try {
            return exif_read_data($src);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
