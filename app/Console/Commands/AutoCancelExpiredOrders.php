<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaisyOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AutoCancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-cancel 
                            {--dry-run : Run without making actual changes}
                            {--limit=100 : Maximum number of orders to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-cancel expired orders that are past their SMS window without making API requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');
        
        $this->info('Starting auto-cancellation of expired orders...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }
        
        $daisyResults = $this->processDaisyOrders($dryRun, $limit);
        $poolResults = $this->processPoolOrders($dryRun, $limit);
        
        $this->displayResults($daisyResults, $poolResults, $dryRun);
        
        return Command::SUCCESS;
    }
    
    /**
     * Process expired DaisyOrder records
     */
    private function processDaisyOrders($dryRun = false, $limit = 100)
    {
        $results = [
            'processed' => 0,
            'cancelled' => 0,
            'refunded' => 0,
            'errors' => 0,
            'total_refund_amount' => 0
        ];
        
        // Find DaisyOrders that are expired beyond 1-minute grace period
        $expiredOrders = DaisyOrder::where('expires_at', '<', Carbon::now()->subMinute())
            ->whereIn('status', [DaisyOrder::STATUS_PENDING, DaisyOrder::STATUS_ACTIVE])
            ->whereNull('sms_code')
            ->limit($limit)
            ->get();
            
        $this->info("Found {$expiredOrders->count()} expired Daisy orders to process");
        
        foreach ($expiredOrders as $order) {
            $results['processed']++;
            $requestId = 'AUTO_CANCEL_' . time() . '_' . $order->id;
            
            try {
                $minutesPastExpiry = Carbon::now()->diffInMinutes($order->expires_at);
                
                // Log::info('Auto-Cancel Command - Processing Expired DaisyOrder', [
                //     'request_id' => $requestId,
                //     'order_id' => $order->id,
                //     'rental_id' => $order->rental_id,
                //     'expired_at' => $order->expires_at,
                //     'current_time' => Carbon::now(),
                //     'minutes_past_expiry' => $minutesPastExpiry,
                //     'user_id' => $order->user_id,
                //     'dry_run' => $dryRun
                // ]);
                
                if (!$dryRun) {
                    DB::beginTransaction();
                    
                    try {
                        // Update order status to cancelled
                        $order->update([
                            'status' => DaisyOrder::STATUS_CANCELLED,
                            'cancelled_at' => Carbon::now()
                        ]);
                        
                        $results['cancelled']++;
                        
                        // Process full refund
                        $refundAmount = $order->price;
                        $user = User::findOrFail($order->user_id);
                        $originalBalance = $user->balance;
                        
                        // Refund using User model method
                        $transaction = $user->addBalance(
                            $refundAmount,
                            'sms_rental_expired_refund',
                            'Auto-cancel refund for expired SMS rental - ' . $order->service_name,
                            $order
                        );
                        
                        $results['refunded']++;
                        $results['total_refund_amount'] += $refundAmount;
                        
                        // Log::info('Auto-Cancel Command - DaisyOrder Refund Processed', [
                        //     'request_id' => $requestId,
                        //     'order_id' => $order->id,
                        //     'original_price' => $order->price,
                        //     'refund_amount' => $refundAmount,
                        //     'original_balance' => $originalBalance,
                        //     'new_balance' => $user->fresh()->balance,
                        //     'user_id' => $user->id,
                        //     'transaction_id' => $transaction->id
                        // ]);
                        
                        DB::commit();
                        
                        $this->line("✓ Cancelled DaisyOrder #{$order->id} - Refunded ₦" . number_format($refundAmount, 2));
                        
                    } catch (Exception $dbException) {
                        DB::rollback();
                        $results['errors']++;
                        
                        // Log::error('Auto-Cancel Command - DaisyOrder Database Error', [
                        //     'request_id' => $requestId,
                        //     'order_id' => $order->id,
                        //     'db_exception' => $dbException->getMessage(),
                        //     'user_id' => $order->user_id,
                        //     'stack_trace' => $dbException->getTraceAsString()
                        // ]);
                        
                        // Fallback to just marking as expired
                        $order->status = DaisyOrder::STATUS_EXPIRED;
                        $order->save();
                        
                        $this->error("✗ Failed to process DaisyOrder #{$order->id}: {$dbException->getMessage()}");
                    }
                } else {
                    $this->line("DRY RUN: Would cancel DaisyOrder #{$order->id} and refund ₦" . number_format($order->price, 2));
                }
                
            } catch (Exception $e) {
                $results['errors']++;
                
                // Log::error('Auto-Cancel Command - DaisyOrder Processing Error', [
                //     'request_id' => $requestId,
                //     'order_id' => $order->id,
                //     'exception' => $e->getMessage(),
                //     'user_id' => $order->user_id,
                //     'stack_trace' => $e->getTraceAsString()
                // ]);
                
                $this->error("✗ Error processing DaisyOrder #{$order->id}: {$e->getMessage()}");
            }
        }
        
        return $results;
    }
    
    /**
     * Process expired PoolOrder records
     */
    private function processPoolOrders($dryRun = false, $limit = 100)
    {
        $results = [
            'processed' => 0,
            'cancelled' => 0,
            'refunded' => 0,
            'errors' => 0,
            'total_refund_amount' => 0
        ];
        
        // Find PoolOrders that should be auto-cancelled (1 minute past SMS window expiration)
        $expiredOrders = Order::where('sms_window_expires_at', '<', Carbon::now()->subMinute())
            ->whereNull('sms_received_at')
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACTIVE])
            ->limit($limit)
            ->get();
            
        $this->info("Found {$expiredOrders->count()} expired Pool orders to process");
        
        foreach ($expiredOrders as $order) {
            $results['processed']++;
            $requestId = 'AUTO_CANCEL_POOL_' . time() . '_' . $order->id;
            
            try {
                // Log::info('Auto-Cancel Command - Processing Expired PoolOrder', [
                //     'request_id' => $requestId,
                //     'order_id' => $order->id,
                //     'activation_id' => $order->activation_id,
                //     'sms_window_expires_at' => $order->sms_window_expires_at,
                //     'current_time' => Carbon::now(),
                //     'user_id' => $order->user_id,
                //     'dry_run' => $dryRun
                // ]);
                
                if (!$dryRun) {
                    DB::beginTransaction();
                    
                    try {
                        // Update order status to cancelled
                        $order->update([
                            'status' => Order::STATUS_CANCELLED,
                            'cancelled_at' => Carbon::now(),
                            'cancellation_reason' => 'Auto-cancelled: SMS window expired (1+ minute past expiration)',
                            'can_cancel' => false
                        ]);
                        
                        $results['cancelled']++;
                        
                        // Process refund using the existing method
                        $order->processRefund('Auto-cancelled: SMS window expired', 'system');
                        
                        $results['refunded']++;
                        $results['total_refund_amount'] += $order->final_price;
                        
                        // Log status change
                        $order->logStatusChange(Order::STATUS_CANCELLED, 'Auto-cancelled: SMS window expired', 'system');
                        
                        DB::commit();
                        
                        // Log::info('Auto-Cancel Command - PoolOrder Cancelled Successfully', [
                        //     'request_id' => $requestId,
                        //     'order_id' => $order->id,
                        //     'refund_amount' => $order->final_price,
                        //     'user_id' => $order->user_id
                        // ]);
                        
                        $this->line("✓ Cancelled PoolOrder #{$order->id} - Refunded ₦" . number_format($order->final_price, 2));
                        
                    } catch (Exception $dbException) {
                        DB::rollback();
                        $results['errors']++;
                        
                        // Log::error('Auto-Cancel Command - PoolOrder Database Error', [
                        //     'request_id' => $requestId,
                        //     'order_id' => $order->id,
                        //     'db_exception' => $dbException->getMessage(),
                        //     'user_id' => $order->user_id,
                        //     'stack_trace' => $dbException->getTraceAsString()
                        // ]);
                        
                        $this->error("✗ Failed to process PoolOrder #{$order->id}: {$dbException->getMessage()}");
                    }
                } else {
                    $this->line("DRY RUN: Would cancel PoolOrder #{$order->id} and refund ₦" . number_format($order->final_price, 2));
                }
                
            } catch (Exception $e) {
                $results['errors']++;
                
                // Log::error('Auto-Cancel Command - PoolOrder Processing Error', [
                //     'request_id' => $requestId,
                //     'order_id' => $order->id,
                //     'exception' => $e->getMessage(),
                //     'user_id' => $order->user_id,
                //     'stack_trace' => $e->getTraceAsString()
                // ]);
                
                $this->error("✗ Error processing PoolOrder #{$order->id}: {$e->getMessage()}");
            }
        }
        
        return $results;
    }
    
    /**
     * Display the results summary
     */
    private function displayResults($daisyResults, $poolResults, $dryRun)
    {
        $this->newLine();
        $this->info('=== AUTO-CANCELLATION SUMMARY ===');
        
        if ($dryRun) {
            $this->warn('DRY RUN RESULTS (No actual changes made):');
        }
        
        $this->table(
            ['Order Type', 'Processed', 'Cancelled', 'Refunded', 'Errors', 'Total Refund Amount'],
            [
                [
                    'Daisy Orders',
                    $daisyResults['processed'],
                    $daisyResults['cancelled'],
                    $daisyResults['refunded'],
                    $daisyResults['errors'],
                    '₦' . number_format($daisyResults['total_refund_amount'], 2)
                ],
                [
                    'Pool Orders',
                    $poolResults['processed'],
                    $poolResults['cancelled'],
                    $poolResults['refunded'],
                    $poolResults['errors'],
                    '₦' . number_format($poolResults['total_refund_amount'], 2)
                ],
                [
                    'TOTAL',
                    $daisyResults['processed'] + $poolResults['processed'],
                    $daisyResults['cancelled'] + $poolResults['cancelled'],
                    $daisyResults['refunded'] + $poolResults['refunded'],
                    $daisyResults['errors'] + $poolResults['errors'],
                    '₦' . number_format($daisyResults['total_refund_amount'] + $poolResults['total_refund_amount'], 2)
                ]
            ]
        );
        
        if ($daisyResults['errors'] > 0 || $poolResults['errors'] > 0) {
            $this->warn('Some orders encountered errors. Check the logs for details.');
        }
        
        if (!$dryRun && ($daisyResults['cancelled'] > 0 || $poolResults['cancelled'] > 0)) {
            $this->info('Auto-cancellation completed successfully!');
        }
    }
}
