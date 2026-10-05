<?php

namespace Database\Seeders;

use App\Enums\UserRole;

class DistributeurSeeder extends DemoRoleUsersSeeder
{
    protected function role(): string
    {
        return UserRole::DISTRIBUTEUR;
    }

    protected function prefix(): string
    {
        return 'distributeur';
    }

    protected function accountCount(): int
    {
        return 25;
    }
}
