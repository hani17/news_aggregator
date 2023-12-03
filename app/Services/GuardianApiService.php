<?php

namespace App\Services;

use App\Enums\SourceName;
use App\Services\Interfaces\NewsServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService extends BaseApiService implements NewsServiceInterface
{
    /**
     * @var array<string>
     */
    protected array $apiArticleFields = [
        'webTitle',
        'webPublicationDate',
        'webUrl',
        'pillarName',
    ];

    public function fetchArticles(): ?Collection
    {
        try {
            $response = Http::get($this->getApiUrl())->throw();
            $results = $response->json('response.results');

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
                            'api_id' => $item['id'],
                            'hash' => $this->createHash(
                                Arr::only($item, ['id'])
                            ),
                            'title' => $item['webTitle'],
                            'published_at' => $item['webPublicationDate'],
                            'url' => $item['webUrl'],
                            'category' => $item['pillarName'],
                            'image_url' => $item['fields']['thumbnail'] ?? null,
                            'source' => SourceName::GUARDIAN->value,
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
            config('services.guardian.api_url')
            .'?api-key='
            .config('services.guardian.api_key')
            .'&show-fields=thumbnail';
    }
}
