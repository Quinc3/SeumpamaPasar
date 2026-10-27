<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ProductStatus;
use App\Enums\SalesChannel;
use App\Enums\SellerStatus;
use App\Enums\SellerType;
use App\Enums\UserRole;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\WholesalePriceTier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $individualSeller = SellerProfile::where('seller_type', SellerType::Individual)
            ->where('status', SellerStatus::Active)->first();

        if ($individualSeller) {
            $this->seedB2C($individualSeller);
        }

        $businessSeller = $this->ensureBusinessSeller();
        $this->seedB2B($businessSeller);
    }

    private function seedB2C(SellerProfile $seller): void
    {
        $products = [
            ['Elektronik', 'Headphone Wireless', 450000, 25, 'Headphone bluetooth dengan noise cancelling dan baterai 30 jam.'],
            ['Elektronik', 'Power Bank 10.000mAh', 180000, 40, 'Power bank tipis dengan fast charging 20W.'],
            ['Fashion', 'Kaos Katun Premium', 85000, 100, 'Kaos katun combed 30s, nyaman untuk harian.'],
            ['Fashion', 'Tote Bag Kanvas', 120000, 60, 'Tas kanvas kokoh, cocok untuk kuliah dan kerja.'],
            ['Rumah Tangga', 'Lampu Meja LED', 95000, 30, 'Lampu meja dengan 3 mode pencahayaan dan USB charging.'],
            ['Buku & Alat Tulis', 'Notebook A5 Hardcover', 55000, 80, 'Notebook 200 halaman dengan hardcover tahan lama.'],
        ];

        foreach ($products as [$catName, $name, $price, $stock, $desc]) {
            $category = Category::firstOrCreate(['slug' => Str::slug($catName)], ['name' => $catName]);
            Product::updateOrCreate(['slug' => Str::slug($name)], [
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'stock' => $stock,
                'sales_channel' => SalesChannel::B2C->value,
                'min_purchase_qty' => 1,
                'status' => ProductStatus::Active->value,
            ]);
        }
    }

    private function ensureBusinessSeller(): SellerProfile
    {
        $seller = SellerProfile::where('seller_type', SellerType::Business)
            ->where('status', SellerStatus::Active)->first();

        if ($seller) {
            return $seller;
        }

        $user = User::updateOrCreate(
            ['email' => 'business@example.test'],
            [
                'name' => 'Bisnis Demo',
                'password' => Hash::make('password'),
                'role' => UserRole::User->value,
                'account_type' => AccountType::Business->value,
                'email_verified_at' => now(),
            ]
        );

        $adminId = User::where('role', UserRole::Admin->value)->value('id');

        BusinessProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => 'PT Demo Sejahtera',
                'tax_id' => '01.234.567.8-901.000',
                'phone' => '021-1234567',
                'address' => 'Jl. Demo No. 1, Jakarta',
                'verified_at' => now(),
                'verified_by' => $adminId,
            ]
        );

        return SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'seller_type' => SellerType::Business->value,
                'store_name' => 'Grosir Demo',
                'description' => 'Toko grosir demo untuk pengujian B2B.',
                'status' => SellerStatus::Active->value,
            ]
        );
    }

    private function seedB2B(SellerProfile $seller): void
    {
        $products = [
            ['Fashion', 'Kaos Polos Grosir', 75000, 500, 12, SalesChannel::Both,
                'Kaos polos katun combed 30s untuk kebutuhan grosir.',
                [[12, 70000], [24, 65000], [60, 60000]]],
            ['Fashion', 'Tote Bag Kanvas Grosir', 80000, 300, 24, SalesChannel::Both,
                'Tote bag kanvas tebal untuk reseller dan event.',
                [[24, 75000], [48, 70000]]],
            ['Rumah Tangga', 'Lampu LED Grosir', 25000, 1000, 50, SalesChannel::B2B,
                'Lampu LED 9W khusus pembelian grosir.',
                [[50, 22000], [200, 19000]]],
        ];

        foreach ($products as [$catName, $name, $price, $stock, $minQty, $channel, $desc, $tiers]) {
            $category = Category::firstOrCreate(['slug' => Str::slug($catName)], ['name' => $catName]);
            $product = Product::updateOrCreate(['slug' => Str::slug($name)], [
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'stock' => $stock,
                'sales_channel' => $channel->value,
                'min_purchase_qty' => $minQty,
                'status' => ProductStatus::Active->value,
            ]);

            foreach ($tiers as [$minQ, $tierPrice]) {
                WholesalePriceTier::updateOrCreate(
                    ['product_id' => $product->id, 'min_quantity' => $minQ],
                    ['price' => $tierPrice]
                );
            }
        }
    }
}