<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialMediaCategory;
use App\Models\SocialMediaProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OwletServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Owlet Services seeding...');
        
        // Path to the JSON file
        $jsonPath = base_path('owlet_services_test_2025-07-16_14-34-06.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('Owlet services JSON file not found at: ' . $jsonPath);
            return;
        }
        
        // Read and decode JSON data
        $jsonContent = file_get_contents($jsonPath);
        $services = json_decode($jsonContent, true);
        
        if (!$services || !is_array($services)) {
            $this->command->error('Invalid JSON data in Owlet services file');
            return;
        }
        
        $this->command->info('Found ' . count($services) . ' services to process');
        
        // Extract unique categories
        $categories = collect($services)
            ->pluck('category')
            ->unique()
            ->filter()
            ->values();
        
        $this->command->info('Found ' . $categories->count() . ' unique categories');
        
        // Create categories
        $categoryMap = [];
        foreach ($categories as $categoryName) {
            $slug = Str::slug($categoryName);
            
            $category = SocialMediaCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $categoryName,
                    'description' => 'Auto-generated category from Owlet API: ' . $categoryName,
                    'status' => true,
                    'sort_order' => 0
                ]
            );
            
            $categoryMap[$categoryName] = $category->id;
            
            if ($category->wasRecentlyCreated) {
                $this->command->info('Created category: ' . $categoryName);
            }
        }
        
        // Create products
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($services as $service) {
            try {
                // Skip if required fields are missing
                if (empty($service['service']) || empty($service['name']) || empty($service['category'])) {
                    $skippedCount++;
                    continue;
                }
                
                $categoryId = $categoryMap[$service['category']] ?? null;
                
                if (!$categoryId) {
                    $this->command->warn('Category not found for service: ' . $service['name']);
                    $skippedCount++;
                    continue;
                }
                
                // Prepare product data
                $productData = [
                    'name' => $service['name'],
                    'slug' => Str::slug($service['name'] . '-' . $service['service']),
                    'description' => $this->generateDescription($service),
                    'price_per_1000' => (float) ($service['rate'] ?? 0),
                    'min_quantity' => (int) ($service['min'] ?? 1),
                    'max_quantity' => (int) ($service['max'] ?? 1000000),
                    'status' => true,
                    'sort_order' => 0,
                    'category_id' => $categoryId,
                    'external_service_id' => (int) $service['service']
                ];
                
                // Check if product already exists by external_service_id
                $existingProduct = SocialMediaProduct::where('external_service_id', $service['service'])->first();
                
                if ($existingProduct) {
                    // Update existing product
                    $existingProduct->update($productData);
                    $updatedCount++;
                } else {
                    // Create new product
                    SocialMediaProduct::create($productData);
                    $createdCount++;
                }
                
            } catch (\Exception $e) {
                Log::error('Error processing service: ' . $service['name'] ?? 'Unknown', [
                    'error' => $e->getMessage(),
                    'service' => $service
                ]);
                $skippedCount++;
            }
        }
        
        $this->command->info('Seeding completed!');
        $this->command->info('Categories created: ' . count($categoryMap));
        $this->command->info('Products created: ' . $createdCount);
        $this->command->info('Products updated: ' . $updatedCount);
        $this->command->info('Products skipped: ' . $skippedCount);
    }
    
    /**
     * Generate a description for the product based on service data
     */
    private function generateDescription(array $service): string
    {
        $description = [];
        
        if (!empty($service['type']) && $service['type'] !== 'Default') {
            $description[] = 'Type: ' . $service['type'];
        }
        
        if (isset($service['min']) && isset($service['max'])) {
            $description[] = 'Quantity: ' . number_format($service['min']) . ' - ' . number_format($service['max']);
        }
        
        if (isset($service['dripfeed']) && $service['dripfeed']) {
            $description[] = 'Supports dripfeed delivery';
        }
        
        if (isset($service['refill']) && $service['refill']) {
            $description[] = 'Refillable service';
        }
        
        if (isset($service['cancel']) && $service['cancel']) {
            $description[] = 'Cancellable';
        }
        
        $baseDescription = 'Social media boosting service from Owlet API.';
        
        if (!empty($description)) {
            return $baseDescription . ' ' . implode('. ', $description) . '.';
        }
        
        return $baseDescription;
    }
}