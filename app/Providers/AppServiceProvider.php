<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // view()->composer('*', function ($view) {
        //     $view->with('dollar_rate', Session::get('dollar_rate'));
        //     $view->with('is_nigeria', Session::get('is_nigeria'));
        // });
        // $val = Session::get('dollar_rate');
        // $val = $request->session()->get('key', 'default');;

        // $response = Http::get('https://free.currconv.com/api/v7/convert?q=USD_NGN&compact=ultra&apiKey=8fa6c6f0698970300589');
        $response = Http::get('https://openexchangerates.org/api/latest.json?app_id=8c8c207bcbab4c14970a06d7fd4f92c2');
        $resp = json_decode($response);
        // dd($resp->rates->NGN);
        // $dollar_rate = $resp->USD_NGN;
        $dollar_rate = $resp->rates->NGN;
        // dd($dollar_rate);
        $is_nigeria = false;
        // dd(Auth::user());
        // if(auth()->user() && auth()->user()->country_id == 566){
        //     $is_nigeria = true;
        // }
        // dd($dollar_rate, " ", auth()->user());

        $file_location = "storage/pictures/";
        View::share([
            'file_location' => $file_location,
            'dollar_rate' => $dollar_rate,
        ]);
    }
}