<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ExhibitionSeeder::class,
            AddressSeeder::class,
            LikeSeeder::class,
            PurchaseSeeder::class,
            CategorySeeder::class
        ]);
    }

}
