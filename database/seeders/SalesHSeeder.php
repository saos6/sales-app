<?php

namespace Database\Seeders;

namespace Database\Seeders;

use App\Models\Cust;
use App\Models\Emp;
use App\Models\SalesH;
use App\Models\SalesM;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SalesHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $custs = Cust::all();
        $emps = Emp::all();

        if ($custs->isEmpty()) {
            $this->call(CustSeeder::class);
            $custs = Cust::all();
        }

        if ($emps->isEmpty()) {
            $this->call(EmpSeeder::class);
            $emps = Emp::all();
        }

        for ($i = 0; $i < 50; $i++) {
            $cust = $custs->random();
            $emp = $emps->random();

            $subtotal = 0;
            $tax = 0;

            $salesH = SalesH::create([
                'sales_no' => 'SAL-' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'sales_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'posting_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'cust_id' => $cust->id,
                'cust_name' => $cust->name,
                'cust_department' => $cust->department,
                'cust_person' => $cust->person_in_charge,
                'emps_id' => $emp->id,
                'subject' => $faker->sentence(3),
                'subtotal' => 0, // Will be updated later
                'tax' => 0, // Will be updated later
                'total' => 0, // Will be updated later
                'payment_status' => $faker->randomElement(['未', '一部', '完了']),
                'payment_date' => $faker->optional()->date(),
                'receipt_no' => $faker->optional()->ean13(),
                'remarks' => $faker->optional()->realText(50),
                'status' => $faker->randomElement(['draft', 'posted', 'canceled']),
            ]);

            $details = [];
            $lineCount = rand(1, 5);

            for ($j = 0; $j < $lineCount; $j++) {
                $quantity = $faker->numberBetween(1, 100);
                $unit_price = $faker->numberBetween(100, 10000);
                $amount = $quantity * $unit_price;
                $tax_rate = 0.10;
                $tax_amount = $amount * $tax_rate;

                $subtotal += $amount;
                $tax += $tax_amount;

                $details[] = [
                    'sales_id' => $salesH->id,
                    'line_no' => $j + 1,
                    'item_name' => $faker->realText(20),
                    'quantity' => $quantity,
                    'unit' => '個',
                    'unit_price' => $unit_price,
                    'amount' => $amount,
                    'tax_rate' => $tax_rate,
                    'tax_amount' => $tax_amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            SalesM::insert($details);

            $salesH->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
            ]);
        }
    }
}
