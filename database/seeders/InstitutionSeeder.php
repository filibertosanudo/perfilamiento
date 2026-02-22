<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        Institution::insert([
            [
                'name'    => 'Universidad Tecnológica de Hermosillo',
                'type'    => 'universidad',
                'city'    => 'Hermosillo',
                'address' => 'Blvd. Universidad 3000',
                'phone'   => '6621234567',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'Preparatoria Regional Norte',
                'type'    => 'preparatoria',
                'city'    => 'Hermosillo',
                'address' => 'Av. Solidaridad 500',
                'phone'   => '6629876543',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'Empresa Industrial del Norte SA',
                'type'    => 'empresa',
                'city'    => 'Monterrey',
                'address' => 'Zona Industrial 100',
                'phone'   => '8181234567',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}