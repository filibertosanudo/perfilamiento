<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Area;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all()->keyBy('name');
        $ingenieria = $areas['Ingeniería'];
        $salud = $areas['Ciencias de la Salud'];
        $admon = $areas['Administración'];

        // Obtener orientadores
        $carlosMartinez = User::where('email', 'carlos.martinez@siesi.test')->first();
        $mariaLopez = User::where('email', 'maria.lopez@siesi.test')->first();
        $juanPerez = User::where('email', 'juan.perez@siesi.test')->first();

        // GRUPOS - Ingeniería
        
        $grupoIng8A = Group::create([
            'area_id'      => $ingenieria->id,
            'creator_id'   => $carlosMartinez->id,
            'name'         => 'Ingeniería 8A',
            'description'  => 'Grupo de octavo semestre turno matutino',
            'active'       => true,
            'created_at'   => now()->subMonths(4),
        ]);

        $grupoTaller = Group::create([
            'area_id'      => $ingenieria->id,
            'creator_id'   => $carlosMartinez->id,
            'name'         => 'Taller de Liderazgo',
            'description'  => 'Taller extracurricular',
            'active'       => true,
            'created_at'   => now()->subMonths(3),
        ]);

        // GRUPOS - Ciencias de la Salud
        
        $grupoSalud4A = Group::create([
            'area_id'      => $salud->id,
            'creator_id'   => $mariaLopez->id,
            'name'         => 'Enfermería 4A',
            'description'  => 'Grupo de cuarto semestre',
            'active'       => true,
            'created_at'   => now()->subMonths(3),
        ]);

        $grupoMedicina = Group::create([
            'area_id'      => $salud->id,
            'creator_id'   => $mariaLopez->id,
            'name'         => 'Medicina 6B',
            'description'  => 'Grupo de sexto semestre turno vespertino',
            'active'       => true,
            'created_at'   => now()->subMonths(2),
        ]);

        // GRUPOS - Administración
        $grupoAdmon2B = Group::create([
            'area_id'      => $admon->id,
            'creator_id'   => $juanPerez->id,
            'name'         => 'Administración 2B',
            'description'  => 'Grupo de segundo semestre',
            'active'       => true,
            'created_at'   => now()->subMonths(2),
        ]);


        // ASIGNAR USUARIOS A GRUPOS (tabla pivot group_user)
        
        // Usuarios Ingeniería
        $anaGarcia = User::where('email', 'ana.garcia@siesi.test')->first();
        $joseFernandez = User::where('email', 'jose.fernandez@siesi.test')->first();

        $grupoIng8A->users()->attach([
            $anaGarcia->id => ['joined_at' => now()->subDays(50)],
        ]);

        $grupoTaller->users()->attach([
            $anaGarcia->id => ['joined_at' => now()->subDays(40)],
        ]);

        $grupoIng8A->users()->attach([
            $joseFernandez->id => ['joined_at' => now()->subDays(48)],
        ]);


        // Usuarios Salud
        $patriciaDiaz = User::where('email', 'patricia.diaz@siesi.test')->first();
        $alejandroMendoza = User::where('email', 'alejandro.mendoza@siesi.test')->first();

        $grupoSalud4A->users()->attach([
            $patriciaDiaz->id => ['joined_at' => now()->subDays(45)],
        ]);

        $grupoMedicina->users()->attach([
            $patriciaDiaz->id => ['joined_at' => now()->subDays(30)],
        ]);

        $grupoSalud4A->users()->attach([
            $alejandroMendoza->id => ['joined_at' => now()->subDays(25)],
        ]);

        // Usuarios Administración
        $lauraRamirez = User::where('email', 'laura.ramirez@siesi.test')->first();
        $andresOrtiz = User::where('email', 'andres.ortiz@siesi.test')->first();

        $grupoAdmon2B->users()->attach([
            $lauraRamirez->id => ['joined_at' => now()->subDays(28)],
            $andresOrtiz->id => ['joined_at' => now()->subDays(25)],
        ]);
    }
}