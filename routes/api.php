<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\CollectionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/collection', [CollectionController::class, 'create']);
});

Route::get('/collection/{id}', [CollectionController::class, 'find']);


Route::middleware('ugo.cache')->name('search.')->group(function () {
    Route::get('/search/terms/{terms}/{page}', [SearchController::class, 'SearchByTerms'])->name('terms');
    Route::get('/search/file/{provider}/{id}/', [SearchController::class, 'SearchByProviderAndId'])->name('id');
});


Route::get('/health', function (Request $request) {
    return "The api is running like butter in a hot pan. Smoothly";
});

Route::get('/test', [SearchController::class, 'test']);
Route::get('/scraper', [SearchController::class, 'scrape']);

