<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstimateH;
use App\Models\Cust; // Assuming Cust model exists for foreign key
use App\Models\Emp;  // Assuming Emp model exists for foreign key
use Illuminate\Support\Str;

class EstimateHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are customers and employees to link to
        if (Cust::count() === 0) {
            \App\Models\Cust::factory()->count(5)->create();
        }
        if (Emp::count() === 0) {
            \App\Models\Emp::factory()->count(5)->create();
        }

        $custs = Cust::all();
        $emps = Emp::all();

        for ($i = 1; $i <= 10; $i++) {
            $cust = $custs->random();
            $emp = $emps->random();

            EstimateH::create([
                'id' => (string) Str::uuid(),
                'estimate_no' => 'EST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'estimate_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'valid_until' => now()->addDays(rand(1, 60))->format('Y-m-d'),
                'cust_id' => $cust->id,
                'cust_name' => $cust->name,
                'cust_department' => $cust->department,
                'cust_person' => $cust->contact_person_name,
                'emps_id' => $emp->id,
                'subject' => '見積案件 ' . $i,
                'subtotal' => rand(10000, 100000),
                'tax' => rand(1000, 10000),
                'total' => rand(11000, 110000),
                'currency' => '円',
                'remarks' => '備考 ' . $i,
                'status' => ['作成中', '提出済', '受注'][array_rand(['作成中', '提出済', '受注'])],
                'deleted' => false,
            ]);
        }
    }
}