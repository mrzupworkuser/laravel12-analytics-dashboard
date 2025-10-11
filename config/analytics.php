<?php

/**
 * Analytics module configuration.
 *
 * @author Manohar Zarkar
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | Cache time-to-live for analytics endpoints in seconds.
    | Can be overridden via ANALYTICS_CACHE_TTL environment variable.
    |
    */
    'cache_ttl_seconds' => env('ANALYTICS_CACHE_TTL', 30),
];


