<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('emps')->insert([
            [
                'name' => '山田 太郎',
                'email' => 'taro.yamada@example.com',
                'birthday' => Carbon::create('1985', '04', '12'),
                'depts_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '鈴木 一郎',
                'email' => 'ichiro.suzuki@example.com',
                'birthday' => Carbon::create('1990', '08', '21'),
                'depts_id' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'hanako.sato@example.com',
                'birthday' => Carbon::create('1992', '11', '30'),
                'depts_id' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '田中 優子',
                'email' => 'yuko.tanaka@example.com',
                'birthday' => Carbon::create('1988', '02', '15'),
                'depts_id' => 9,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
