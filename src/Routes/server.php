<?php

/**
 * Routes which is neccessary for the SSO server.
 */

Route::middleware(config('laravel-sso.routeGroupMiddleware'))
    ->prefix(trim(config('laravel-sso.routePrefix'), ' /'))
    ->group(function () {
        Route::post('login', 'n0izestr3am\LaravelSSO\Controllers\ServerController@login');
        Route::post('logout', 'n0izestr3am\LaravelSSO\Controllers\ServerController@logout');
        Route::middleware(config('laravel-sso.routeAttachMiddleware'))
            ->get('attach', 'n0izestr3am\LaravelSSO\Controllers\ServerController@attach');
        Route::get('userInfo', 'n0izestr3am\LaravelSSO\Controllers\ServerController@userInfo');
    });
