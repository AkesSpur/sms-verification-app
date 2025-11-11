<?php

namespace Database\Seeders;

use App\Models\ResellerProduct;
use App\Models\ResellerProductLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ResellerSeeder extends Seeder
{
    public function run(): void
    {
        $imagesDir = public_path('uploads/digital-products/products');
        $imageFiles = [];
        if (is_dir($imagesDir)) {
            foreach (File::files($imagesDir) as $f) {
                $relative = str_replace(public_path(), '', $f->getPathname());
                $imageFiles[] = $relative;
            }
        }

        $productCount = 12; // at least 10
        $logsPerProduct = 25; // 20+ logs each

        for ($i = 1; $i <= $productCount; $i++) {
            $name = "Reseller Product {$i}";
            $slug = Str::slug($name) . '-' . $i;

            $image = null;
            if (!empty($imageFiles)) {
                $image = $imageFiles[array_rand($imageFiles)];
                if (strpos($image, '/uploads/') === false) {
                    $image = '/uploads/digital-products/products/' . basename($image);
                }
            }

            $product = ResellerProduct::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $this->randomDescription(),
                'image' => $image,
                'price' => mt_rand(500, 5000),
                'stock' => 0,
                'status' => true,
                'sort_order' => $i,
            ]);

            for ($j = 1; $j <= $logsPerProduct; $j++) {
                ResellerProductLog::create([
                    'product_id' => $product->id,
                    'log_item' => $this->randomLogItem($product->name, $j),
                    'details' => $this->randomDetails(),
                    'status' => 'available',
                ]);
            }

            $product->updateStock();
        }
    }

    private function randomDescription(): string
    {
        $features = [
            'Instant delivery upon purchase',
            'Verified and tested content',
            'High-quality data for resellers',
            'Updated regularly',
            'Support available for issues',
        ];
        shuffle($features);
        return '<p>' . implode('</p><p>', array_slice($features, 0, 3)) . '</p>';
    }

    private function randomLogItem(string $productName, int $index): string
    {
        $sections = [
            "<strong>Item #{$index}</strong> for {$productName}",
            "<p>Credentials: <code>user{$index}@example.com : pass{$index}!</code></p>",
            "<ul><li>Region: US</li><li>Tier: Premium</li><li>Expires: 30 days</li></ul>",
            "<blockquote>Use responsibly, do not resell outside terms.</blockquote>",
            "<pre>KEY-{$index}-" . Str::upper(Str::random(12)) . "</pre>",
        ];
        shuffle($sections);
        return implode("\n", array_slice($sections, 0, 3));
    }

    private function randomDetails(): ?string
    {
        $details = [
            'Batch A',
            'Batch B',
            'Promo stock',
            'Limited time',
            null,
        ];
        return $details[array_rand($details)];
    }
}