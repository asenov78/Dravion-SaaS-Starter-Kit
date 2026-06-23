<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_secret_is_not_mass_assignable(): void
    {
        $user = User::factory()->create();

        $user->fill(['two_factor_secret' => 'evil-secret']);
        $user->save();

        $fresh = $user->fresh();
        $this->assertNull($fresh->two_factor_secret, '2FA secret must not be mass-assignable');
    }

    public function test_two_factor_confirmed_at_is_not_mass_assignable(): void
    {
        $user = User::factory()->create(['two_factor_confirmed_at' => null]);

        $user->fill(['two_factor_confirmed_at' => now()]);
        $user->save();

        $fresh = $user->fresh();
        $this->assertNull($fresh->two_factor_confirmed_at, '2FA confirmed_at must not be mass-assignable');
    }
}
