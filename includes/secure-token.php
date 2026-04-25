<?php
/**
 * Secure Token System — Encrypts/decrypts IDs for URLs
 * 
 * Instead of exposing raw database IDs in URLs (e.g., report.php?id=298),
 * this system creates opaque, tamper-proof tokens (e.g., report.php?ref=aX3kQ9z...).
 * 
 * Changing even one character in the token will cause decryption to fail.
 */

if (!defined('HMS_TOKEN_SECRET')) {
    // Secret key for encryption — unique to this installation
    define('HMS_TOKEN_SECRET', 'EchoHMS_2026_S3cur3_T0k3n_K3y!@#');
}

if (!function_exists('hms_encrypt_id')) {
    /**
     * Encrypt an ID into an opaque URL-safe token
     * @param int $id The database ID to encrypt
     * @return string URL-safe encrypted token
     */
    function hms_encrypt_id(int $id): string
    {
        $method = 'aes-128-cbc';
        $key = substr(hash('sha256', HMS_TOKEN_SECRET), 0, 16);
        $iv = substr(hash('sha256', HMS_TOKEN_SECRET . '_iv'), 0, 16);
        
        // Add a timestamp to make each token unique even for the same ID
        $payload = json_encode(['id' => $id, 't' => time()]);
        
        $encrypted = openssl_encrypt($payload, $method, $key, 0, $iv);
        
        // Make it URL-safe: replace +/= with -_~
        $urlSafe = strtr($encrypted, '+/=', '-_~');
        
        return $urlSafe;
    }
}

if (!function_exists('hms_decrypt_id')) {
    /**
     * Decrypt a URL token back to the original ID
     * @param string $token The encrypted token from the URL
     * @return int|null The original ID, or null if token is invalid/tampered
     */
    function hms_decrypt_id(string $token): ?int
    {
        $method = 'aes-128-cbc';
        $key = substr(hash('sha256', HMS_TOKEN_SECRET), 0, 16);
        $iv = substr(hash('sha256', HMS_TOKEN_SECRET . '_iv'), 0, 16);
        
        // Reverse URL-safe encoding
        $base64 = strtr($token, '-_~', '+/=');
        
        $decrypted = openssl_decrypt($base64, $method, $key, 0, $iv);
        
        if ($decrypted === false) {
            return null; // Tampered or invalid token
        }
        
        $payload = json_decode($decrypted, true);
        
        if (!is_array($payload) || !isset($payload['id'])) {
            return null;
        }
        
        return (int)$payload['id'];
    }
}
