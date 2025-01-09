<?php
namespace KeyManagement;

use phpseclib3\Crypt\RSA;

class RSAGenerator
{
     public static function generateKey($keySize = 2048)
    {
        $privateKey = RSA::createKey($keySize);  // Generate a private key
        $publicKey = $privateKey->getPublicKey();  // Get the public key from the private key

        return [
            'public' => $publicKey,
            'private' => $privateKey  // Return the private key directly
        ];
    }
}

