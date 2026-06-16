<?php

namespace App\Contracts;

interface LicenseServiceInterface
{
    /**
     * Activate a purchase code. Returns ['license_key' => '...'] or ['error' => '...'].
     */
    public function activate(string $purchaseCode, string $domain): array;

    /**
     * Whether the installation holds a valid license.
     */
    public function isValid(): bool;

    /**
     * Read the raw signed cache. Returns null if missing or tampered.
     */
    public function readCachePublic(): ?array;

    /**
     * Write a signed cache entry (called by LicenseCheck middleware).
     */
    public function writeCache(array $data): void;
}
