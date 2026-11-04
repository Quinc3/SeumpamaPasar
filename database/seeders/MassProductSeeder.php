<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Enums\SalesChannel;
use App\Enums\SellerStatus;
use App\Enums\SellerType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use App\Models\WholesalePriceTier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MassProductSeeder extends Seeder
{
    /**
     * Folder gambar (opsional): storage/app/public/products/{slug}.jpg
     * Kalau file ada, otomatis di-attach ke produk.
     */
    public function run(): void
    {
        $individualSeller = SellerProfile::where('seller_type', SellerType::Individual)
            ->where('status', SellerStatus::Active)->first();
        $businessSeller = SellerProfile::where('seller_type', SellerType::Business)
            ->where('status', SellerStatus::Active)->first();

        if (! $individualSeller || ! $businessSeller) {
            $this->command->warn('Seller tidak ditemukan. Jalankan CatalogDemoSeeder dulu.');
            return;
        }

        $this->seedB2C($individualSeller);
        $this->seedB2B($businessSeller);

        $this->command->info('Mass products seeded.');
    }

    private function seedB2C(SellerProfile $seller): void
    {
        // [kategori, nama, harga, stok, terjual, diskon%, featured, deskripsi]
        $products = [
            // === ELEKTRONIK ===
            ['Elektronik', 'Headphone Wireless Pro', 450000, 25, 89, 25, true, 'Headphone bluetooth noise cancelling, baterai 30 jam, driver 40mm.'],
            ['Elektronik', 'Power Bank 10.000mAh', 180000, 40, 156, 0, true, 'Power bank tipis, fast charging 20W, dual output USB-A + USB-C.'],
            ['Elektronik', 'Speaker Bluetooth Portabel', 320000, 30, 67, 15, false, 'Speaker bluetooth 10W, tahan air IPX6, baterai 12 jam.'],
            ['Elektronik', 'Mouse Wireless Silent', 85000, 100, 245, 0, false, 'Mouse wireless silent click, DPI adjustable 800-1600.'],
            ['Elektronik', 'Keyboard Mechanical TKL', 550000, 15, 42, 0, true, 'Keyboard mechanical tenkeyless, switch blue, RGB backlight.'],
            ['Elektronik', 'Webcam HD 1080p', 275000, 20, 31, 20, false, 'Webcam 1080p 30fps, built-in mic, autofocus.'],
            ['Elektronik', 'Kabel USB-C 2m Nylon', 35000, 200, 890, 0, false, 'Kabel USB-C to USB-C 2 meter, nylon braided, 100W PD.'],
            ['Elektronik', 'Charger GaN 65W', 245000, 35, 78, 0, false, 'Charger GaN 65W 3 port, ringkas, cocok untuk laptop & HP.'],
            ['Elektronik', 'TWS Earbuds Sport', 199000, 50, 134, 10, false, 'Earbuds TWS sport, IPX5, baterai 24 jam dengan case.'],
            ['Elektronik', 'Smart Watch Fitness', 425000, 22, 51, 0, false, 'Smartwatch dengan heart rate monitor, step counter, notifikasi.'],
            ['Elektronik', 'Lampu LED Strip RGB 5m', 95000, 60, 178, 0, false, 'LED strip RGB 5 meter, remote control, USB powered.'],
            ['Elektronik', 'Kipas Angin Mini USB', 65000, 80, 203, 0, false, 'Kipas mini USB, 3 kecepatan, portabel untuk meja.'],

            // === FASHION ===
            ['Fashion', 'Kaos Katun Premium', 85000, 100, 342, 0, true, 'Kaos katun combed 30s, unisex, warna solid.'],
            ['Fashion', 'Tote Bag Kanvas', 120000, 60, 78, 15, false, 'Tas kanvas kokoh, cocok untuk kuliah dan kerja.'],
            ['Fashion', 'Kemeja Flanel Lengan Panjang', 175000, 40, 92, 0, false, 'Kemeja flanel katun, motif kotak, unisex.'],
            ['Fashion', 'Celana Chino Slim Fit', 195000, 35, 67, 0, false, 'Celana chino slim fit stretch, warna netral.'],
            ['Fashion', 'Sneakers Casual Canvas', 285000, 25, 45, 10, false, 'Sneakers kanvas casual, sol karet anti-slip.'],
            ['Fashion', 'Jaket Denim Oversize', 325000, 20, 28, 0, true, 'Jaket denim oversize, unisex, vintage wash.'],
            ['Fashion', 'Topi Baseball Bordir', 75000, 50, 89, 0, false, 'Topi baseball dengan bordir logo, adjustable strap.'],
            ['Fashion', 'Kaos Kaki Sport 3 Pasang', 45000, 150, 312, 0, false, 'Kaos kaki sport breathable, isi 3 pasang.'],
            ['Fashion', 'Sabuk Kulit Asli', 155000, 30, 54, 0, false, 'Sabuk kulit asli, buckle stainless, cocok formal & casual.'],
            ['Fashion', 'Dompet Card Holder', 55000, 70, 123, 0, false, 'Dompet card holder tipis, kulit sintetis premium.'],

            // === RUMAH TANGGA ===
            ['Rumah Tangga', 'Lampu Meja LED Touch', 95000, 30, 45, 0, false, 'Lampu meja LED touch dimmer, 3 mode warna, USB charging.'],
            ['Rumah Tangga', 'Tempat Sampah Mini Sensor', 165000, 25, 34, 0, false, 'Tempat sampah sensor otomatis 12L, silent, USB charging.'],
            ['Rumah Tangga', 'Rak Bumbu Dapur 3 Tingkat', 85000, 40, 67, 0, false, 'Rak bumbu dapur 3 tingkat, stainless steel anti karat.'],
            ['Rumah Tangga', 'Botol Minum Tumbler 500ml', 75000, 100, 289, 20, true, 'Tumbler stainless 500ml, tahan panas/dingin 12 jam.'],
            ['Rumah Tangga', 'Setrika Mini Portable', 145000, 20, 18, 0, false, 'Setrika mini portable, cocok untuk travel, dual voltage.'],
            ['Rumah Tangga', 'Sapu Set + Pengki', 65000, 50, 112, 0, false, 'Sapu dan pengki set, bulu lembut, gagang panjang.'],
            ['Rumah Tangga', 'Hanger Set 10 pcs', 35000, 200, 456, 0, false, 'Hanger plastik anti slip, isi 10 pcs, warna random.'],
            ['Rumah Tangga', 'Wadah Makanan Set 3', 85000, 45, 89, 0, false, 'Wadah makanan kaca borosilikat, isi 3 ukuran, microwave safe.'],

            // === BUKU & ALAT TULIS ===
            ['Buku & Alat Tulis', 'Notebook A5 Hardcover', 55000, 80, 220, 0, true, 'Notebook 200 halaman hardcover, kertas 80gsm.'],
            ['Buku & Alat Tulis', 'Pulpen Gel Set 5 Warna', 35000, 120, 267, 0, false, 'Pulpen gel 0.5mm, isi 5 warna, tinta halus.'],
            ['Buku & Alat Tulis', 'Sticky Notes Set', 25000, 150, 334, 0, false, 'Sticky notes warna-warni, isi 5 pack, 100 lembar/pack.'],
            ['Buku & Alat Tulis', 'Buku Sketsa A4', 65000, 40, 78, 0, false, 'Buku sketsa A4, kertas 120gsm, 50 lembar.'],
            ['Buku & Alat Tulis', 'Pensil Warna 24 Warna', 85000, 50, 145, 15, false, 'Pensil warna 24 warna, non-toxic, mudah di-sharp.'],

            // === KESEHATAN ===
            ['Kesehatan', 'Vitamin C 1000mg 30 Tablet', 65000, 80, 234, 0, false, 'Vitamin C 1000mg effervescent, isi 30 tablet, rasa jeruk.'],
            ['Kesehatan', 'Hand Sanitizer 500ml', 45000, 100, 456, 0, false, 'Hand sanitizer 70% alkohol, 500ml, dengan pump.'],
            ['Kesehatan', 'Masker Medis 3ply 50 pcs', 35000, 200, 890, 10, false, 'Masker medis 3ply, isi 50 pcs, earloop.'],
            ['Kesehatan', 'Sabun Cair Antibacterial 1L', 55000, 60, 178, 0, false, 'Sabun cair antibakteri 1 liter, wangi lavender, refill.'],
            ['Kesehatan', 'Termometer Digital', 95000, 25, 67, 0, false, 'Termometer digital infrared, 1 detik, LCD display.'],
        ];

        foreach ($products as [$catName, $name, $price, $stock, $sold, $discountPct, $featured, $desc]) {
            $this->createProduct($seller, $catName, $name, $price, $stock, $sold, $discountPct, $featured, $desc, SalesChannel::B2C, 1, []);
        }
    }

    private function seedB2B(SellerProfile $seller): void
    {
        // [kategori, nama, harga, stok, minQty, channel, featured, deskripsi, tier [[qty, price], ...]]
        $products = [
            ['Fashion', 'Kaos Polos Grosir', 75000, 500, 12, SalesChannel::B2B, false,
                'Kaos polos katun combed 30s untuk kebutuhan grosir.',
                [[12, 70000], [24, 65000], [60, 60000]]],

            ['Fashion', 'Tote Bag Kanvas Grosir', 80000, 300, 24, SalesChannel::B2B, false,
                'Tote bag kanvas tebal untuk reseller dan event.',
                [[24, 75000], [48, 70000]]],

            ['Rumah Tangga', 'Lampu LED 9W Grosir', 25000, 1000, 50, SalesChannel::B2B, false,
                'Lampu LED 9W khusus pembelian grosir.',
                [[50, 22000], [200, 19000]]],

            ['Fashion', 'Kemeja Kerja Grosir', 145000, 200, 20, SalesChannel::B2B, false,
                'Kemeja kerja formal untuk kebutuhan perusahaan.',
                [[20, 135000], [50, 125000]]],

            ['Rumah Tangga', 'Tumbler Custom Grosir', 85000, 300, 30, SalesChannel::B2B, false,
                'Tumbler custom logo, minimal 30 pcs.',
                [[30, 75000], [100, 65000]]],

            ['Kesehatan', 'Masker Medis Grosir', 25000, 2000, 100, SalesChannel::B2B, false,
                'Masker medis 3ply untuk grosir, karton isi 2000 pcs.',
                [[100, 22000], [500, 19000]]],
        ];

        foreach ($products as [$catName, $name, $price, $stock, $minQty, $channel, $featured, $desc, $tiers]) {
            $this->createProduct($seller, $catName, $name, $price, $stock, 0, 0, $featured, $desc, $channel, $minQty, $tiers);
        }
    }

    private function createProduct(
        SellerProfile $seller,
        string $catName,
        string $name,
        float $price,
        int $stock,
        int $sold,
        int $discountPct,
        bool $featured,
        string $desc,
        SalesChannel $channel,
        int $minQty,
        array $tiers
    ): void {
        $category = Category::firstOrCreate(
            ['slug' => Str::slug($catName)],
            ['name' => $catName]
        );

        $slug = Str::slug($name);
        $discountPrice = $discountPct > 0 ? round($price * (1 - $discountPct / 100), 2) : null;

        $product = Product::updateOrCreate(
            ['slug' => $slug],
            [
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'stock' => $stock,
                'sales_channel' => $channel->value,
                'min_purchase_qty' => $minQty,
                'status' => ProductStatus::Active->value,
                'rating' => round(4 + (mt_rand(0, 100) / 100), 2),
                'sold_count' => $sold,
                'discount_price' => $discountPrice,
                'is_featured' => $featured,
            ]
        );

        // Attach gambar kalau file ada di storage/app/public/products/{slug}.{jpg,png,webp}
        $this->attachImageIfExists($product, $slug);

        // Tier harga B2B
        foreach ($tiers as [$minQ, $tierPrice]) {
            WholesalePriceTier::updateOrCreate(
                ['product_id' => $product->id, 'min_quantity' => $minQ],
                ['price' => $tierPrice]
            );
        }
    }

    private function attachImageIfExists(Product $product, string $slug): void
    {
        if ($product->images()->exists()) {
            return; // sudah ada gambar, skip
        }

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $path = "products/{$slug}.{$ext}";
            if (Storage::disk('public')->exists($path)) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => 0,
                ]);
                return;
            }
        }
    }
}