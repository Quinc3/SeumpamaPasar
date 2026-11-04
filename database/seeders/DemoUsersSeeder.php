<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ProductStatus;
use App\Enums\SalesChannel;
use App\Enums\SellerStatus;
use App\Enums\SellerType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1 seller individu aktif + 1 produk
        $sellerUser = User::updateOrCreate(
            ['email' => 'seller@seumpama.com'],
            [
                'name' => 'Penjual Demo',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'account_type' => AccountType::Personal,
                'email_verified_at' => now(),
            ]
        );

        $sellerProfile = SellerProfile::updateOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'seller_type' => SellerType::Individual,
                'store_name' => 'Toko Demo',
                'description' => 'Toko penjual individu untuk keperluan demo dan pengujian.',
                'status' => SellerStatus::Active,
            ]
        );

        $category = Category::query()->firstOrFail();

        Product::updateOrCreate(
            ['slug' => 'produk-demo-1'],
            [
                'seller_profile_id' => $sellerProfile->id,
                'category_id' => $category->id,
                'name' => 'Produk Demo 1',
                'description' => 'Produk contoh untuk menguji tampilan dashboard penjual.',
                'price' => 50000.00,
                'stock' => 10,
                'sales_channel' => SalesChannel::B2C,
                'min_purchase_qty' => 1,
                'status' => ProductStatus::Active,
            ]
        );

        // 1 buyer personal (tanpa seller_profile)
        User::updateOrCreate(
            ['email' => 'buyer@seumpama.com'],
            [
                'name' => 'Pembeli Demo',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'account_type' => AccountType::Personal,
                'email_verified_at' => now(),
            ]
        );
    }
}