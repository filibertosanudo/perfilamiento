<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;
use App\Models\Institution;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = Institution::all()->keyBy('name');
        $uth = $institutions['Universidad Tecnológica de Hermosillo'];
        $prep = $institutions['Preparatoria Regional Norte'];
        $empresa = $institutions['Empresa Industrial del Norte SA'];

        // Obtener orientadores y usuarios
        $carlosMartinez = User::where('email', 'carlos.martinez@uth.test')->first();
        $mariaRodriguez = User::where('email', 'maria.rodriguez@uth.test')->first();
        $luisGonzalez   = User::where('email', 'luis.gonzalez@prep.test')->first();
        $robertoHernandez = User::where('email', 'roberto.hernandez@empresa.test')->first();

        // GRUPOS - Universidad Tecnológica (2 orientadores, múltiples grupos)
        
        $grupoIng8A = Group::create([
            'institution_id' => $uth->id,
            'creator_id'     => $carlosMartinez->id,
            'name'           => 'Ingeniería 8A',
            'description'    => 'Grupo de octavo semestre turno matutino',
            'active'         => true,
            'created_at'     => now()->subMonths(4),
        ]);

        $grupoIng6B = Group::create([
            'institution_id' => $uth->id,
            'creator_id'     => $luisGonzalez->id,
            'name'           => 'Ingeniería 6B',
            'description'    => 'Grupo de sexto semestre turno vespertino',
            'active'         => true,
            'created_at'     => now()->subMonths(3),
        ]);

        $grupoAdmon4A = Group::create([
            'institution_id' => $uth->id,
            'creator_id'     => $mariaRodriguez->id,
            'name'           => 'Administración 4A',
            'description'    => 'Grupo de cuarto semestre',
            'active'         => true,
            'created_at'     => now()->subMonths(3),
        ]);

        $grupoAdmon2B = Group::create([
            'institution_id' => $uth->id,
            'creator_id'     => $mariaRodriguez->id,
            'name'           => 'Administración 2B',
            'description'    => 'Grupo de segundo semestre',
            'active'         => true,
            'created_at'     => now()->subMonths(2),
        ]);

        $grupoTallerLiderazgo = Group::create([
            'institution_id' => $uth->id,
            'creator_id'     => $carlosMartinez->id,
            'name'           => 'Taller de Liderazgo',
            'description'    => 'Taller extracurricular de desarrollo personal',
            'active'         => true,
            'created_at'     => now()->subMonths(1),
        ]);

        // GRUPOS - Preparatoria
        
        $grupo3A = Group::create([
            'institution_id' => $prep->id,
            'creator_id'     => $luisGonzalez->id,
            'name'           => '3° Semestre A',
            'description'    => 'Grupo de tercer semestre turno matutino',
            'active'         => true,
            'created_at'     => now()->subMonths(2),
        ]);

        // GRUPOS - Empresa
        
        $grupoCapacitacion = Group::create([
            'institution_id' => $empresa->id,
            'creator_id'     => $robertoHernandez->id,
            'name'           => 'Capacitación Operativa 2024',
            'description'    => 'Programa de capacitación para personal operativo',
            'active'         => true,
            'created_at'     => now()->subMonths(1),
        ]);

        // ASIGNAR USUARIOS A GRUPOS (tabla pivot group_user)
        
        // Usuarios UTH
        $anaGarcia = User::where('email', 'ana.garcia@uth.test')->first();
        $joseFernandez = User::where('email', 'jose.fernandez@uth.test')->first();
        $patriciaDiaz = User::where('email', 'patricia.diaz@uth.test')->first();
        $miguelVega = User::where('email', 'miguel.vega@uth.test')->first();
        $carmenOrtiz = User::where('email', 'carmen.ortiz@uth.test')->first();

        // Ana García: 2 grupos del MISMO orientador (Carlos)
        $grupoIng8A->users()->attach([
            $anaGarcia->id => ['joined_at' => now()->subDays(50)],
        ]);

        $grupoIng6B->users()->attach([
            $anaGarcia->id => ['joined_at' => now()->subDays(40)],
        ]);

        // José Fernández: 2 orientadores DIFERENTES (Carlos + María)
        $grupoIng8A->users()->attach([
            $joseFernandez->id => ['joined_at' => now()->subDays(48)],
        ]);

        $grupoAdmon4A->users()->attach([
            $joseFernandez->id => ['joined_at' => now()->subDays(35)],
        ]);

        // Patricia Díaz: 3 orientadores DIFERENTES (Carlos + María + Luis)
        $grupoIng6B->users()->attach([
            $patriciaDiaz->id => ['joined_at' => now()->subDays(45)],
        ]);

        $grupoTallerLiderazgo->users()->attach([
            $patriciaDiaz->id => ['joined_at' => now()->subDays(30)],
        ]);

        $grupoAdmon2B->users()->attach([
            $patriciaDiaz->id => ['joined_at' => now()->subDays(25)],
        ]);

        // Miguel Vega: Solo 1 grupo (María)
        $grupoAdmon4A->users()->attach([
            $miguelVega->id => ['joined_at' => now()->subDays(35)],
        ]);

        // Usuarios Preparatoria
        $lauraMendez = User::where('email', 'laura.mendez@prep.test')->first();
        $ricardoSilva = User::where('email', 'ricardo.silva@prep.test')->first();
        $danielaParedes = User::where('email', 'daniela.paredes@prep.test')->first();

        $grupo3A->users()->attach([
            $lauraMendez->id => ['joined_at' => now()->subDays(28)],
            $ricardoSilva->id => ['joined_at' => now()->subDays(25)],
            $danielaParedes->id => ['joined_at' => now()->subDays(22)],
        ]);

        // Usuarios Empresa
        $jorgeMoreno = User::where('email', 'jorge.moreno@empresa.test')->first();
        $sandraRuiz = User::where('email', 'sandra.ruiz@empresa.test')->first();

        $grupoCapacitacion->users()->attach([
            $jorgeMoreno->id => ['joined_at' => now()->subDays(15)],
            $sandraRuiz->id => ['joined_at' => now()->subDays(12)],
        ]);
    }
}