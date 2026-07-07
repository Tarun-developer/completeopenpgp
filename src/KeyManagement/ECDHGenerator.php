<?php
namespace KeyManagement;

use phpseclib3\Crypt\EC;

class ECDHGenerator
{
    /**
     * Generate an ECDH key pair.
     * 
     * @param string $curve Curve name (e.g., 'nistp256', 'nistp384').
     * @return array
     */
    public static function generateKey($curve = 'nistp256')
    {
        $privateKey = EC::createKey($curve);
        $publicKey = $privateKey->getPublicKey();

        return [
            'public' => $publicKey->toString('PKCS8'),
            'private' => $privateKey->toString('PKCS8')
        ];
    }
}
