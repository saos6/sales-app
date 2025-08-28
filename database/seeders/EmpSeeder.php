<?php

namespace Database\Seeders;

use App\Models\Emp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Emp::truncate();
        Emp::factory()->count(10)->create();
    }
}
