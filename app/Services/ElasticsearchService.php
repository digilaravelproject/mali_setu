<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ElasticsearchService
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->config = config('elasticsearch');
        $this->client = ClientBuilder::create()
            ->setHosts($this->config['connections']['default']['hosts'])
            ->setRetries($this->config['connections']['default']['retries'])
            ->build();
    }

    /**
     * Create an index
     */
    public function createIndex(string $indexName, array $mapping = [])
    {
        try {
            $params = [
                'index' => $indexName,
                'body' => [
                    'settings' => $this->config['indices'][str_replace('mali_setu_', '', $indexName)]['settings'] ?? [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                    ],
                    'mappings' => $mapping
                ]
            ];

            if (!$this->indexExists($indexName)) {
                $response = $this->client->indices()->create($params);
                Log::info("Elasticsearch index created: {$indexName}", $response);
                return $response;
            }

            return ['acknowledged' => true, 'message' => 'Index already exists'];
        } catch (\Exception $e) {
            Log::error("Failed to create Elasticsearch index: {$indexName}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Check if index exists
     */
    public function indexExists(string $indexName): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $indexName]);
        } catch (\Exception $e) {
            Log::error("Failed to check if index exists: {$indexName}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Index a document
     */
    public function indexDocument(string $indexName, string $id, array $body)
    {
        try {
            $params = [
                'index' => $indexName,
                'id' => $id,
                'body' => $body
            ];

            $response = $this->client->index($params);
            Log::info("Document indexed in {$indexName}", ['id' => $id]);
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to index document in {$indexName}", ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update a document
     */
    public function updateDocument(string $indexName, string $id, array $body)
    {
        try {
            $params = [
                'index' => $indexName,
                'id' => $id,
                'body' => [
                    'doc' => $body
                ]
            ];

            $response = $this->client->update($params);
            Log::info("Document updated in {$indexName}", ['id' => $id]);
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to update document in {$indexName}", ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete a document
     */
    public function deleteDocument(string $indexName, string $id)
    {
        try {
            $params = [
                'index' => $indexName,
                'id' => $id
            ];

            $response = $this->client->delete($params);
            Log::info("Document deleted from {$indexName}", ['id' => $id]);
            return $response;
        } catch (\Exception $e) {
            Log::error("Failed to delete document from {$indexName}", ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Search documents
     */
    public function search(array $params)
    {
        try {
            $response = $this->client->search($params);
            Log::info('Elasticsearch search executed', ['query' => $params['body']['query'] ?? 'No query']);
            return $response;
        } catch (\Exception $e) {
            Log::error('Elasticsearch search failed', ['error' => $e->getMessage(), 'params' => $params]);
            throw $e;
        }
    }

    /**
     * Global search across multiple indices
     */
    public function globalSearch(string $query, array $indices = [], int $size = 20, int $from = 0)
    {
        $searchIndices = empty($indices) ? array_values($this->config['indices']) : $indices;
        
        $params = [
            'index' => implode(',', array_column($searchIndices, 'name')),
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^3', 'name^3', 'description^2', 'skills', 'category', 'location'],
                        'fuzziness' => 'AUTO',
                        'operator' => 'or'
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'title' => new \stdClass(),
                        'name' => new \stdClass(),
                        'description' => new \stdClass()
                    ]
                ],
                'size' => $size,
                'from' => $from
            ]
        ];

        return $this->search($params);
    }

    /**
     * Location-based search
     */
    public function locationSearch(string $query, float $lat, float $lon, int $radius = 50, array $indices = [], int $size = 20)
    {
        $searchIndices = empty($indices) ? array_values($this->config['indices']) : $indices;
        
        $params = [
            'index' => implode(',', array_column($searchIndices, 'name')),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['title^3', 'name^3', 'description^2', 'skills', 'category'],
                                'fuzziness' => 'AUTO'
                            ]
                        ],
                        'filter' => [
                            'geo_distance' => [
                                'distance' => $radius . 'km',
                                'location' => [
                                    'lat' => $lat,
                                    'lon' => $lon
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    [
                        '_geo_distance' => [
                            'location' => [
                                'lat' => $lat,
                                'lon' => $lon
                            ],
                            'order' => 'asc',
                            'unit' => 'km'
                        ]
                    ]
                ],
                'size' => $size
            ]
        ];

        return $this->search($params);
    }

    /**
     * Get search suggestions/autocomplete
     */
    public function getSuggestions(string $query, array $indices = [], int $size = 10)
    {
        $searchIndices = empty($indices) ? array_values($this->config['indices']) : $indices;
        
        $params = [
            'index' => implode(',', array_column($searchIndices, 'name')),
            'body' => [
                'suggest' => [
                    'title_suggest' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'title_suggest',
                            'size' => $size
                        ]
                    ],
                    'name_suggest' => [
                        'prefix' => $query,
                        'completion' => [
                            'field' => 'name_suggest',
                            'size' => $size
                        ]
                    ]
                ]
            ]
        ];

        return $this->search($params);
    }

    /**
     * Bulk index documents
     */
    public function bulkIndex(array $documents)
    {
        try {
            $params = ['body' => []];

            foreach ($documents as $doc) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $doc['index'],
                        '_id' => $doc['id']
                    ]
                ];
                $params['body'][] = $doc['body'];
            }

            $response = $this->client->bulk($params);
            Log::info('Bulk indexing completed', ['count' => count($documents)]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Bulk indexing failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete an index
     */
    public function deleteIndex(string $indexName)
    {
        try {
            if ($this->indexExists($indexName)) {
                $response = $this->client->indices()->delete(['index' => $indexName]);
                Log::info("Elasticsearch index deleted: {$indexName}");
                return $response;
            }
            return ['acknowledged' => true, 'message' => 'Index does not exist'];
        } catch (\Exception $e) {
            Log::error("Failed to delete Elasticsearch index: {$indexName}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}