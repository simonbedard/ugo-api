<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;


class CollectionController extends Controller
{

    /**
     * Post request to create a collection
     */
    public function create(Request $request){

        $uuid = Str::uuid();
        $shareDomaine = "share.ugo.go";
        $collection = [
            'user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            "images" => "{}",
            "share_link" => "https://{$shareDomaine}/{$uuid}",
        ];

        return Collection::create($collection);

    }

    public function update(Request $request, $id){

        $updatedCollection = Collection::findOrFail($id);

        if (!Gate::allows('update-collection', $updatedCollection)) {
            $response = [
                "NOT_ALLOW" => "You are not allow to update this collection because you are not the owner.",
            ];
            return response()->json($response, 403);
        }
        
        $collection = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ];

        $updatedCollection = Collection::findOrFail($id);
        $updatedCollection->update($collection);
        // Return the updated collection
        return $updatedCollection;
    }
    public function delete(Request $request, $id){

        
        try {
            $deletedCollection = Collection::findOrFail($id);

            if (!Gate::allows('update-collection', $deletedCollection)) {
                $response = [
                    "NOT_ALLOW" => "You are not allow to delete this collection because you are not the owner.",
                ];
                return response()->json($response, 403);
            }

            Collection::destroy($id);
            $response = [
                "message" => "Collection with id {$id} has been succesfully delete from the database",
                "collection" =>  $deletedCollection,
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = [
                "NOT_FOUND" => "Enable to delete collection with id {$id}. The collection id is not in the database",
            ];
            return response()->json($response, 400);
        }

    }
    /**
     * Find the collection base on the id
     */
    public function find(Request $request, $id){

        // If the collection is public, everyone can see it, but if private, only the user with the id 
        $collection = Collection::findOrFail($id);
        if($collection->isPublic()){
            return $collection;
        }else{
            return "This is a private collection, you are not allow to view.";
        }
       
    }



    // Image manipulation operation
    
}
