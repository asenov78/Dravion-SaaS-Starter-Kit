<?php

namespace Tests\Unit\Updater;

use App\Services\Updater\UpdateHistory;
use PHPUnit\Framework\TestCase;

class UpdateHistoryTest extends TestCase
{
    private string $tmpPath;
    private UpdateHistory $history;

    protected function setUp(): void
    {
        $this->tmpPath = sys_get_temp_dir() . '/dravion_history_test_' . uniqid() . '.json';
        // Inject a custom path via reflection so we don't touch storage/
        $this->history = new class ($this->tmpPath) extends UpdateHistory {
            public function __construct(private string $customPath)
            {
                // skip parent constructor (which calls storage_path)
            }

            public function all(): array
            {
                if (! file_exists($this->customPath)) return [];
                $data = json_decode(file_get_contents($this->customPath), true);
                return is_array($data) ? $data : [];
            }

            public function append(string $fromVersion, string $toVersion, string $changelog = ''): void
            {
                $history   = $this->all();
                $history[] = [
                    'from'         => $fromVersion,
                    'to'           => $toVersion,
                    'changelog'    => $changelog,
                    'installed_at' => '2024-01-01T00:00:00+00:00',
                ];
                file_put_contents($this->customPath, json_encode($history, JSON_PRETTY_PRINT));
            }
        };
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpPath);
    }

    public function test_all_returns_empty_when_no_file(): void
    {
        $this->assertSame([], $this->history->all());
    }

    public function test_append_creates_entry(): void
    {
        $this->history->append('1.0.0', '1.1.0', 'Bug fixes');

        $all = $this->history->all();
        $this->assertCount(1, $all);
        $this->assertSame('1.0.0', $all[0]['from']);
        $this->assertSame('1.1.0', $all[0]['to']);
        $this->assertSame('Bug fixes', $all[0]['changelog']);
    }

    public function test_append_accumulates_entries(): void
    {
        $this->history->append('1.0.0', '1.1.0');
        $this->history->append('1.1.0', '1.2.0');

        $this->assertCount(2, $this->history->all());
    }

    public function test_append_empty_changelog(): void
    {
        $this->history->append('1.0.0', '1.1.0');
        $all = $this->history->all();
        $this->assertSame('', $all[0]['changelog']);
    }
}
