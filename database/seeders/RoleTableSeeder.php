<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'client',
            'admin',
        ];

        foreach($roles as $role) {

            Role::firstOrCreate(
                [
                    'name' => $role
                ],
                [
                    'name'          => $role,
                    'guard_name'    => 'web',
                ]
            );
        }
    }
}
