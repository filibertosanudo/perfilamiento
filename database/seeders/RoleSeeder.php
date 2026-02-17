<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Administrator',
                'description' => 'System administrator with full access',
            ],
            [
                'name' => 'Advisor',
                'description' => 'Advisor who reviews and recommends based on test results',
            ],
            [
                'name' => 'User',
                'description' => 'Standard user who answers assigned tests',
            ],
        ]);
    }
}
