<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncOwletOrdersAlias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:owlet-orders {--limit=50 : Number of orders to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync social media order statuses with Owlet API (alias for owlet:sync-orders)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        // Call the actual sync command
        return Artisan::call('owlet:sync-orders', ['--limit' => $limit]);
    }
}