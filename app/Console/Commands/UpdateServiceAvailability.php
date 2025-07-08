<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use App\Models\Country;
use App\Services\SmsPoolService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateServiceAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:update-availability 
                            {--service= : Specific service code to update}
                            {--country= : Specific country code to update}
                            {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update service availability and pricing from SMS API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serviceCode = $this->option('service');
        $countryCode = $this->option('country');
        $dryRun = $this->option('dry-run');
        
        $this->info('Updating service availability...');
        
        try {
            $smsPoolService = new SmsPoolService();
            
            // Get services to update
            $services = $serviceCode 
                ? Service::where('code', $serviceCode)->get()
                : Service::active()->get();
            
            if ($services->isEmpty()) {
                $this->warn('No services found to update.');
                return 0;
            }
            
            // Get countries to update
            $countries = $countryCode 
                ? Country::where('code', $countryCode)->get()
                : Country::where('is_active', true)->get();
            
            if ($countries->isEmpty()) {
                $this->warn('No countries found to update.');
                return 0;
            }
            
            $this->info("Updating {$services->count()} services for {$countries->count()} countries...");
            
            $updated = 0;
            $errors = 0;
            
            foreach ($services as $service) {
                foreach ($countries as $country) {
                    try {
                        // Check if service is available in this country
                        $countryService = $service->countries()->where('country_id', $country->id)->first();
                        
                        if (!$countryService) {
                            continue; // Skip if service not configured for this country
                        }
                        
                        // Get availability and pricing from API
                        $availability = $smsPoolService->getNumbersStatus($service->code, $country->code);
                        $price = $smsPoolService->getPrice($service->code, $country->code);
                        
                        $availableNumbers = $availability['count'] ?? 0;
                        $isAvailable = $availableNumbers > 0;
                        
                        if ($dryRun) {
                            $this->line("[DRY RUN] {$service->name} ({$service->code}) in {$country->name} ({$country->code}): {$availableNumbers} numbers, Price: {$price}");
                        } else {
                            // Update availability
                            $service->updateAvailabilityForCountry(
                                $country->code, 
                                $availableNumbers, 
                                json_encode($availability)
                            );
                            
                            // Update pricing if available
                            if ($price) {
                                $markup = $service->markup_percentage ?? 20;
                                $finalPrice = $price * (1 + ($markup / 100));
                                
                                $service->countries()->updateExistingPivot($country->id, [
                                    'api_price' => $price,
                                    'markup_percentage' => $markup,
                                    'final_price' => $finalPrice,
                                    'last_price_update' => Carbon::now()
                                ]);
                            }
                            
                            $this->line("Updated {$service->name} in {$country->name}: {$availableNumbers} numbers");
                        }
                        
                        $updated++;
                        
                        // Small delay to avoid rate limiting
                        usleep(100000); // 0.1 second
                        
                    } catch (\Exception $e) {
                        $this->error("Failed to update {$service->name} in {$country->name}: {$e->getMessage()}");
                        
                        Log::error("Failed to update service availability", [
                            'service_code' => $service->code,
                            'country_code' => $country->code,
                            'error' => $e->getMessage()
                        ]);
                        
                        $errors++;
                    }
                }
            }
            
            if ($dryRun) {
                $this->info("[DRY RUN] Would update {$updated} service-country combinations.");
            } else {
                $this->info("Successfully updated {$updated} service-country combinations.");
                
                if ($errors > 0) {
                    $this->warn("Encountered {$errors} errors during update.");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to update service availability: {$e->getMessage()}");
            Log::error('Service availability update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        return 0;
    }
}