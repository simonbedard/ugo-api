<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{

    /**
     * Post request to create a collection
     */
    public function create(Request $request){
        return Collection::create([
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            "images" => "{}",
            "share_link" => "https://share.ugo.go/uuidgeneratedby",
        ]);

    }

    public function update(Request $request){

    }
    public function delete(Request $request){

    }

    /**
     * Find the collection base on the id
     */
    public function find(Request $request, $id){
        $collection = Collection::findOrFail($id);
        return $collection;
    }
}
