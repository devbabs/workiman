<?php

use Illuminate\Support\Facades\Route;

Route::get('', [
    'as' => 'admin.dashboard',
    'uses' => 'AdminController@dashboard'
]);

Route::group(['prefix' => 'users'], function () {
    Route::get('{user_category}', [
        'as' => 'admin.users.index',
        'uses' => 'UserController@index'
    ]);

    Route::get('/disable/{id}', 'UserController@disable')->name('admin.users.disable');

    // Route::resource('users', 'UserController')->names([
    //     'index' => 'admin.users.index',
    //     'show' => 'admin.users.show',
    //     'update' => 'admin.users.update',
    // ]);
});

Route::group(['prefix' => 'contests'], function () {
    Route::group(['prefix' => 'addons'], function () {
        Route::match(['get', 'post', 'put'], '{id?}', [
            'as' => 'admin.contests.addons.index',
            'uses' => 'ContestController@addons'
        ]);
    });

    Route::group(['prefix' => 'categories'], function () {
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

Route::group(['prefix' => 'withdrawals'], function () {
    Route::get('approve-reject/{withdrawal}/{status}', [
        'as' => 'admin.withdrawals.approve-reject',
        'uses' => 'WithdrawalsController@approveReject'
    ]);

    Route::get('{status}', [
        'as' => 'admin.withdrawals',
        'uses' => 'WithdrawalsController@index'
    ]);
});

Route::group(['prefix' => 'offers'], function () {
    Route::group(['prefix' => 'addons'], function () {
        Route::match(['get', 'post', 'put'], '{id?}', [
            'as' => 'admin.offers.addons.index',
            'uses' => 'OfferController@addons'
        ]);
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::match(['get', 'post'], '', [
            'as' => 'admin.offers.categories.index',
            'uses' => 'OfferController@categories'
        ]);

        Route::match(['post', 'put', 'delete'], 'sub-category', [
            'as' => 'admin.offers.categories.sub-category',
            'uses' => 'OfferController@subCategory'
        ]);

        Route::match(['get', 'put'], '{id}', [
            'as' => 'admin.offers.categories.show',
            'uses' => 'OfferController@showCategory'
        ]);

        Route::delete('{id}', [
            'as' => 'admin.offers.categories.delete',
            'uses' => 'OfferController@deleteCategory'
        ]);
    });


    Route::resource('', 'OfferController')->names([
        'index' => 'admin.offers.index'
    ]);
});

Route::delete('{id}', [
    'as' => 'admin.offers.categories.delete',
    'uses' => 'OfferController@deleteCategory'
]);

Route::prefix('admin/sliders')->group(function () {
    Route::get('/disable/{id}', 'SliderController@disable')->name('admin.sliders.disable');
    Route::get('/restore/{id}', 'SliderController@restore')->name('admin.sliders.restore');
    Route::get('/disabled', 'SliderController@disabled')->name('admin.sliders.disabled');
    // Route::get('/{link}', [CompaniesController::class, 'displayByLink'])->name('companies.single');
});

Route::resource('/sliders', 'SliderController')->names([
    'index' => 'admin.sliders.index',
    'create' => 'admin.sliders.create',
    'edit' => 'admin.sliders.edit',
    'update' => 'admin.sliders.update',
    'show' => 'admin.sliders.show',
    'delete' => 'admin.sliders.delete',
]);

Route::resource('/admin-users', 'AdminUserController')->names([
    'index' => 'admin.admin-users.index',
    'create' => 'admin.admin-users.create',
    'edit' => 'admin.admin-users.edit',
    'update' => 'admin.admin-users.update',
    'show' => 'admin.admin-users.show',
    'delete' => 'admin.admin-users.delete',
]);