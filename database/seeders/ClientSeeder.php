<?php

namespace Database\Seeders;

use App\Enums\UserRole;

class ClientSeeder extends DemoRoleUsersSeeder
{
    protected function role(): string
    {
        return UserRole::CLIENT;
    }

    protected function prefix(): string
    {
        return 'client';
    }

    protected function accountCount(): int
    {
        return 100;
    }
}
