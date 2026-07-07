<?php

namespace Encryption;

use KeyManagement\ElGamal;

class ElGamalEncryptor
{
    public static function encrypt($message, $publicKey)
    {
        $elGamal = new ElGamal(null, null, null, $publicKey);
        $ciphertext = $elGamal->encrypt($message);
        return base64_encode(json_encode($ciphertext));
    }

    public static function decrypt($ciphertext, $privateKey)
    {
        $data = json_decode(base64_decode($ciphertext), true);
        if (!$data || !isset($data['c1']) || !isset($data['c2'])) {
            throw new \Exception("Invalid ElGamal ciphertext format.");
        }
        $elGamal = new ElGamal(null, null, $privateKey, null);
        return $elGamal->decrypt($data['c1'], $data['c2']);
    }
}
