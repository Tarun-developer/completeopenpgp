<?php

namespace CompleteOpenPGP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array generateKey(string $algorithm = 'RSA', int|string $keySizeOrCurve = 2048)
 * @method static string encrypt(string $algorithm, string $message, string $publicKey)
 * @method static string decrypt(string $algorithm, string $ciphertext, string $privateKey)
 * @method static string sign(string $algorithm, string $message, string $privateKey)
 * @method static bool verify(string $algorithm, string $message, string $signature, string $publicKey)
 * @method static string enarmor(string $data, string $marker = 'MESSAGE', array $headers = [])
 * @method static string unarmor(string $text, string $marker = 'MESSAGE')
 * 
 * @see \KeyManagement\PGPKeyManager
 */
class CompleteOpenPGP extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'completeopenpgp';
    }
}
