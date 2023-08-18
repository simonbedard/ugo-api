<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;



class FileController extends Controller
{


    /**
     * Create file reference in database
     */
    static public function create($file){
        $formatForDatabase = [
            "provider_id" => $file['id'],
            "provider" => $file['provider'],
            "views" =>  $file['views'],
            "downloads" => $file['downloads'],
            "likes" => $file['likes'],
            "description" => $file['description'],
            "width" => $file['width'],
            "height" => $file['height'],
            "size" => "",
            "date" => [
                "created_at" => $file['date']['created_at'],
                "updated_at" => $file['date']['updated_at']
            ],
            "src" => [
                "full" => $file['src']['full'],
                "regular" => $file['src']['regular'],
                "small" => $file['src']['small'],
            ],
            "exif" => [],
            "links" => [
                "html" => $file['links']['html'],
                "download" => $file['links']['download']
            ],

        ];

        return File::updateOrCreate(
            ["provider_id" => $file['id'], "provider" => $file['provider']],
            $formatForDatabase
        );

        
    }

    public function addToCollection(Request $request){}
    public function removeToCollection(Request $request){}
    public function getExifdata(Request $request){}
}