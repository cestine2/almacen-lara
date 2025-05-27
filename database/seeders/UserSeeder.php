<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'nombre' => 'Administrador Principal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'sucursal_id' => null,
                'estado' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'almacen@test.com'],
            [
                'nombre' => 'Usuario Almacen',
                'password' => Hash::make('password'),
                'estado' => true
            ]
        );
    }
}
