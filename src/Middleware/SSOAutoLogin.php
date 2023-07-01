<?php

namespace novandtya\LaravelSSO\Middleware;

use Closure;
use Illuminate\Http\Request;
use novandtya\LaravelSSO\LaravelSSOBroker;

class SSOAutoLogin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $broker = new LaravelSSOBroker();
        $response = $broker->getUserInfo();

        // If client is logged out in SSO server but still logged in broker.
        if (!isset($response['data']) && !auth()->guest()) {
            return $this->logout($request);
        }

        // If there is a problem with data in SSO server, we will re-attach client session.
        if (isset($response['error'])
            && (
                strpos($response['error'], 'There is no saved session data associated with the broker session id') !== false
                || strpos($response['error'], 'User not authenticated') !== false
                || strpos($response['error'], 'User not found') !== false
            )
        ) {
            return $this->clearSSOCookie($request);
        }

        $userIdField = config('laravel-sso.userIdField');

        // If client is logged in SSO server and didn't logged in broker...
        if (isset($response['data']) && (auth()->guest() || auth()->user()->{config('laravel-sso.userIdField')} != $response['data'][$userIdField])) {
            // ... we will authenticate our client.

            $user = config('laravel-sso.usersModel')::query()
                ->firstOrCreate([
                    $userIdField => $response['data'][$userIdField]
                ], $response['data']);

            auth()->login($user);
        }

        return $next($request);
    }

    /**
     * Clearing SSO cookie so broker will re-attach SSO server session.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function clearSSOCookie(Request $request)
    {
        return redirect($request->fullUrl())->cookie(cookie('sso_token_' . config('laravel-sso.brokerName')));
    }

    /**
     * Logging out authenticated user.
     * Need to make a page refresh because current page may be accessible only for authenticated users.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(Request $request)
    {
        auth()->logout();
        return redirect($request->fullUrl());
    }
}
