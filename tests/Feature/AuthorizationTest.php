<?php

namespace Tests\Feature;

use App\Enums\CheckoutStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\SalesChannel;
use App\Enums\SellerStatus;
use App\Enums\SellerType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $overrides = []): User
    {
        $user = User::factory()->create();
        if (! empty($overrides)) {
            $user->forceFill($overrides)->save();
        }
        return $user->fresh();
    }

    private function makeSeller(string $storeName): SellerProfile
    {
        $user = $this->makeUser();
        return SellerProfile::create([
            'user_id' => $user->id,
            'seller_type' => SellerType::Individual->value,
            'store_name' => $storeName,
            'description' => 'test',
            'status' => SellerStatus::Active->value,
        ]);
    }

    private function makeProduct(SellerProfile $seller, string $slug, SalesChannel $channel = SalesChannel::B2C, int $minQty = 1): Product
    {
        $cat = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test']);

        $product = Product::create([
            'seller_profile_id' => $seller->id,
            'category_id' => $cat->id,
            'name' => 'Produk ' . $slug,
            'slug' => $slug,
            'description' => 'desc',
            'price' => 10000,
            'stock' => 100,
            'sales_channel' => $channel->value,
            'min_purchase_qty' => $minQty,
        ]);

        $product->forceFill(['status' => ProductStatus::Active->value])->save();

        return $product->fresh();
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_buyer_cannot_access_admin(): void
    {
        $buyer = $this->makeUser(['role' => UserRole::User->value]);
        $this->actingAs($buyer)->get('/admin')->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->makeUser(['role' => UserRole::Admin->value]);
        $this->actingAs($admin)->get('/admin')->assertStatus(200);
    }

    public function test_seller_cannot_edit_other_seller_product(): void
    {
        $sellerA = $this->makeSeller('Toko A');
        $productA = $this->makeProduct($sellerA, 'produk-a');

        $sellerBUser = $this->makeUser();
        SellerProfile::create([
            'user_id' => $sellerBUser->id,
            'seller_type' => SellerType::Individual->value,
            'store_name' => 'Toko B',
            'description' => 'test',
            'status' => SellerStatus::Active->value,
        ]);

        $this->actingAs($sellerBUser)
            ->get("/toko/produk/{$productA->id}/edit")
            ->assertStatus(403);
    }

    public function test_buyer_cannot_view_other_buyer_order(): void
    {
        $buyerA = $this->makeUser();
        $buyerB = $this->makeUser();
        $seller = $this->makeSeller('Toko C');

        $checkout = Checkout::create([
            'user_id' => $buyerA->id,
            'reference' => 'CHK-TEST-1',
            'total_amount' => 10000,
            'status' => CheckoutStatus::Paid->value,
        ]);

        $order = Order::create([
            'checkout_id' => $checkout->id,
            'seller_profile_id' => $seller->id,
            'buyer_id' => $buyerA->id,
            'subtotal' => 10000,
            'shipping_recipient_name' => 'A',
            'shipping_phone' => '081',
            'shipping_line1' => 'Jl',
            'shipping_city' => 'C',
            'shipping_province' => 'P',
            'shipping_postal_code' => '12345',
        ]);
        $order->forceFill(['status' => OrderStatus::PendingProcessing->value])->save();

        $this->actingAs($buyerB)
            ->get("/akun/pesanan/{$order->id}")
            ->assertStatus(403);
    }

    public function test_b2b_product_hidden_from_personal_buyer(): void
    {
        $buyer = $this->makeUser();
        $seller = $this->makeSeller('Grosir');
        $product = $this->makeProduct($seller, 'b2b-hidden', SalesChannel::B2B, 10);

        $this->actingAs($buyer)
            ->get("/produk/{$product->slug}")
            ->assertStatus(404);
    }

    public function test_b2c_product_visible_to_personal_buyer(): void
    {
        $buyer = $this->makeUser();
        $seller = $this->makeSeller('Retail');
        $product = $this->makeProduct($seller, 'b2c-visible');

        $this->actingAs($buyer)
            ->get("/produk/{$product->slug}")
            ->assertStatus(200);
    }
}