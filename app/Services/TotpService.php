<?php

namespace App\Services;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new 16-character Base32 secret key.
     */
    public function generateSecret(int $length = 16): string
    {
        $secret = '';
        $randomBytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[ord($randomBytes[$i]) & 31];
        }

        return $secret;
    }

    /**
     * Calculate the 6-digit TOTP code for a given timestamp.
     */
    public function getCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = (int) floor($timestamp / 30);

        // Pack counter into 8-byte big-endian binary string
        $binaryCounter = pack('N*', 0) . pack('N*', $timeSlice);

        // Decode Base32 secret
        $binarySecret = $this->base32Decode($secret);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $binaryCounter, $binarySecret, true);

        // Dynamic truncation
        $offset = ord(substr($hash, -1)) & 0x0F;
        $unpacked = unpack('N', substr($hash, $offset, 4));
        $truncatedHash = $unpacked[1] & 0x7FFFFFFF;

        $code = (string) ($truncatedHash % 1000000);

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a submitted 6-digit code against the secret key.
     * Checks current window, previous 30s, and next 30s.
     */
    public function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $checkTime = $currentTime + ($i * 30);
            if (hash_equals($this->getCode($secret, $checkTime), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate otpauth:// URL for authenticator apps.
     */
    public function getQrCodeUri(string $company, string $holder, string $secret): string
    {
        $label = rawurlencode($company) . ':' . rawurlencode($holder);
        return "otpauth://totp/{$label}?secret={$secret}&issuer=" . rawurlencode($company);
    }

    /**
     * Generate 8 cryptographically secure emergency recovery codes.
     * Each code is 16 characters (4 blocks of 4 alphanumeric characters).
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $raw = strtoupper(bin2hex(random_bytes(8))); // 16 hexadecimal chars
            $codes[] = substr($raw, 0, 4) . '-' . substr($raw, 4, 4) . '-' . substr($raw, 8, 4) . '-' . substr($raw, 12, 4);
        }

        return $codes;
    }

    /**
     * Hash a recovery code using HMAC-SHA256 keyed with the application secret key.
     */
    public function hashRecoveryCode(string $code): string
    {
        $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
        $appKey = (string) config('app.key');

        return hash_hmac('sha256', $normalized, $appKey);
    }

    /**
     * Verify a recovery code against stored HMAC hashes in constant time.
     * Returns matching index or false.
     */
    public function verifyRecoveryCode(string $code, array $storedHashes): int|false
    {
        $targetHash = $this->hashRecoveryCode($code);

        foreach ($storedHashes as $index => $storedHash) {
            if (hash_equals($storedHash, $targetHash)) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Decode a Base32 string to raw binary.
     */
    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper(trim($b32));
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            $val = strpos(self::BASE32_CHARS, $char);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $binary;
    }
}
