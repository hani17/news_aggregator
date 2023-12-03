<?php

namespace App\Services;

use App\Services\Interfaces\NewsServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService extends BaseApiService implements NewsServiceInterface
{

    /**
     * @var array<string>
     */
    protected array $apiArticleFields = [
        'title',
        'publishedAt',
        'url',
        'source'
    ];

    public function fetchArticles(): ?Collection
    {
        try {
            $response = Http::get($this->getApiUrl())->throw();
            $results = $response->json('articles');
            return $results
                ? $this->formatResponse($results)
                : null;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function formatResponse(array $data): ?Collection
    {
        if (count($data)) {
            return collect($data)
                ->map(function ($item) {
                    if (Arr::has($item, $this->apiArticleFields)) {
                        return [
                            'api_id' => null,
                            'hash' => $this->createHash(
                                Arr::only($item, ['title', 'publishedAt', 'url'])
                            ),
                            'title' => $item['title'],
                            'published_at' => $item['publishedAt'],
                            'url' => $item['url'],
                            'category' => null,
                            'image_url' => $item['urlToImage'] ?? null,
                            'source' => $item['source']['name'] ?? null
                        ];
                    }
                    return null;
                })
                ->filter(fn($item) => $item != null);
        }
        return null;
    }

    public function getApiUrl(): string
    {
        return
            config('services.news_api.api_url')
            . '?apiKey='
            . config('services.news_api.api_key')
            . '&country=us';
    }
}
