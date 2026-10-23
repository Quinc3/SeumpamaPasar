<?php

namespace Database\Seeders;

use App\Enums\DigitalServiceType;
use App\Models\DigitalProduct;
use Illuminate\Database\Seeder;

class DigitalProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Token listrik prabayar — pilihan nominal
            ['type' => DigitalServiceType::ElectricityToken, 'code' => 'TOKEN-20K',  'name' => 'Token Listrik 20.000',  'nominal_value' => 20000.00,  'base_price' => 20500.00,  'provider' => 'PLN'],
            ['type' => DigitalServiceType::ElectricityToken, 'code' => 'TOKEN-50K',  'name' => 'Token Listrik 50.000',  'nominal_value' => 50000.00,  'base_price' => 51000.00,  'provider' => 'PLN'],
            ['type' => DigitalServiceType::ElectricityToken, 'code' => 'TOKEN-100K', 'name' => 'Token Listrik 100.000', 'nominal_value' => 100000.00, 'base_price' => 101500.00, 'provider' => 'PLN'],
            ['type' => DigitalServiceType::ElectricityToken, 'code' => 'TOKEN-200K', 'name' => 'Token Listrik 200.000', 'nominal_value' => 200000.00, 'base_price' => 202500.00, 'provider' => 'PLN'],

            // Pulsa — beberapa operator dan nominal
            ['type' => DigitalServiceType::Pulse, 'code' => 'PULSA-TSEL-10K', 'name' => 'Pulsa Telkomsel 10.000', 'nominal_value' => 10000.00, 'base_price' => 10500.00, 'provider' => 'Telkomsel'],
            ['type' => DigitalServiceType::Pulse, 'code' => 'PULSA-TSEL-25K', 'name' => 'Pulsa Telkomsel 25.000', 'nominal_value' => 25000.00, 'base_price' => 25500.00, 'provider' => 'Telkomsel'],
            ['type' => DigitalServiceType::Pulse, 'code' => 'PULSA-ISAT-10K', 'name' => 'Pulsa Indosat 10.000',    'nominal_value' => 10000.00, 'base_price' => 10300.00, 'provider' => 'Indosat'],
            ['type' => DigitalServiceType::Pulse, 'code' => 'PULSA-XL-25K',   'name' => 'Pulsa XL 25.000',       'nominal_value' => 25000.00, 'base_price' => 25500.00, 'provider' => 'XL'],

            // Paket data — beberapa operator
            ['type' => DigitalServiceType::DataPackage, 'code' => 'DATA-TSEL-3GB', 'name' => 'Telkomsel 3 GB / 30 hari', 'nominal_value' => 3.00, 'base_price' => 35000.00, 'provider' => 'Telkomsel'],
            ['type' => DigitalServiceType::DataPackage, 'code' => 'DATA-TSEL-10GB','name' => 'Telkomsel 10 GB / 30 hari','nominal_value' => 10.00,'base_price' => 85000.00, 'provider' => 'Telkomsel'],
            ['type' => DigitalServiceType::DataPackage, 'code' => 'DATA-ISAT-5GB', 'name' => 'Indosat 5 GB / 30 hari',  'nominal_value' => 5.00, 'base_price' => 45000.00, 'provider' => 'Indosat'],

            // Tagihan listrik pascabayar — entri produk digital hanya sebagai katalog;
            // nominal tagihan berasal dari dummy_bills saat transaksi.
            ['type' => DigitalServiceType::ElectricityBill, 'code' => 'BILL-PLN-PASCA', 'name' => 'Tagihan Listrik PLN Pascabayar', 'nominal_value' => null, 'base_price' => 0.00, 'provider' => 'PLN'],
        ];

        foreach ($products as $row) {
            DigitalProduct::updateOrCreate(
                ['code' => $row['code']],
                array_merge($row, [
                    'metadata' => null,
                    'is_active' => true,
                ])
            );
        }
    }
}