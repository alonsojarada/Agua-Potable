<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // 👈 Importante para encriptar la contraseña

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario Administrador
        User::create([
            'name' => 'Administrador General',
            'email' => 'admin@aguapotable.com',
            'password' => Hash::make('password123'),
            'rol' => 'admin',
        ]);

        // Usuario Operador de Campo
        User::create([
            'name' => 'Operador de Campo',
            'email' => 'operador@aguapotable.com',
            'password' => Hash::make('password123'),
            'rol' => 'operador',
        ]);
    }
}