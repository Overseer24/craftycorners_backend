<?php

return [



    'paths' => ['*', 'api/*', 'sanctum/csrf-cookie','storage/*'],

    'allowed_methods' => ['*, GET, POST, PUT, DELETE, OPTIONS, HEAD'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-XSRF-TOKEN', 'X-CSRF-TOKEN','Authorization'],

    'exposed_headers' => [''],

    'max_age' => 0,

    'supports_credentials' => true,
];
