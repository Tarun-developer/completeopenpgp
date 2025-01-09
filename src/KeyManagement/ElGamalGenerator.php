<?php

namespace KeyManagement;

// use phpseclib3\Crypt\ElGamal;
use KeyManagement\ElGamal;

class ElGamalGenerator
{
    public static function generateKey($keySize = 2048)
    {
        $p = '3233';  // Example prime number
        $g = '2';     // Generator
        
        // Create an ElGamal instance
        $elGamal = new ElGamal($p, $g);

        
        // Generate keys
        $keys = $elGamal->generateKeys();
        


        // echo "Private Key: " . $keys['private'] . "\n";
        // echo "Public Key: " . $keys['public'] . "\n";
        // die("****");
        return [
            'public' => $elgamal->getPublicKey(),
            'private' => $elgamal->getPrivateKey()
        ];
    }
}

