<?php

namespace Database\Seeders;

use App\Models\DummyBill;
use Illuminate\Database\Seeder;

class DummyBillSeeder extends Seeder
{
    public function run(): void
    {
        $bills = [
            ['meter_number' => '111122223333', 'customer_name' => 'Budi Santoso',   'period' => '2025-01', 'amount' => 187500.00],
            ['meter_number' => '111122223333', 'customer_name' => 'Budi Santoso',   'period' => '2025-02', 'amount' => 192300.00],
            ['meter_number' => '444455556666', 'customer_name' => 'Siti Nurhaliza', 'period' => '2025-01', 'amount' => 250750.00],
            ['meter_number' => '444455556666', 'customer_name' => 'Siti Nurhaliza', 'period' => '2025-02', 'amount' => 262100.00],
        ];

        foreach ($bills as $row) {
            DummyBill::updateOrCreate(
                ['meter_number' => $row['meter_number'], 'period' => $row['period']],
                ['customer_name' => $row['customer_name'], 'amount' => $row['amount']]
            );
        }
    }
}