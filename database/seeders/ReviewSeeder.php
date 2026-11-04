<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ProductStatus;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewSeeder extends Seeder
{
    private array $comments5 = [
        'Barang bagus, sesuai deskripsi. Pengiriman cepat, packing rapi.',
        'Kualitas mantap! Recommended buat yang cari produk serupa.',
        'Sesuai ekspektasi. Seller responsif, fast respon.',
        'Sangat memuaskan, produk original. Akan repeat order.',
        'Kualitas di atas harga. Worth it banget!',
        'Barang sampai dengan selamat, kondisi sempurna. Puas belanja di sini.',
        'Mantap! Kualitas premium, tidak mengecewakan.',
        'Suka banget sama produknya, fungsi sesuai iklan.',
        'Pengemasan aman, produk berfungsi normal. Terima kasih seller!',
        'Pengiriman cepat, produk sesuai foto. Recommended seller.',
    ];

    private array $comments4 = [
        'Barang bagus, cuma pengiriman agak lama. Overall OK.',
        'Produk sesuai, kualitas lumayan untuk harga segini.',
        'Sesuai gambar, fungsi normal. Packing bisa lebih rapi lagi.',
        'Cukup puas, ada sedikit minus tapi masih acceptable.',
        'Produk bagus, tapi warna agak beda dari foto.',
        'Sesuai harga, kualitas standar. Recommended.',
    ];

    private array $comments3 = [
        'Biasa aja, sesuai harga. Tidak ada yang spesial.',
        'Produk OK tapi pengiriman lama. Bintang 3 dulu.',
        'Fungsi normal, kualitas standar. Cukup buat pemakaian ringan.',
        'Lumayan, tapi ada bagian yang kurang rapi.',
    ];

    private array $comments2 = [
        'Kurang sesuai ekspektasi. Kualitas di bawah harga.',
        'Barang sampai tapi ada sedikit cacat. Kecewa.',
        'Packing kurang aman, produk lecet dikit.',
    ];

    private array $comments1 = [
        'Sangat kecewa. Produk tidak sesuai deskripsi.',
        'Rusak saat sampai. Tidak recommended.',
    ];

    private array $dummyReviewers = [
        ['name' => 'Budi Santoso',     'email' => 'reviewer1@seumpama.com'],
        ['name' => 'Siti Nurhaliza',   'email' => 'reviewer2@seumpama.com'],
        ['name' => 'Ahmad Wijaya',     'email' => 'reviewer3@seumpama.com'],
        ['name' => 'Dewi Lestari',     'email' => 'reviewer4@seumpama.com'],
        ['name' => 'Rudi Hartono',     'email' => 'reviewer5@seumpama.com'],
        ['name' => 'Maya Anggraini',   'email' => 'reviewer6@seumpama.com'],
        ['name' => 'Andi Pratama',     'email' => 'reviewer7@seumpama.com'],
        ['name' => 'Rina Susanti',     'email' => 'reviewer8@seumpama.com'],
    ];

    public function run(): void
    {
        $this->ensureReviewerPool();

        $reviewerPool = User::query()
            ->where('role', UserRole::User->value)
            ->whereDoesntHave('sellerProfile')
            ->get();

        if ($reviewerPool->count() < 3) {
            $this->command->error('Gagal menyiapkan reviewer pool.');
            return;
        }

        $products = Product::query()
            ->where('status', ProductStatus::Active->value)
            ->get();

        $seededCount = 0;
        $skippedCount = 0;

        foreach ($products as $product) {
            if (Review::where('product_id', $product->id)->exists()) {
                $skippedCount++;
                continue;
            }

            $maxReviewers = min(10, $reviewerPool->count());
            $reviewCount = rand(5, $maxReviewers);
            $reviewers = $reviewerPool->shuffle()->take($reviewCount);

            foreach ($reviewers as $reviewer) {
                $rating = $this->pickRating();

                DB::table('reviews')->insert([
                    'user_id' => $reviewer->id,
                    'product_id' => $product->id,
                    'order_id' => null,
                    'rating' => $rating,
                    'comment' => $this->pickComment($rating),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now(),
                ]);
            }

            $avg = (float) Review::where('product_id', $product->id)->avg('rating');
            $soldCount = max(
                (int) $product->sold_count,
                $reviewCount + rand(20, 200)
            );

            $product->forceFill([
                'rating' => round($avg, 2),
                'sold_count' => $soldCount,
            ])->save();

            $seededCount++;
        }

        $this->command->info("Review seeded untuk {$seededCount} produk. Dilewati (sudah ada review): {$skippedCount}.");
        $this->command->info("Total reviewer pool: {$reviewerPool->count()} user.");
    }

    private function ensureReviewerPool(): void
    {
        foreach ($this->dummyReviewers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::User->value,
                    'account_type' => AccountType::Personal->value,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Reviewer pool siap (8 buyer + buyer@seumpama.com).');
    }

    private function pickRating(): int
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 70 => 5,
            $rand <= 90 => 4,
            $rand <= 95 => 3,
            $rand <= 98 => 2,
            default => 1,
        };
    }

    private function pickComment(int $rating): ?string
    {
        $pool = match ($rating) {
            5 => $this->comments5,
            4 => $this->comments4,
            3 => $this->comments3,
            2 => $this->comments2,
            1 => $this->comments1,
            default => [],
        };

        if (empty($pool)) return null;
        if (rand(1, 100) <= 15) return null;

        return $pool[array_rand($pool)];
    }
}