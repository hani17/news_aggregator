<?php

namespace Tests\Feature\Services;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\AggregatorService;
use App\Services\Interfaces\NewsServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AggregatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function getTestData(): Collection
    {
        return collect(
            json_decode(file_get_contents(base_path('tests/Fixtures/Helpers/articles.json')), true)
        )->map(function ($item) {
            return $item + ['hash' => sha1($item['api_id'])];
        });
    }

    public function test_fetchAndSaveArticles_calls_fetchArticles_and_saveData(): void
    {
        $mockedAggregatorService = $this->partialMock(AggregatorService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchArticles')->once()->andReturn(new Collection());
            $mock->shouldReceive('saveData')->once()->with(Collection::class);
        });

        $mockedAggregatorService->fetchAndSaveArticles();
    }

    public function test_fetchArticles_calls_FetchArticles_on_each_provider(): void
    {
        $newsServices = [
            Mockery::mock(NewsServiceInterface::class),
            Mockery::mock(NewsServiceInterface::class),
        ];

        $mockedAggregatorService = Mockery::mock(AggregatorService::class, $newsServices)
            ->makePartial();

        $mockedAggregatorService->shouldReceive('fetchArticles')
            ->once()
            ->passthru();

        foreach ($newsServices as $provider) {
            $provider->shouldReceive('fetchArticles')->once()->andReturn(new Collection());
        }

        $mockedAggregatorService->fetchArticles();
    }

    public function test_it_creates_categories_after_fetching_articles()
    {
        $data = $this->getTestData();
        $mockedNewsService = $this->createMock(NewsServiceInterface::class);
        $mockedNewsService->expects($this->once())
            ->method('fetchArticles')
            ->willReturn($data);

        $aggregatorService = new AggregatorService($mockedNewsService);
        $aggregatorService->fetchAndSaveArticles();

        $data->pluck('category')->each(
            fn ($source) => $this->assertDatabaseHas('categories', ['name' => $source])
        );
    }

    public function test_it_creates_source_after_fetching_articles()
    {
        $data = $this->getTestData();
        $mockedNewsService = $this->createMock(NewsServiceInterface::class);
        $mockedNewsService->expects($this->once())
            ->method('fetchArticles')
            ->willReturn($data);

        $aggregatorService = new AggregatorService($mockedNewsService);
        $aggregatorService->fetchAndSaveArticles();

        $data->pluck('source')->each(
            fn ($source) => $this->assertDatabaseHas('sources', ['name' => $source])
        );
    }

    public function test_it_creates_articles_after_fetching_articles()
    {
        $data = $this->getTestData();
        $mockedNewsService = $this->createMock(NewsServiceInterface::class);
        $mockedNewsService->expects($this->once())
            ->method('fetchArticles')
            ->willReturn($data);

        $aggregatorService = new AggregatorService($mockedNewsService);
        $aggregatorService->fetchAndSaveArticles();

        $data->each(function ($article) {
            $this->assertDatabaseHas('articles', [
                'hash' => $article['hash'],
                'title' => $article['title'],
                'api_id' => $article['api_id'],
                'url' => $article['url'],
            ]);
        });
    }
}
