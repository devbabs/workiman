<?php

use Illuminate\Support\Facades\Route;

Route::get('', [
    'as' => 'admin.dashboard',
    'uses' => 'AdminController@dashboard'
]);

Route::group(['prefix' => 'contests'], function ()
{
    Route::group(['prefix' => 'addons'], function ()
    {
        Route::match(['get', 'post', 'put'], '{id?}', [
            'as' => 'admin.contests.addons.index',
            'uses' => 'ContestController@addons'
        ]);
    });

    Route::group(['prefix' => 'categories'], function ()
    {
        Route::match(['get', 'post'], '', [
            'as' => 'admin.contests.categories.index',
            'uses' => 'ContestController@categories'
        ]);

        Route::match(['post', 'put', 'delete'], 'sub-category', [
            'as' => 'admin.contests.categories.sub-category',
            'uses' => 'ContestController@subCategory'
        ]);

        Route::match(['get', 'put'], '{id}', [
            'as' => 'admin.contests.categories.show',
            'uses' => 'ContestController@showCategory'
        ]);

        Route::delete('{id}', [
            'as' => 'admin.contests.categories.delete',
            'uses' => 'ContestController@deleteCategory'
        ]);
    });


    Route::resource('', 'ContestController')->names([
        'index' => 'admin.contests.index'
    ]);
});
