<?php

namespace App\Jobs;

use App\Services\AggregatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AggregateArticles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            /** @var AggregatorService $aggregator */
            $aggregator = app()->make(AggregatorService::class);
            $aggregator->fetchAndSaveArticles();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
