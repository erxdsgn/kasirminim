<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['BRG001', '899123456001', 'Indomie Goreng', 3500],
            ['BRG002', '899123456002', 'Aqua 600ml', 4000],
            ['BRG003', '899123456003', 'Teh Botol Sosro', 5000],
            ['BRG004', '899123456004', 'Beras 5 Kg', 75000],
            ['BRG005', '899123456005', 'Minyak Goreng 1 Liter', 18000],
            ['BRG006', '899123456006', 'Gula Pasir 1 Kg', 17000],
        ];

        foreach ($products as [$code, $barcode, $name, $price]) {
            Product::updateOrCreate(
                ['code' => $code],
                ['barcode' => $barcode, 'name' => $name, 'price' => $price, 'stock' => 100]
            );
        }
    }
}
