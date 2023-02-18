<?php

namespace App\Ugo\Providers;

class ImageProvider
{

    public array $warnings = [];
    public array $errors = [];
    public bool $failed = false;
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
    public function fake(): ImageProvider
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
    public function fakeSingle(): ImageProvider
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
    public function warnings(): array
    {
        return $this->warnings;
    }
    /**
     * Return warning'S
     */
    public function errors(): array
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
