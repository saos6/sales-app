<?php

namespace Database\Seeders;

use App\Models\Cust;
use App\Models\Emp;
use App\Models\PaymentH;
use App\Models\PaymentM;
use App\Models\SalesH;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PaymentHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $custs = Cust::all();
        $emps = Emp::all();
        $sales = SalesH::all();

        if ($custs->isEmpty()) {
            $this->call(CustSeeder::class);
            $custs = Cust::all();
        }

        if ($emps->isEmpty()) {
            $this->call(EmpSeeder::class);
            $emps = Emp::all();
        }

        if ($sales->isEmpty()) {
            $this->call(SalesHSeeder::class);
            $sales = SalesH::all();
        }

        for ($i = 0; $i < 50; $i++) {
            $cust = $custs->random();
            $emp = $emps->random();
            $sale = $sales->random();

            $total_amount = 0;

            $paymentH = PaymentH::create([
                'receipt_no' => 'PAY-' . str_pad($i + 1, 8, '0', STR_PAD_LEFT) . '-' . uniqid(),
                'receipt_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'cust_id' => $cust->id,
                'cust_name' => $cust->name,
                'emp_id' => $emp->id,
                'subject' => $faker->sentence(3),
                'total_amount' => 0, // Will be updated later
                'remarks' => 'Test remarks',
                'status' => $faker->randomElement(['draft', 'posted', 'canceled']),
            ]);

            $details = [];
            $lineCount = rand(1, 3);

            for ($j = 0; $j < $lineCount; $j++) {
                $apply_amount = $faker->numberBetween(1000, 50000);
                $total_amount += $apply_amount;

                $details[] = [
                    'payment_id' => $paymentH->id,
                    'line_no' => $j + 1,
                    'sales_id' => $sale->id,
                    'apply_amount' => $apply_amount,
                    'remarks' => $faker->optional()->realText(50),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            PaymentM::insert($details);

            $paymentH->update([
                'total_amount' => $total_amount,
            ]);
        }
    }
}