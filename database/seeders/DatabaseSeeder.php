<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countWarehouses = 10;
        $warehouses = Warehouse::factory()
            ->count($countWarehouses)
            ->create();

        Product::factory()
            ->count(20)
            ->create()
            ->each(function ($product) use ($warehouses, $countWarehouses) {
                $product->warehouses()->attach(
                    $warehouses->random(rand(1, $countWarehouses))
                        ->pluck('id')
                        ->toArray(),
                    [
                        'quantity' => rand(1, 100)
                    ]
                );
            });

        // User::factory(10)->create();
    }
}
