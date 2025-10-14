<?php

namespace App\Console\Commands;

use App\Http\Controllers\Backend\DaisyServiceController;
use App\Services\DaisySmsService;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SyncDaisyPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daisy:sync-prices 
                            {--show-summary : Show detailed sync summary}
                            {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk sync Daisy SMS service prices from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $this->info('Starting Daisy SMS service prices bulk sync...');
        
        try {
            // Create instances of required services
            $daisySmsService = app(DaisySmsService::class);
            $exchangeRateService = app(ExchangeRateService::class);
            
            // Create an instance of the controller with proper dependencies
            $controller = new DaisyServiceController($daisySmsService, $exchangeRateService);
            
            // Create a mock request object (since the controller method expects a request)
            $request = new Request();
            
            // Call the bulkSyncPrices method
            $response = $controller->bulkSyncPrices();
            
            // Get the response data
            $responseData = $response->getData(true);
            
            if ($responseData['success']) {
                // $this->info('✅ ' . $responseData['message']);
                
                // Show summary
                $summary = $responseData['summary'];
                // $this->line("Total services processed: {$summary['total_services']}");
                // $this->line("Successfully synced: {$summary['synced_count']}");
                // $this->line("Skipped: {$summary['skipped_count']}");
                // $this->line("Errors: {$summary['error_count']}");
                // $this->line("API calls made: {$summary['api_calls_made']}");
                
                // Show detailed results if requested
                if ($this->option('show-summary') && isset($responseData['results'])) {
                    // $this->line('');
                    // $this->line('Detailed Results:');
                    // $this->line('================');
                    
                    foreach ($responseData['results'] as $result) {
                        $status = strtoupper($result['status']);
                        $serviceName = $result['service_name'] ?? $result['service_code'];
                        
                        switch ($result['status']) {
                            case 'synced':
                                // $this->line("✅ [{$status}] {$serviceName}: {$result['message']}");
                                break;
                            case 'skipped':
                                // $this->line("⏭️  [{$status}] {$serviceName}: {$result['reason']}");
                                break;
                            case 'error':
                                // $this->line("❌ [{$status}] {$serviceName}: {$result['reason']}");
                                break;
                        }
                    }
                }
                
            } else {
                // $this->error('❌ ' . $responseData['message']);
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            // $this->error('❌ Command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}