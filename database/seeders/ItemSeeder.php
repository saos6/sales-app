<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::truncate();

        Item::create([
            'item_code' => 'BKE-001',
            'item_name' => 'スタンダードバイク',
            'spec' => '250cc',
            'category_id' => CategoryType::BIKE,
            'unit' => '台',
            'standard_price' => 650000,
            'cost_price' => 500000,
            'tax_rate' => 'standard',
            'currency' => 'JPY',
            'maker' => 'Sample Motors',
            'jan_code' => '4901234567890',
            'stock_qty' => 5,
            'reorder_point' => 2,
            'lead_time' => 14,
            'status' => 'active',
            'remarks' => '人気モデル',
        ]);

        Item::create([
            'item_code' => 'PRT-100',
            'item_name' => 'ブレーキパッド',
            'spec' => 'Front/Standard',
            'category_id' => CategoryType::PARTS,
            'unit' => '個',
            'standard_price' => 3500,
            'cost_price' => 2000,
            'tax_rate' => 'standard',
            'currency' => 'JPY',
            'maker' => 'Sample Parts Co.',
            'jan_code' => '4909876543210',
            'stock_qty' => 120,
            'reorder_point' => 50,
            'lead_time' => 7,
            'status' => 'active',
            'remarks' => '純正互換',
        ]);
    }
}


