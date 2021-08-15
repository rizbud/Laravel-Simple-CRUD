<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function(Request $request) {
    return response()->json([
        'response' => [
            'status' => 200,
            'message' => getMessage(200),
            'url' => $request->fullUrl()
        ],
        'data' => [
            'message' => 'Root'
        ]
    ], 200);
});
