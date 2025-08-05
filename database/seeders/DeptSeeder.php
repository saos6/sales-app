<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('depts')->insert([
            ['name' => '本社', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '経営企画室', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '営業本部', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '開発本部', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '管理本部', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '営業第一部', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '営業第二部', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '開発第一部', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '人事部', 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '総務部', 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
