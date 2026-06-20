<?php

namespace Tests;

use App\Models\Setting;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        // Static cache must be flushed each test — RefreshDatabase rolls back
        // the DB but static properties survive across test methods.
        Setting::flushCache();
    }
}
