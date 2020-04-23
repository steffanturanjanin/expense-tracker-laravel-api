<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
   Route::post('login', 'AuthController@login');
   Route::post('signup', 'AuthController@signup');

   Route::middleware(['auth:api'])->group(function () {
       Route::post('logout', 'AuthController@logout');
       Route::get('user', 'AuthController@user');
   });
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('categories', 'CategoryController@store');
    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{id}', 'CategoryController@show');
    Route::delete('categories/{id}', 'CategoryController@destroy');


    Route::get('expenses/{year}/{month}', 'ExpenseController@getExpensesByMonth');
    Route::get('expenses/months', 'ExpenseController@getMonths');
    Route::delete('expenses/{id}', 'ExpenseController@destroy');
    Route::get('expenses', 'ExpenseController@index');
    Route::post('expenses', 'ExpenseController@store');

    Route::get('date/expenses', 'ExpenseController@getDateExpensesByYearAndMonth');
    Route::get('date/expenses/{year}/{month}', 'ExpenseController@getDateExpensesByMonth');

});
