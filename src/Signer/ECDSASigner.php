<?php
namespace Signer;

use phpseclib3\Crypt\EC;

class ECDSASigner
{
    /**
     * Sign a message using ECDSA private key.
     * 
     * @param string $message
     * @param string $privateKey
     * @return string Raw signature
     */
    public static function sign($message, $privateKey)
    {
        $ec = EC::loadPrivateKey($privateKey);
        return $ec->sign($message);
    }

    /**
     * Verify ECDSA signature.
     * 
     * @param string $message
     * @param string $signature
     * @param string $publicKey
     * @return bool
     */
    public static function verify($message, $signature, $publicKey)
    {
        $ec = EC::loadPublicKey($publicKey);
        return $ec->verify($message, $signature);
    }
}
