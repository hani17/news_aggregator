<?php

namespace App\Console\Commands;

use App\Jobs\AggregateArticles;
use Illuminate\Console\Command;

class AggregateNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:aggregate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'aggregate news from (The Guardian, The New York Times, and NewsAPI ...etc )';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new AggregateArticles());
    }
}
