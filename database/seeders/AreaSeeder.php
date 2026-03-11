<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        Area::insert([
            [
                'name'    => 'Ingeniería',
                'type'    => 'universidad',
                'city'    => 'Hermosillo',
                'address' => 'Blvd. Universidad 3000, Edificio A',
                'phone'   => '6621234567',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'Ciencias de la Salud',
                'type'    => 'universidad',
                'city'    => 'Hermosillo',
                'address' => 'Blvd. Universidad 3000, Edificio B',
                'phone'   => '6629876543',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'Administración',
                'type'    => 'universidad',
                'city'    => 'Hermosillo',
                'address' => 'Blvd. Universidad 3000, Edificio C',
                'phone'   => '8181234567',
                'active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
