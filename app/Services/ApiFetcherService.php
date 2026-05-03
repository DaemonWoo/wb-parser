<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiFetcherService
{
    protected string $baseUrl;

    protected string $key;

    protected int $limit = 500;

    public function __construct()
    {
        $config = config('services.wb');

        $this->baseUrl = "{$config['protocol']}://{$config['host']}:{$config['port']}/api";
        $this->key = $config['key'];
    }

    public function fetchPage(string $endpoint, array $params, int $page): ?array
    {
        $response = Http::retry(3, 2000)->timeout(30)->get("{$this->baseUrl}/{$endpoint}", array_merge($params, [
            'page' => $page,
            'limit' => $this->limit,
            'key' => $this->key,
        ]));

        if (! $response->successful()) {
            return null;
        }

        return $response->json()['data'] ?? [];
    }

    public function saveChunks(string $model, array $items, array $uniqueKeys): void
    {
        $chunks = collect($items)->chunk(500);

        foreach ($chunks as $chunk) {
            if ($chunk->isEmpty()) {
                continue;
            }
            $model::upsert(
                $chunk->toArray(),
                $uniqueKeys,
                array_keys($chunk->first())
            );
        }
    }

    public function getTotalPages(string $endpoint, array $params): int
    {
        $response = Http::retry(3, 2000)->timeout(30)->get("{$this->baseUrl}/{$endpoint}", array_merge($params, [
            'page' => 1,
            'limit' => 1,
            'key' => $this->key,
        ]));

        $total = ceil(($response->json()['meta']['total'] ?? 0) / $this->limit);

        return $total;
    }
}
