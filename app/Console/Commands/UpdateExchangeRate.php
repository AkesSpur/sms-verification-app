<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class UpdateExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rate:update 
                            {--force : Force update even if recently updated}
                            {--show-current : Show current exchange rate from database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update USD to NGN exchange rate from external API';

    /**
     * Execute the console command.
     */
    public function handle(ExchangeRateService $exchangeRateService)
    {
        if ($this->option('show-current')) {
            $currentRate = $exchangeRateService->getCurrentDatabaseRate();
            
            if ($currentRate) {
                $this->info("Current exchange rate in database: 1 USD = {$currentRate} NGN");
            } else {
                $this->warn('No exchange rate found in database.');
            }
            
            return Command::SUCCESS;
        }

        $this->info('Fetching current USD to NGN exchange rate...');
        
        $result = $exchangeRateService->updateExchangeRate();
        
        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->line("Original rate: 1 USD = {$result['original_rate']} NGN");
            $this->line("Markup percentage: {$result['markup_percentage']}%");
            $this->line("Final rate: 1 USD = {$result['final_rate']} NGN");
        } else {
            $this->error('❌ ' . $result['message']);
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}