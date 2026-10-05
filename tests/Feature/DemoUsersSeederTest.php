<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoUsersSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_creates_many_accounts_for_every_role_and_is_repeatable(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users2', 135);
        $this->assertDatabaseCount('comptes', 135);
        $this->assertDatabaseHas('users2', [
            'email' => 'agent@djamonopay.test',
            'role' => UserRole::AGENT,
        ]);
        $this->assertSame(10, Users2::where('role', UserRole::AGENT)->count());
        $this->assertSame(25, Users2::where('role', UserRole::DISTRIBUTEUR)->count());
        $this->assertSame(100, Users2::where('role', UserRole::CLIENT)->count());

        $this->seed();

        $this->assertDatabaseCount('users2', 135);
        $this->assertDatabaseCount('comptes', 135);
    }
}
