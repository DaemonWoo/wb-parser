<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiFetcherService
{
    public string $baseUrl;

    public string $key;

    public int $limit = 500;

    public function __construct()
    {
        $config = config('services.wb');
        $this->baseUrl = "{$config['protocol']}://{$config['host']}:{$config['port']}/api";
        $this->key = $config['key'];
    }

    public function getTotalPages(string $endpoint, array $params): int
    {
        $response = Http::retry(3, 2000)->timeout(30)->get(
            "{$this->baseUrl}/{$endpoint}",
            array_merge($params, ['page' => 1, 'limit' => 1, 'key' => $this->key]),
        );

        return (int)ceil(($response->json()['meta']['total'] ?? 0) / $this->limit);
    }

    public function fetchPagesParallel(
        string $endpoint,
        array $params,
        int $startPage,
        int $endPage,
        int $concurrency = 10,
    ): array {
        $results = [];
        $pages = range($startPage, $endPage);
        $chunks = array_chunk($pages, $concurrency);

        foreach ($chunks as $chunk) {
            $responses = Http::pool(function ($pool) use ($chunk, $endpoint, $params) {
                return collect($chunk)->map(function ($page) use ($pool, $endpoint, $params) {
                    return $pool->timeout(30)->retry(3, 2000)->get(
                        "{$this->baseUrl}/{$endpoint}",
                        [...$params, 'page' => $page, 'limit' => $this->limit, 'key' => $this->key],
                    );
                })->all();
            });
            foreach ($responses as $response) {
                if (!$response || !$response->successful()) {
                    continue;
                }
                $data = $response->json()['data'] ?? [];
                if (!empty($data)) {
                    $results[] = $data;
                }
            }
        }

        return $results;
    }
}
