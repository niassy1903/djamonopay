<?php

namespace Database\Seeders;

use App\Enums\UserRole;

class AgentSeeder extends DemoRoleUsersSeeder
{
    protected function role(): string
    {
        return UserRole::AGENT;
    }

    protected function prefix(): string
    {
        return 'agent';
    }

    protected function accountCount(): int
    {
        return 10;
    }

    protected function accountEmail(int $index, string $suffix): string
    {
        return $index === 1 ? 'agent@djamonopay.test' : parent::accountEmail($index, $suffix);
    }
}
