<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Users\Enums\UserEnum;
use App\SharedKernel\Enums\GenericStatusEnum;
use App\SharedKernel\Enums\RoleEnum;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "email"             => "admin@mail.com",
                "password"          => Hash::make("12341234"),
                "name"              => "admin",
                "email_verified_at" => Carbon::now(),
            ]
        ];

        foreach($users as $user) {

            $user = User::firstOrCreate(
                [
                    'email' => $user['email'],
                    "name"  => $user['name'],
                ],
                [
                    'email'                 => $user['email'],
                    "name"                  => $user['name'],
                    'password'              => $user['password'],
                    "email_verified_at"     => $user['email_verified_at'],
                ]
            );

            $user->assignRole('admin');
        }
    }
}
