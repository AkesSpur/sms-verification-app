<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-expired {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process orders that have exceeded their SMS window and auto-cancel them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Processing expired orders...');
        
        // Find orders that need auto-cancellation
        $expiredOrders = Order::needsAutoCancellation()->get();
        
        if ($expiredOrders->isEmpty()) {
            $this->info('No orders found that need auto-cancellation.');
            return 0;
        }
        
        $this->info("Found {$expiredOrders->count()} orders that need auto-cancellation.");
        
        $processed = 0;
        $errors = 0;
        
        foreach ($expiredOrders as $order) {
            try {
                if ($dryRun) {
                    $this->line("[DRY RUN] Would cancel order #{$order->id} (User: {$order->user_id}, Service: {$order->service->name}, Phone: {$order->phone_number}) and refund ₦{$order->price}");
                } else {
                    // Cancel the order and refund users
                    $order->cancel(
                        'Auto-cancelled: SMS window expired (20 minutes) without receiving SMS',
                        'system'
                    );
                    
                    // Process refund using the stored order price
                    // $this->processRefund($order);
                    
                    $this->line("Cancelled order #{$order->id} (User: {$order->user_id}, Service: {$order->service->name}, Phone: {$order->phone_number}) and refunded ₦{$order->price}");
                    
                    Log::info("Auto-cancelled order #{$order->id} due to SMS window expiry with refund", [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'service_id' => $order->service_id,
                        'phone_number' => $order->phone_number,
                        'refund_amount' => $order->price,
                        'sms_window_expires_at' => $order->sms_window_expires_at,
                        'created_at' => $order->created_at
                    ]);
                }
                
                $processed++;
            } catch (\Exception $e) {
                $this->error("Failed to cancel order #{$order->id}: {$e->getMessage()}");
                
                Log::error("Failed to auto-cancel order #{$order->id}", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $errors++;
            }
        }
        
        if ($dryRun) {
            $this->info("[DRY RUN] Would process {$processed} orders.");
        } else {
            $this->info("Successfully processed {$processed} orders.");
            
            if ($errors > 0) {
                $this->warn("Encountered {$errors} errors during processing.");
            }
        }
    }

    /**
     * Process refund for a cancelled order
     *
     * @param \App\Models\Order $order
     * @return void
     */
    // private function processRefund($order)
    // {
    //     try {
    //         // Get the user
    //         $user = User::findOrFail($order->user_id);
            
    //         if (!$user) {
    //             Log::error("Cannot process refund for order #{$order->id}: User not found", [
    //                 'order_id' => $order->id,
    //                 'user_id' => $order->user_id
    //             ]);
    //             return;
    //         }

    //         // Add the order price back to user's balance
    //         $user->increment('balance', $order->final_price);

    //         // // Log the refund transaction
    //         // Transaction::create([
    //         //     'user_id' => $user->id,
    //         //     'type' => 'credit',
    //         //     'amount' => $order->final_price,
    //         //     'description' => "Refund for auto-cancelled order #{$order->id} - {$order->service->name}",
    //         //     'reference' => 'refund_' . $order->id . '_' . time(),
    //         //     'status' => 'completed'
    //         // ]);

    //         Transaction::createTransaction(
    //                  $user,
    //                  'credit',
    //                  'sms_refund',
    //                  $order->final_price,
    //                  "Refund for auto-cancelled order #{$order->id} - {$order->service->name}",
    //                  ['service_code' => $order->service->code, 'country_code' => $order->country->code],
    //                  $order
    //              );

    //         Log::info("Processed refund for order #{$order->id}", [
    //             'order_id' => $order->id,
    //             'user_id' => $user->id,
    //             'refund_amount' => $order->price,
    //             'new_balance' => $user->fresh()->balance
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error("Failed to process refund for order #{$order->id}", [
    //             'order_id' => $order->id,
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         throw $e;
    //     }
    // }
}
        
     