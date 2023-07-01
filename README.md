# Simple PHP SSO integration for Laravel

This package based on [Simple PHP SSO integration for Laravel](https://github.com/zefy/laravel-sso) package.

### Requirements
* Laravel 6+
* PHP 7.3+

### Documentation
Please read [Simple PHP SSO integration for Laravel docs](https://github.com/zefy/laravel-sso).

# Installation
### Server
Install this package using composer.
```shell
$ composer require n0izestr3am/laravel-sso
```


Copy config file to Laravel project `config/` folder.
```shell
$ php artisan vendor:publish --provider="novandtya\LaravelSSO\SSOServiceProvider"
```


Create table where all brokers will be saved.
```shell
$ php artisan migrate --path=vendor/novandtya/laravel-sso/database/migrations
```


Edit your `app/Http/Kernel.php` by create new middleware like this:
```php
'api' => [
    'throttle:60,1',
    'bindings',
],

'sso' => [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    'bindings',
],

//...
```


Now you should create brokers.
You can create new broker using following Artisan CLI command:
```shell
$ php artisan sso:broker:create {name}
```

----------

### Broker
Install this package using composer.
```shell
$ composer require n0izestr3am/laravel-sso
```


Copy config file to Laravel project `config/` folder.
```shell
$ php artisan vendor:publish --provider="novandtya\LaravelSSO\SSOServiceProvider"
```


Change `type` value in `config/laravel-sso.php` file from `server`
 to `broker`.



Set 3 new options in your `.env` file:
```shell
SSO_SERVER_URL=
SSO_BROKER_NAME=
SSO_BROKER_SECRET=
```
`SSO_SERVER_URL` is your server's http url without trailing slash. `SSO_BROKER_NAME` and `SSO_BROKER_SECRET` must be data which exists in your server's `brokers` table.



Edit your `app/Http/Kernel.php` by adding `\novandtya\LaravelSSO\Middleware\SSOAutoLogin::class` middleware to `$routeMiddleware` array. It should look like this:
```php
protected $routeMiddleware = [
    'auto_login' => \novandtya\LaravelSSO\Middleware\SSOAutoLogin::class,
    //...
];
```

Then use like this:
![Use auto_login middleware](https://i.imgur.com/1p3BTp1.png)



Last but not least, you need to edit `app/Http/Controllers/Auth/LoginController.php`. You should add two functions into `LoginController` class which will authenticate your client through SSO server but not your Broker page.
```php
protected function attemptLogin(Request $request)
{
    $broker = new \novandtya\LaravelSSO\LaravelSSOBroker;

    $credentials = $this->credentials($request);
    return $broker->login($credentials[$this->username()], $credentials['password']);
}

public function logout(Request $request)
{
    $broker = new \novandtya\LaravelSSO\LaravelSSOBroker;

    $broker->logout();

    $this->guard()->logout();

    $request->session()->invalidate();

    return redirect('/');
}
```


That's all. For other Broker pages you should repeat everything from the beginning just changing your Broker name and secret in configuration file.




Example `.env` options:
```shell
SSO_SERVER_URL=https://server.test
SSO_BROKER_NAME=site1
SSO_BROKER_SECRET=892asjdajsdksja74jh38kljk2929023
```
