<?php

namespace App\Console\Commands;

use App\Models\SocialMediaOrder;
use App\Services\OwletApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SyncMultipleOwletOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owlet:sync-multiple-orders 
                            {--batch-size=100 : Number of orders to process in each batch}
                            {--max-batches=10 : Maximum number of batches to process}
                            {--delay=1 : Delay in seconds between batches}
                            {--status=* : Filter by specific order statuses (processing, pending, etc.)}
                            {--created-after= : Only sync orders created after this date (Y-m-d format)}
                            {--dry-run : Show what would be synced without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync multiple social media order statuses with Owlet API in batches for better scalability';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = $this->option('batch-size');
        $maxBatches = $this->option('max-batches');
        $delay = $this->option('delay');
        $statuses = $this->option('status');
        $createdAfter = $this->option('created-after');
        $dryRun = $this->option('dry-run');

        $this->info('Starting multiple Owlet orders synchronization...');
        $this->info("Batch size: {$batchSize}, Max batches: {$maxBatches}, Delay: {$delay}s");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Build query for orders to sync
        $query = SocialMediaOrder::whereNotNull('external_order_id')
            ->whereNotIn('status', ['completed', 'cancelled', 'refunded']);

        // Apply status filter if provided
        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
            $this->info('Filtering by statuses: ' . implode(', ', $statuses));
        }

        // Apply date filter if provided
        if ($createdAfter) {
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $createdAfter);
                $query->where('created_at', '>=', $date);
                $this->info("Filtering orders created after: {$createdAfter}");
            } catch (\Exception $e) {
                $this->error('Invalid date format. Use Y-m-d format (e.g., 2024-01-01)');
                return 1;
            }
        }

        $totalOrders = $query->count();
        
        if ($totalOrders === 0) {
            $this->info('No orders to sync.');
            return 0;
        }

        $this->info("Found {$totalOrders} orders to sync.");
        
        $owletService = new OwletApiService();
        $totalSynced = 0;
        $totalErrors = 0;
        $batchCount = 0;
        
        $progressBar = $this->output->createProgressBar(min($totalOrders, $batchSize * $maxBatches));
        $progressBar->start();

        // Process orders in batches
        $query->orderBy('updated_at', 'asc')
            ->chunk($batchSize, function (Collection $orders) use (
                $owletService, 
                &$totalSynced, 
                &$totalErrors, 
                &$batchCount, 
                $maxBatches, 
                $delay, 
                $dryRun,
                $progressBar
            ) {
                $batchCount++;
                
                if ($batchCount > $maxBatches) {
                    $this->newLine();
                    $this->warn("Reached maximum batch limit ({$maxBatches}). Stopping.");
                    return false; // Stop chunking
                }

                $this->newLine();
                $this->info("Processing batch {$batchCount} ({$orders->count()} orders)...");

                $batchSynced = 0;
                $batchErrors = 0;

                foreach ($orders as $order) {
                    try {
                        if ($dryRun) {
                            $this->line("[DRY RUN] Would sync order #{$order->id} (External ID: {$order->external_order_id})");
                            $batchSynced++;
                        } else {
                            $statusResponse = $owletService->getOrderStatus($order->external_order_id);
                            
                            if ($statusResponse && isset($statusResponse['order'])) {
                                $oldStatus = $order->status;
                                $order->updateFromExternalApi($statusResponse);
                                
                                if ($oldStatus !== $order->status) {
                                    $this->line("  Order #{$order->id}: {$oldStatus} → {$order->status}");
                                } else {
                                    $this->line("  Order #{$order->id}: Status unchanged ({$order->status})");
                                }
                                
                                $batchSynced++;
                            } else {
                                $this->warn("  Failed to get status for order #{$order->id}");
                                $batchErrors++;
                            }
                        }
                        
                    } catch (\Exception $e) {
                        $this->error("  Error syncing order #{$order->id}: {$e->getMessage()}");
                        Log::error('Multiple Owlet sync error for order ' . $order->id, [
                            'error' => $e->getMessage(),
                            'external_order_id' => $order->external_order_id,
                            'batch' => $batchCount
                        ]);
                        $batchErrors++;
                    }
                    
                    $progressBar->advance();
                }

                $totalSynced += $batchSynced;
                $totalErrors += $batchErrors;

                $this->info("Batch {$batchCount} completed: {$batchSynced} synced, {$batchErrors} errors");

                // Add delay between batches to avoid overwhelming the API
                if ($delay > 0 && $batchCount < $maxBatches) {
                    $this->info("Waiting {$delay} seconds before next batch...");
                    sleep($delay);
                }

                return true; // Continue chunking
            });

        $progressBar->finish();
        $this->newLine(2);

        // Final summary
        $this->info('=== Synchronization Summary ===');
        $this->info("Total batches processed: {$batchCount}");
        $this->info("Total orders synced: {$totalSynced}");
        
        if ($totalErrors > 0) {
            $this->warn("Total errors: {$totalErrors}");
        }
        
        if ($dryRun) {
            $this->warn('This was a dry run - no actual changes were made.');
        }

        $this->info('Multiple order synchronization completed.');
        
        return 0;
    }
}