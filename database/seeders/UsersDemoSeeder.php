<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'name' => 'Administrador CRM',
                'email' => 'admin@crm.com',
                'role' => RoleEnum::ADMIN,
                'state' => StateEnum::ACTIVE,
            ],
            [
                'name' => 'Operador CRM',
                'email' => 'operador@crm.com',
                'role' => RoleEnum::OPERATOR,
                'state' => StateEnum::ACTIVE,
            ],
            [
                'name' => 'Comercial CRM',
                'email' => 'comercial@crm.com',
                'role' => RoleEnum::SALES,
                'state' => StateEnum::ACTIVE,
            ],
            [
                'name' => 'Soporte CRM',
                'email' => 'soporte@crm.com',
                'role' => RoleEnum::SUPPORT,
                'state' => StateEnum::ACTIVE,
            ],
            [
                'name' => 'Administrativo CRM',
                'email' => 'administrativo@crm.com',
                'role' => RoleEnum::ADMINISTRATIVE,
                'state' => StateEnum::ACTIVE,
            ],
            [
                'name' => 'Cliente Demo CRM',
                'email' => 'cliente.demo@crm.com',
                'role' => RoleEnum::CLIENT,
                'state' => StateEnum::ACTIVE,
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => $password,
                    'role' => $user['role'],
                    'state' => $user['state'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
