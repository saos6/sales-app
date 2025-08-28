<?php

namespace Database\Seeders;

use App\Models\Cust;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cust::truncate();
        Cust::factory()->count(50)->create();
    }
}
