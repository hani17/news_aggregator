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
        $this->saveArticles(
            $data,
            $this->saveCategories($data),
            $this->saveSources($data)
        );
    }

    /**
     * @param Collection $data
     * @return Collection<Category>
     */
    public function saveCategories(Collection $data): Collection
    {
        $categoriesNames = $data->pluck('category')
            ->unique()
            ->filter(fn ($i) => $i != null)
            ->map(fn ($i) => ['name' => $i]);

        $categories = collect();

        if ($categoriesNames->isNotEmpty()) {
            Category::upsert($categoriesNames->toArray(), ['name']);
            $categories = Category::whereIn('name', $categoriesNames->pluck('name'))
                ->get();
        }

        return $categories;
    }

    /**
     * @param Collection $data
     * @return Collection<Source>
     */
    public function saveSources(Collection $data): Collection
    {
        $sourcesNames = $data->pluck('source')
            ->unique()
            ->filter(fn ($i) => $i != null)
            ->map(fn ($i) => ['name' => $i]);

        $sources = collect();

        if ($sourcesNames->isNotEmpty()) {
            Source::upsert($sourcesNames->toArray(), ['name']);
            $sources = Source::whereIn('name', $sourcesNames->pluck('name'))
                ->get();
        }

        return $sources;
    }

    /**
     * @param Collection $data
     * @param Collection<Category> $categories
     * @param Collection<Source> $sources
     */
    public function saveArticles(Collection $data, Collection $categories, Collection $sources): void
    {
        $articles = $data->map(function ($item) use ($categories, $sources) {
            $categoryName = Arr::get($item, 'category');
            $sourceName = Arr::get($item, 'source');

            $category = $categories->isNotEmpty() && $categoryName
                ? $categories->firstWhere('name', $categoryName)
                : null;

            $source = $sources->isNotEmpty() && $sourceName
                ? $sources->firstWhere('name', $sourceName)
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
