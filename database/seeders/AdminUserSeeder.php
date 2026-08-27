<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = config('seed.admin.name');
        $email = config('seed.admin.email');
        $password = config('seed.admin.password');

        if (! is_string($name) || $name === '') {
            throw new LogicException('seed.admin.name must be a non-empty string.');
        }

        if (! is_string($email) || $email === '') {
            throw new LogicException('seed.admin.email must be a non-empty string.');
        }

        if (! is_string($password) || $password === '') {
            throw new LogicException('seed.admin.password must be a non-empty string.');
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([Role::Admin]);
    }
}
