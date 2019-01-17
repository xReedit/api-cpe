<?php
return [
     /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'defaults' => array(
        'supportsCredentials' => false,
        'allowedOrigins' => array('*'),
        'allowedHeaders' => array('*'),
        'allowedMethods' => array('POST', 'PUT', 'GET', 'DELETE'),
        'maxAge' => 3600,
        'exposedHeaders' => array(),
        'hosts' => array(),
    ),

    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedHeaders' => ['Content-Type', 'X-Requested-With', 'application/json'],
    'allowedMethods' => ['*'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
    'exposedHeaders' => [],
    'maxAge' => 0,
];