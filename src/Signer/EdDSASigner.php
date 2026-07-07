<?php
namespace Signer;

use SodiumException;

class EdDSASigner
{
    /**
     * Sign a message using EdDSA (Ed25519) private key.
     * 
     * @param string $message The message to sign.
     * @param string $privateKey Base64-encoded or raw private key.
     * @return string The raw signature.
     */
    public static function sign($message, $privateKey)
    {
        try {
            // Decode the private key if base64 encoded
            $decodedPrivateKey = self::isBase64($privateKey) ? base64_decode($privateKey) : $privateKey;

            // Generate detached signature
            return sodium_crypto_sign_detached($message, $decodedPrivateKey);
        } catch (SodiumException $e) {
            throw new \Exception("Error signing message: " . $e->getMessage());
        }
    }

    /**
     * Verifies a signed message using the EdDSA public key.
     * 
     * @param string $message The original message.
     * @param string $signature The raw or base64-encoded detached signature.
     * @param string $publicKey Base64-encoded or raw public key.
     * @return bool True if valid, false otherwise.
     */
    public static function verify($message, $signature, $publicKey)
    {
        try {
            $decodedPublicKey = self::isBase64($publicKey) ? base64_decode($publicKey) : $publicKey;
            $decodedSignature = self::isBase64($signature) ? base64_decode($signature) : $signature;

            if (strlen($decodedPublicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
                // If it's still not correct length, try raw
                $decodedPublicKey = $publicKey;
            }
            if (strlen($decodedSignature) !== SODIUM_CRYPTO_SIGN_BYTES) {
                $decodedSignature = $signature;
            }

            return sodium_crypto_sign_verify_detached($decodedSignature, $message, $decodedPublicKey);
        } catch (SodiumException $e) {
            throw new \Exception("Error verifying message: " . $e->getMessage());
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
