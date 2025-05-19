<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el rol si no existe
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web', // importante para coincidir con el guard
        ]);

        // Crear el usuario
        $user = User::factory()->create([
            'name' => 'Franz Alanez',
            'email' => 'alanezflores@gmail.com',
        ]);

        // Asignar el rol
        $user->assignRole($role);
    }
}
