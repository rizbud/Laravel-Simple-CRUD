<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function(Request $request) {
    return response()->json([
        'response' => [
            'status' => 200,
            'message' => getMessage(200),
            'url' => $request->fullUrl()
        ],
        'data' => [
            'message' => 'API Endpoint'
        ]
    ], 200);
});

Route::group(['prefix' => 'v1'], function() {
    Route::get('/', function(Request $request) {
        return response()->json([
            'response' => [
                'status' => 200,
                'message' => getMessage(200),
                'url' => $request->fullUrl()
            ],
            'data' => [
                'message' => 'API V1 Endpoint'
            ]
        ], 200);
    });
    Route::resource('products', ProductController::class, [
        'only' => ['index', 'store', 'show', 'update', 'destroy']
    ]);
});