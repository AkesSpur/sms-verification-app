<?php

namespace App\Console\Commands;

use App\Services\GetATextService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncGetATextServices extends Command
{
    protected $signature   = 'getatext:sync-services {--force : Force refresh even if cache is warm}';
    protected $description = 'Refresh the GetAText services/prices cache from the API';

    public function handle(GetATextService $service): int
    {
        $cacheKey = 'getatext-get-services';

        if ($this->option('force') || Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
            $this->line('Cache cleared.');
        }

        $this->info('Fetching services from GetAText API...');

        $services = $service->getServices();

        if (empty($services)) {
            $this->warn('No services returned from GetAText API. Cache not updated.');
            return Command::FAILURE;
        }

        $total    = count($services);
        $inStock  = count(array_filter($services, fn ($s) => $s['count'] > 0));
        $outStock = $total - $inStock;

        $this->info("Cached {$total} services ({$inStock} in stock, {$outStock} out of stock).");

        if ($this->getOutput()->isVerbose()) {
            $rows = array_map(fn ($s) => [
                $s['name'],
                $s['short_name'],
                '$' . number_format($s['cost'], 4),
                $s['count'] > 0 ? $s['count'] : 'OUT',
            ], $services);

            $this->table(['Service', 'API Name', 'USD Price', 'Stock'], $rows);
        }

        return Command::SUCCESS;
    }
}
