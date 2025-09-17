<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ElasticsearchService;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\JobPosting;
use App\Models\VolunteerOpportunity;
use App\Models\DonationCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * Global search across all modules
     */
    public function globalSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0',
            'modules' => 'sometimes|array',
            'modules.*' => 'in:businesses,matrimony,jobs,volunteers,donations'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query');
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);
            $modules = $request->input('modules', []);

            // Get indices based on requested modules
            $indices = [];
            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $indices[] = config("elasticsearch.indices.{$module}.name");
                }
            }

            $results = $this->elasticsearchService->globalSearch($query, $indices, $size, $from);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? []),
                    'took' => $results['took'] ?? 0,
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Global search failed', ['error' => $e->getMessage(), 'query' => $request->input('query')]);
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search businesses
     */
    public function searchBusinesses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'sometimes|string|min:2|max:255',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'verified_only' => 'sometimes|boolean',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query', '*');
            $category = $request->input('category');
            $location = $request->input('location');
            $verifiedOnly = $request->input('verified_only', false);
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);

            $searchParams = [
                'index' => config('elasticsearch.indices.businesses.name'),
                'body' => [
                    'query' => $this->buildBusinessQuery($query, $category, $location, $verifiedOnly),
                    'size' => $size,
                    'from' => $from,
                    'sort' => [
                        ['_score' => ['order' => 'desc']],
                        ['created_at' => ['order' => 'desc']]
                    ]
                ]
            ];

            $results = $this->elasticsearchService->search($searchParams);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'filters' => compact('category', 'location', 'verifiedOnly'),
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? [], 'business'),
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Business search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Business search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search matrimony profiles
     */
    public function searchMatrimony(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'age_min' => 'sometimes|integer|min:18|max:100',
            'age_max' => 'sometimes|integer|min:18|max:100',
            'location' => 'sometimes|string',
            'caste' => 'sometimes|string',
            'education' => 'sometimes|string',
            'occupation' => 'sometimes|string',
            'gender' => 'sometimes|in:male,female,other',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $filters = $request->only(['age_min', 'age_max', 'location', 'caste', 'education', 'occupation', 'gender']);
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);

            $searchParams = [
                'index' => config('elasticsearch.indices.matrimony.name'),
                'body' => [
                    'query' => $this->buildMatrimonyQuery($filters),
                    'size' => $size,
                    'from' => $from,
                    'sort' => [
                        ['created_at' => ['order' => 'desc']]
                    ]
                ]
            ];

            $results = $this->elasticsearchService->search($searchParams);

            return response()->json([
                'success' => true,
                'data' => [
                    'filters' => $filters,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? [], 'matrimony'),
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Matrimony search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Matrimony search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search job postings
     */
    public function searchJobs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'sometimes|string|min:2|max:255',
            'location' => 'sometimes|string',
            'category' => 'sometimes|string',
            'experience_level' => 'sometimes|in:entry,mid,senior,executive',
            'employment_type' => 'sometimes|in:full_time,part_time,contract,freelance,internship',
            'salary_min' => 'sometimes|numeric|min:0',
            'salary_max' => 'sometimes|numeric|min:0',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query', '*');
            $filters = $request->only(['location', 'category', 'experience_level', 'employment_type', 'salary_min', 'salary_max']);
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);

            $searchParams = [
                'index' => config('elasticsearch.indices.jobs.name'),
                'body' => [
                    'query' => $this->buildJobQuery($query, $filters),
                    'size' => $size,
                    'from' => $from,
                    'sort' => [
                        ['_score' => ['order' => 'desc']],
                        ['created_at' => ['order' => 'desc']]
                    ]
                ]
            ];

            $results = $this->elasticsearchService->search($searchParams);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'filters' => $filters,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? [], 'job'),
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Job search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Job search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search volunteer opportunities
     */
    public function searchVolunteers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'sometimes|string|min:2|max:255',
            'location' => 'sometimes|string',
            'skills' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query', '*');
            $filters = $request->only(['location', 'skills', 'start_date', 'end_date']);
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);

            $searchParams = [
                'index' => config('elasticsearch.indices.volunteers.name'),
                'body' => [
                    'query' => $this->buildVolunteerQuery($query, $filters),
                    'size' => $size,
                    'from' => $from,
                    'sort' => [
                        ['start_date' => ['order' => 'asc']],
                        ['_score' => ['order' => 'desc']]
                    ]
                ]
            ];

            $results = $this->elasticsearchService->search($searchParams);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'filters' => $filters,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? [], 'volunteer'),
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Volunteer search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Volunteer search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search donation causes
     */
    public function searchDonations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'sometimes|string|min:2|max:255',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'urgency' => 'sometimes|in:low,medium,high,critical',
            'active_only' => 'sometimes|boolean',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query', '*');
            $filters = $request->only(['category', 'location', 'urgency', 'active_only']);
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);

            $searchParams = [
                'index' => config('elasticsearch.indices.donations.name'),
                'body' => [
                    'query' => $this->buildDonationQuery($query, $filters),
                    'size' => $size,
                    'from' => $from,
                    'sort' => [
                        ['urgency_score' => ['order' => 'desc']],
                        ['_score' => ['order' => 'desc']]
                    ]
                ]
            ];

            $results = $this->elasticsearchService->search($searchParams);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'filters' => $filters,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? [], 'donation'),
                    'pagination' => [
                        'from' => $from,
                        'size' => $size,
                        'total' => $results['hits']['total']['value'] ?? 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Donation search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Donation search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get search suggestions/autocomplete
     */
    public function getSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:1|max:100',
            'modules' => 'sometimes|array',
            'modules.*' => 'in:businesses,matrimony,jobs,volunteers,donations',
            'size' => 'sometimes|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query');
            $modules = $request->input('modules', []);
            $size = $request->input('size', 10);

            $indices = [];
            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $indices[] = config("elasticsearch.indices.{$module}.name");
                }
            }

            $results = $this->elasticsearchService->getSuggestions($query, $indices, $size);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'suggestions' => $this->formatSuggestions($results['suggest'] ?? [])
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Suggestions failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Suggestions failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Location-based search
     */
    public function locationSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|integer|min:1|max:500',
            'modules' => 'sometimes|array',
            'modules.*' => 'in:businesses,matrimony,jobs,volunteers,donations',
            'size' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = $request->input('query');
            $lat = $request->input('latitude');
            $lon = $request->input('longitude');
            $radius = $request->input('radius', 50);
            $modules = $request->input('modules', []);
            $size = $request->input('size', 20);

            $indices = [];
            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $indices[] = config("elasticsearch.indices.{$module}.name");
                }
            }

            $results = $this->elasticsearchService->locationSearch($query, $lat, $lon, $radius, $indices, $size);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'location' => ['latitude' => $lat, 'longitude' => $lon],
                    'radius' => $radius,
                    'total' => $results['hits']['total']['value'] ?? 0,
                    'results' => $this->formatSearchResults($results['hits']['hits'] ?? []),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Location search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Location search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods for building queries
    private function buildBusinessQuery($query, $category, $location, $verifiedOnly)
    {
        $must = [];
        $filter = [];

        if ($query !== '*') {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['business_name^3', 'description^2', 'category', 'products', 'services'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        if ($category) {
            $filter[] = ['term' => ['category.keyword' => $category]];
        }

        if ($location) {
            $must[] = [
                'match' => [
                    'location' => $location
                ]
            ];
        }

        if ($verifiedOnly) {
            $filter[] = ['term' => ['verification_status' => 'approved']];
        }

        $query = ['bool' => []];
        if (!empty($must)) $query['bool']['must'] = $must;
        if (!empty($filter)) $query['bool']['filter'] = $filter;
        if (empty($must) && empty($filter)) $query = ['match_all' => new \stdClass()];

        return $query;
    }

    private function buildMatrimonyQuery($filters)
    {
        $must = [];
        $filter = [];

        if (isset($filters['age_min']) || isset($filters['age_max'])) {
            $range = [];
            if (isset($filters['age_min'])) $range['gte'] = $filters['age_min'];
            if (isset($filters['age_max'])) $range['lte'] = $filters['age_max'];
            $filter[] = ['range' => ['age' => $range]];
        }

        foreach (['location', 'caste', 'education', 'occupation', 'gender'] as $field) {
            if (isset($filters[$field])) {
                $filter[] = ['term' => [$field . '.keyword' => $filters[$field]]];
            }
        }

        // Only show approved profiles
        $filter[] = ['term' => ['approval_status' => 'approved']];

        return ['bool' => ['filter' => $filter]];
    }

    private function buildJobQuery($query, $filters)
    {
        $must = [];
        $filter = [];

        if ($query !== '*') {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'description^2', 'skills_required', 'category'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        foreach (['location', 'category', 'experience_level', 'employment_type'] as $field) {
            if (isset($filters[$field])) {
                $filter[] = ['term' => [$field . '.keyword' => $filters[$field]]];
            }
        }

        if (isset($filters['salary_min']) || isset($filters['salary_max'])) {
            $range = [];
            if (isset($filters['salary_min'])) $range['gte'] = $filters['salary_min'];
            if (isset($filters['salary_max'])) $range['lte'] = $filters['salary_max'];
            $filter[] = ['range' => ['salary_max' => $range]];
        }

        // Only show active jobs
        $filter[] = ['term' => ['status' => 'active']];

        $query = ['bool' => []];
        if (!empty($must)) $query['bool']['must'] = $must;
        if (!empty($filter)) $query['bool']['filter'] = $filter;
        if (empty($must) && empty($filter)) $query = ['match_all' => new \stdClass()];

        return $query;
    }

    private function buildVolunteerQuery($query, $filters)
    {
        $must = [];
        $filter = [];

        if ($query !== '*') {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'description^2', 'required_skills', 'organization'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        foreach (['location', 'skills'] as $field) {
            if (isset($filters[$field])) {
                $must[] = ['match' => [$field => $filters[$field]]];
            }
        }

        if (isset($filters['start_date']) || isset($filters['end_date'])) {
            $range = [];
            if (isset($filters['start_date'])) $range['gte'] = $filters['start_date'];
            if (isset($filters['end_date'])) $range['lte'] = $filters['end_date'];
            $filter[] = ['range' => ['start_date' => $range]];
        }

        // Only show active opportunities
        $filter[] = ['term' => ['status' => 'active']];

        $query = ['bool' => []];
        if (!empty($must)) $query['bool']['must'] = $must;
        if (!empty($filter)) $query['bool']['filter'] = $filter;
        if (empty($must) && empty($filter)) $query = ['match_all' => new \stdClass()];

        return $query;
    }

    private function buildDonationQuery($query, $filters)
    {
        $must = [];
        $filter = [];

        if ($query !== '*') {
            $must[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'description^2', 'category', 'organization'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        foreach (['category', 'location', 'urgency'] as $field) {
            if (isset($filters[$field])) {
                $filter[] = ['term' => [$field . '.keyword' => $filters[$field]]];
            }
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $filter[] = ['term' => ['status' => 'active']];
            $filter[] = ['range' => ['end_date' => ['gte' => 'now']]];
        }

        $query = ['bool' => []];
        if (!empty($must)) $query['bool']['must'] = $must;
        if (!empty($filter)) $query['bool']['filter'] = $filter;
        if (empty($must) && empty($filter)) $query = ['match_all' => new \stdClass()];

        return $query;
    }

    private function formatSearchResults($hits, $type = null)
    {
        return array_map(function ($hit) use ($type) {
            $result = [
                'id' => $hit['_id'],
                'type' => $hit['_index'],
                'score' => $hit['_score'],
                'source' => $hit['_source']
            ];

            if (isset($hit['highlight'])) {
                $result['highlight'] = $hit['highlight'];
            }

            if (isset($hit['sort'])) {
                $result['sort'] = $hit['sort'];
            }

            return $result;
        }, $hits);
    }

    private function formatSuggestions($suggestions)
    {
        $formatted = [];
        
        foreach ($suggestions as $suggestionType => $suggestion) {
            if (isset($suggestion[0]['options'])) {
                foreach ($suggestion[0]['options'] as $option) {
                    $formatted[] = [
                        'text' => $option['text'],
                        'score' => $option['_score'],
                        'type' => str_replace('_suggest', '', $suggestionType)
                    ];
                }
            }
        }

        return $formatted;
    }
}