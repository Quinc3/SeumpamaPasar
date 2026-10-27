<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $seller = SellerProfile::query()->where('status', 'active')->first();
        if (! $seller) {
            $this->command->warn('Tidak ada seller aktif. Jalankan DemoUsersSeeder dulu.');
            return;
        }

        $products = [
            ['Elektronik', 'Headphone Wireless', 450000, 25, 'Headphone bluetooth dengan noise cancelling dan baterai 30 jam.'],
            ['Elektronik', 'Power Bank 10.000mAh', 180000, 40, 'Power bank tipis dengan fast charging 20W.'],
            ['Fashion', 'Kaos Katun Premium', 85000, 100, 'Kaos katun combed 30s, nyaman untuk harian.'],
            ['Fashion', 'Tote Bag Kanvas', 120000, 60, 'Tas kanvas kokoh, cocok untuk kuliah dan kerja.'],
            ['Rumah Tangga', 'Lampu Meja LED', 95000, 30, 'Lampu meja dengan 3 mode pencahayaan dan USB charging.'],
            ['Buku & Alat Tulis', 'Notebook A5 Hardcover', 55000, 80, 'Notebook 200 halaman dengan hardcover tahan lama.'],
        ];

        foreach ($products as [$catName, $name, $price, $stock, $desc]) {
            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug($catName)],
                ['name' => $catName]
            );

            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'seller_profile_id' => $seller->id,
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => $desc,
                    'price' => $price,
                    'stock' => $stock,
                    'sales_channel' => 'b2c',
                    'min_purchase_qty' => 1,
                    'status' => 'active',
                ]
            );
        }
    }
}