<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::updateOrCreate(
            ['member_no' => 'MBR001'],
            ['name' => 'Budi Santoso', 'phone' => '081234567001', 'type' => 'member']
        );

        Customer::updateOrCreate(
            ['member_no' => 'VIP001'],
            ['name' => 'Siti Aminah', 'phone' => '081234567002', 'type' => 'vip']
        );
    }
}
