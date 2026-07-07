<?php
namespace Encryption;

use SodiumException;

class EdDSAEncryptor
{
    /**
     * Encrypt a message using EdDSA public key (by converting it to Curve25519).
     * 
     * @param string $message
     * @param string $publicKey Base64-encoded or raw Ed25519 public key.
     * @return string Base64-encoded encrypted message.
     */
    public static function encrypt($message, $publicKey)
    {
        try {
            $rawPk = self::isBase64($publicKey) ? base64_decode($publicKey) : $publicKey;
            
            // Convert Ed25519 public key to Curve25519 public key
            $curvePk = sodium_crypto_sign_ed25519_pk_to_curve25519($rawPk);
            
            // Encrypt using Sealed Box
            $encrypted = sodium_crypto_box_seal($message, $curvePk);
            
            return base64_encode($encrypted);
        } catch (SodiumException $e) {
            throw new \Exception("Encryption failed: " . $e->getMessage());
        }
    }

    /**
     * Decrypt a message using EdDSA private key.
     * 
     * @param string $ciphertext Base64-encoded encrypted message.
     * @param string $privateKey Base64-encoded or raw Ed25519 private key.
     * @return string Decrypted message.
     */
    public static function decrypt($ciphertext, $privateKey)
    {
        try {
            $rawSk = self::isBase64($privateKey) ? base64_decode($privateKey) : $privateKey;
            $encryptedData = base64_decode($ciphertext);

            // Convert Ed25519 secret key to Curve25519 secret key
            $curveSk = sodium_crypto_sign_ed25519_sk_to_curve25519($rawSk);
            
            // Derive public key from secret key
            $curvePk = sodium_crypto_box_publickey_from_secretkey($curveSk);
            $keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey($curveSk, $curvePk);

            $decrypted = sodium_crypto_box_seal_open($encryptedData, $keyPair);
            if ($decrypted === false) {
                throw new \Exception("Decryption failed. Invalid key or corrupted data.");
            }

            return $decrypted;
        } catch (SodiumException $e) {
            throw new \Exception("Decryption failed: " . $e->getMessage());
        }
    }

    private static function isBase64($str)
    {
        if (!is_string($str)) {
            return false;
        }
        return base64_encode(base64_decode($str, true)) === $str;
    }
}
