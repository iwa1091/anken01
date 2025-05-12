<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run()
    {
        // 10件の配送先住所を生成
        Address::factory()->count(10)->create();
    }
}