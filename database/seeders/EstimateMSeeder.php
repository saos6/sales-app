<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstimateM;
use App\Models\EstimateH; // For foreign key

class EstimateMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are estimate headers to link to
        if (EstimateH::count() === 0) {
            $this->call(EstimateHSeeder::class);
        }

        $estimates = EstimateH::all();

        foreach ($estimates as $estimate) {
            $numberOfLines = rand(2, 5); // Each estimate will have 2 to 5 detail lines
            for ($i = 1; $i <= $numberOfLines; $i++) {
                EstimateM::create([
                    'estimate_id' => $estimate->id,
                    'line_no' => $i,
                    'item_code' => 'ITEM-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'item_name' => '商品名 ' . rand(1, 100),
                    'spec' => '仕様 ' . rand(1, 10),
                    'quantity' => rand(1, 10),
                    'unit' => ['個', '式', '時間'][array_rand(['個', '式', '時間'])],
                    'unit_price' => rand(1000, 50000),
                    'amount' => rand(1, 10) * rand(1000, 50000), // This will be recalculated in controller
                    'tax_rate' => ['標準', '軽減', '非課税'][array_rand(['標準', '軽減', '非課税'])],
                    'remarks' => '明細備考 ' . $i,
                    'deleted' => false,
                ]);
            }
        }
    }
}