<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => '\App\Http\Controllers\Account'
], function () {
    Route::group([
        'middleware' => 'project-manager',
    ], function () {
        Route::match(['get', 'post'], 'payment/{contest}', [
            'as' => 'contests.payment',
            'uses' => 'ContestController@payment'
        ]);

        Route::post('images', [
            'as' => 'contests.images',
            'uses' => 'ContestController@images'
        ]);

        Route::resource('', 'ContestController')->only([
            'create',
            'store',
        ])->names([
            'create' => 'contests.create',
            'store' => 'contests.store',
        ]);
    });

    Route::post("{slug}/submit", [
        "as" => "contests.submit",
        "uses" => "ContestController@submit"
    ]);
});

Route::get("", [
    "as" => "contests.index",
    "uses" => "ContestController@index"
]);

Route::post("filter", [
    "as" => "contests.filter",
    "uses" => "ContestController@filter"
]);

Route::get("{slug}", [
    "as" => "contests.show",
    "uses" => "ContestController@show"
]);
