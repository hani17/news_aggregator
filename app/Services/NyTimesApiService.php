<?php

namespace App\Services;

use App\Services\Interfaces\NewsServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NyTimesApiService extends BaseApiService implements NewsServiceInterface
{
    /**
     * @var array<string>
     */
    protected array $apiArticleFields = [
        '_id',
        'abstract',
        'pub_date',
        'web_url',
        'news_desk',
        'source',
    ];

    public function fetchArticles(): ?Collection
    {
        try {
            $response = Http::get($this->getApiUrl())->throw();
            $results = $response->json('response.docs');

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
                            'api_id' => $item['_id'],
                            'hash' => $this->createHash(
                                Arr::only($item, ['_id'])
                            ),
                            'title' => $item['abstract'],
                            'published_at' => $item['pub_date'],
                            'url' => $item['web_url'],
                            'category' => $item['news_desk'],
                            'image_url' => $this->getArticleImageUrl($item),
                            'source' => $item['source'],
                        ];
                    }

                    return null;
                })
                ->filter(fn ($item) => $item != null);
        }

        return null;
    }

    public function getApiUrl(): string
    {
        return
            config('services.ny_times.api_url')
            .'?api-key='
            .config('services.ny_times.api_key');
    }

    public function getArticleImageUrl(array $item): ?string
    {
        $path = Arr::get($item, 'multimedia.0.url');

        return $path
            ? config('services.ny_times.url').'/'.$path
            : null;
    }
}
