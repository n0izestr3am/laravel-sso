<?php

/**
 * Routes which is neccessary for the SSO server.
 */

Route::middleware(config('laravel-sso.routeGroupMiddleware'))
    ->prefix(trim(config('laravel-sso.routePrefix'), ' /'))
    ->group(function () {
        Route::post('login', 'novandtya\LaravelSSO\Controllers\ServerController@login');
        Route::post('logout', 'novandtya\LaravelSSO\Controllers\ServerController@logout');
        Route::middleware(config('laravel-sso.routeAttachMiddleware'))
            ->get('attach', 'novandtya\LaravelSSO\Controllers\ServerController@attach');
        Route::get('userInfo', 'novandtya\LaravelSSO\Controllers\ServerController@userInfo');
    });
