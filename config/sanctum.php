<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    | Not used by SEAPEDIA (we use plain Bearer tokens, not cookie-based SPA
    | auth), but kept here since Sanctum's service provider reads this key.
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    | Number of minutes until an API token expires. SEAPEDIA sets this to
    | 7 days (10080 minutes) by default so demo/evaluator sessions stay
    | logged in comfortably, while still expiring rather than living
    | forever (security hardening — see README section 5).
    */
    'expiration' => (int) env('SANCTUM_TOKEN_EXPIRATION', 10080),

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
