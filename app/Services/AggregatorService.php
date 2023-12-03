<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\Interfaces\NewsServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AggregatorService
{
    /**
     * @var array<NewsServiceInterface>
     */
    protected array $providers;

    public function __construct(NewsServiceInterface ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * @throws Exception
     */
    public function fetchAndSaveArticles(): void
    {
        $this->saveData($this->fetchArticles());
    }

    /**
     * @throws Exception
     */
    public function fetchArticles(): Collection
    {
        if (empty($this->providers)) {
            throw new \InvalidArgumentException('News Providers must be set before aggregating articles.');
        }

        $articles = collect();

        foreach ($this->providers as $provider) {
            $articles = $articles->merge($provider->fetchArticles());
        }

        return $articles;
    }

    public function saveData(Collection $data): void
    {
        $categoriesNames = $data->pluck('category')
            ->unique()
            ->filter(fn ($i) => $i != null)
            ->map(fn ($i) => ['name' => $i]);

        $sourcesNames = $data->pluck('source')
            ->unique()
            ->filter(fn ($i) => $i != null)
            ->map(fn ($i) => ['name' => $i]);

        if ($sourcesNames->isNotEmpty()) {
            Source::upsert($sourcesNames->toArray(), ['name']);
        }

        if ($categoriesNames->isNotEmpty()) {
            Category::upsert($categoriesNames->toArray(), ['name']);
        }

        $categories = Category::whereIn('name', $categoriesNames->pluck('name'))
            ->get();
        $sources = Source::whereIn('name', $sourcesNames->pluck('name'))
            ->get();

        $articles = $data->map(function ($item) use ($categories, $sources) {
            $category = $item['category'] ?
                $categories->firstWhere('name', $item['category'])
                : null;
            $source = $item['source'] ?
                $sources->firstWhere('name', $item['source'])
                : null;

            return array_merge(
                Arr::except($item, ['category', 'source']),
                [
                    'category_id' => $category?->id,
                    'source_id' => $source?->id,
                    'published_at' => Carbon::parse($item['published_at']),
                ]
            );
        });

        if ($articles->isNotEmpty()) {
            Article::upsert($articles->toArray(), ['hash']);
        }
    }
}
