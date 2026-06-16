<?php

namespace Tests\Unit;

use App\Services\EnvWriter;
use PHPUnit\Framework\TestCase;

class EnvWriterTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = sys_get_temp_dir() . '/.env_test_' . uniqid();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
        parent::tearDown();
    }

    public function test_write_creates_env_file(): void
    {
        EnvWriter::write($this->path, "APP_NAME=Test\nAPP_ENV=production\n");

        $this->assertFileExists($this->path);
        $this->assertStringContainsString('APP_NAME=Test', file_get_contents($this->path));
    }

    public function test_set_updates_existing_key(): void
    {
        file_put_contents($this->path, "APP_NAME=Old\nAPP_ENV=local\n");

        EnvWriter::set($this->path, 'APP_NAME', 'New');

        $this->assertStringContainsString('APP_NAME=New', file_get_contents($this->path));
        $this->assertStringNotContainsString('APP_NAME=Old', file_get_contents($this->path));
    }

    public function test_set_appends_missing_key(): void
    {
        file_put_contents($this->path, "APP_ENV=local\n");

        EnvWriter::set($this->path, 'NEW_KEY', 'hello');

        $this->assertStringContainsString('NEW_KEY=hello', file_get_contents($this->path));
    }

    public function test_set_quotes_value_with_spaces(): void
    {
        file_put_contents($this->path, "APP_NAME=Old\n");

        EnvWriter::set($this->path, 'APP_NAME', 'My App Name');

        $this->assertStringContainsString('APP_NAME="My App Name"', file_get_contents($this->path));
    }

    public function test_set_escapes_special_chars_in_password(): void
    {
        file_put_contents($this->path, "DB_PASSWORD=\n");

        EnvWriter::set($this->path, 'DB_PASSWORD', 'p@$$w0rd#1');

        $content = file_get_contents($this->path);
        $this->assertStringContainsString('DB_PASSWORD="p@$$w0rd#1"', $content);
    }

    public function test_set_empty_value(): void
    {
        file_put_contents($this->path, "DRAVION_LICENSE_KEY=abc123\n");

        EnvWriter::set($this->path, 'DRAVION_LICENSE_KEY', '');

        $this->assertStringContainsString('DRAVION_LICENSE_KEY=', file_get_contents($this->path));
        $this->assertStringNotContainsString('abc123', file_get_contents($this->path));
    }
}
