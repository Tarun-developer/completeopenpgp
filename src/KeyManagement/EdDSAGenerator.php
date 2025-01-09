<?php
namespace KeyManagement;

use SodiumException;

class EdDSAGenerator
{
    public static function generateKey()
    {
        try {
            // Generate the EdDSA key pair
            $keyPair = sodium_crypto_sign_keypair();
            
            // Extract the public and private keys
            $publicKey = sodium_crypto_sign_publickey($keyPair);
            $privateKey = sodium_crypto_sign_secretkey($keyPair);

            // Encode keys in Base64 for better readability and storage
            $publicKeyBase64 = base64_encode($publicKey);
            $privateKeyBase64 = base64_encode($privateKey);

            return [
                'public' => $publicKeyBase64,
                'private' => $privateKeyBase64
            ];
        } catch (SodiumException $e) {
            // Handle any errors with a custom exception message
            throw new \Exception("Error generating EdDSA key pair: " . $e->getMessage());
        }
    }
}
