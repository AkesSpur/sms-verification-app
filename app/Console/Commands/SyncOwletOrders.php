<?php

namespace App\Console\Commands;

use App\Models\SocialMediaOrder;
use App\Services\OwletApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncOwletOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owlet:sync-orders {--limit=50 : Number of orders to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync social media order statuses with Owlet API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('Starting Owlet orders synchronization...');
        
        // Get orders that have external order IDs and are not completed/cancelled
        $orders = SocialMediaOrder::whereNotNull('external_order_id')
            ->whereNotIn('status', ['completed', 'cancelled', 'refunded'])
            ->limit($limit)
            ->get();
            
        if ($orders->isEmpty()) {
            $this->info('No orders to sync.');
            return 0;
        }
        
        $owletService = new OwletApiService();
        $syncedCount = 0;
        $errorCount = 0;
        
        $this->info("Found {$orders->count()} orders to sync.");
        
        foreach ($orders as $order) {
            try {
                $this->line("Syncing order #{$order->id} (External ID: {$order->external_order_id})");
                
                $statusResponse = $owletService->getOrderStatus($order->external_order_id);
                
                if ($statusResponse && isset($statusResponse['status'])) {
                    $oldStatus = $order->status;
                    
                    // Add the external order ID to the response for updateFromExternalApi method
                    $statusResponse['order'] = $order->external_order_id;
                    
                    $order->updateFromExternalApi($statusResponse);
                    
                    if ($oldStatus !== $order->status) {
                        $this->info("  Status updated: {$oldStatus} → {$order->status}");
                    } else {
                        $this->line("  Status unchanged: {$order->status}");
                    }
                    
                    $syncedCount++;
                } else {
                    $this->warn("  Failed to get status for order #{$order->id}");
                    // Log::warning('Owlet API response missing status', [
                    //     'order_id' => $order->id,
                    //     'external_order_id' => $order->external_order_id,
                    //     'response' => $statusResponse
                    // ]);
                    $errorCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("  Error syncing order #{$order->id}: {$e->getMessage()}");
                // Log::error('Owlet sync error for order ' . $order->id, [
                //     'error' => $e->getMessage(),
                //     'external_order_id' => $order->external_order_id
                // ]);
                $errorCount++;
            }
        }
        
        $this->info("\nSynchronization completed:");
        $this->info("- Synced: {$syncedCount} orders");
        
        if ($errorCount > 0) {
            $this->warn("- Errors: {$errorCount} orders");
        }
        
        return 0;
    }
}