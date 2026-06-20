<?php

namespace App\Contracts;

interface LicenseServiceInterface
{
    /**
     * Activate a purchase code. Returns ['license_key' => '...'] or ['error' => '...'].
     */
    public function activate(string $purchaseCode, string $domain): array;

    /**
     * Whether the installation holds a valid license (reads local HMAC cache).
     * Fast — no network call. Use for UI display.
     */
    public function isValid(): bool;

    /**
     * Live ping the license server right now, update the cache, and return
     * whether the license is currently active. Use before any privileged action
     * (update install, re-license) to catch suspended/revoked keys.
     * Falls back to cached value only when the server is unreachable.
     */
    public function isValidLive(): bool;

    /**
     * Ping the license server and return the raw result array.
     * Always hits the network (unless key is DEV-* or empty).
     *
     * @return array{valid:bool,checked_at:int,message:?string,status:?string}
     */
    public function verifyNow(): array;

    /**
     * Read the raw signed cache. Returns null if missing or tampered.
     */
    public function readCachePublic(): ?array;

    /**
     * Write a signed cache entry (called by LicenseCheck middleware).
     */
    public function writeCache(array $data): void;
}
