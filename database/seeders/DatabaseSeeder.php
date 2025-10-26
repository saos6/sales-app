<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            DeptSeeder::class,
            EmpSeeder::class,
            CustSeeder::class,
            ItemSeeder::class,
            EstimateHSeeder::class,
            EstimateMSeeder::class,
            SalesHSeeder::class,
            PaymentHSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}