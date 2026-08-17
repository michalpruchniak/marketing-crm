<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Secrets Storage Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "db", "hashicorp"
    |
    */

    'driver' => env('SECRETS_DRIVER', 'db'),

    'hashicorp' => [
        // From Sail/Docker use host.docker.internal, from host use 127.0.0.1
        'address' => env('HASHICORP_ADDR', 'http://host.docker.internal:8200'),
        'token' => env('HASHICORP_TOKEN', 'root'),
        // Secrets are always written to KV mount "credentials":
        // /v1/credentials/data/{uuid}
    ],

];
