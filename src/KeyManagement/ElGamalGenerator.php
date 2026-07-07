<?php

namespace KeyManagement;

class ElGamalGenerator
{
    public static function generateKey($keySize = 2048)
    {
        $elGamal = new ElGamal();
        $keys = $elGamal->generateKeys();

        return [
            'public' => $keys['public'],
            'private' => $keys['private']
        ];
    }
}
