<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Elasticsearch Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the default elasticsearch connection that gets used
    | when using the Elasticsearch client.
    |
    */
    'default' => env('ELASTICSEARCH_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each Elasticsearch
    | server that is used by your application.
    |
    */
    'connections' => [
        'default' => [
            'hosts' => [
                [
                    'host' => env('ELASTICSEARCH_HOST', 'localhost'),
                    'port' => env('ELASTICSEARCH_PORT', 9200),
                    'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
                    'user' => env('ELASTICSEARCH_USER'),
                    'pass' => env('ELASTICSEARCH_PASS'),
                ]
            ],
            'retries' => 2,
            'handler' => 'default',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for search functionality
    |
    */
    'search' => [
        'max_results' => 100,
        'default_size' => 20,
        'highlight' => [
            'enabled' => true,
            'fields' => ['title', 'description', 'name'],
        ],
        'fuzzy' => [
            'enabled' => true,
            'fuzziness' => 'AUTO',
        ],
        'location' => [
            'default_radius' => 50, // kilometers
            'max_radius' => 500,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Elasticsearch indices
    |
    */
    'indices' => [
        'businesses' => [
            'name' => env('ELASTICSEARCH_BUSINESS_INDEX', 'mali_setu_businesses'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
        'matrimony' => [
            'name' => env('ELASTICSEARCH_MATRIMONY_INDEX', 'mali_setu_matrimony'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
        'jobs' => [
            'name' => env('ELASTICSEARCH_JOBS_INDEX', 'mali_setu_jobs'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
        'volunteers' => [
            'name' => env('ELASTICSEARCH_VOLUNTEERS_INDEX', 'mali_setu_volunteers'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
        'donations' => [
            'name' => env('ELASTICSEARCH_DONATIONS_INDEX', 'mali_setu_donations'),
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
    ],
];