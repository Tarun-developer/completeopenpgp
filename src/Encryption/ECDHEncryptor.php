<?php
namespace Encryption;

use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\DH;

class ECDHEncryptor
{
    /**
     * Encrypt a message using ECDH.
     * 
     * @param string $message
     * @param string $publicKey Recipient's public key (PKCS8/PEM format)
     * @return string Serialized base64 string containing ephemeral public key, IV, tag, and ciphertext.
     */
    public static function encrypt($message, $publicKey)
    {
        $recipientPublicKey = EC::loadPublicKey($publicKey);
        $curve = $recipientPublicKey->getCurve();
        
        // Generate ephemeral key pair
        $ephemeralPrivateKey = EC::createKey($curve);
        $ephemeralPublicKey = $ephemeralPrivateKey->getPublicKey();
        
        // Compute shared secret
        $sharedSecret = DH::computeSecret($ephemeralPrivateKey, $recipientPublicKey);
        
        // Derive key using HKDF
        $aesKey = hash_hkdf('sha256', $sharedSecret, 32, 'ecdh-encryption', '');
        
        // Encrypt message using AES-256-GCM
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($message, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);
        
        if ($ciphertext === false) {
            throw new \Exception("AES-256-GCM encryption failed.");
        }

        return base64_encode(json_encode([
            'ephemeral_public' => $ephemeralPublicKey->toString('PKCS8'),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ciphertext' => base64_encode($ciphertext)
        ]));
    }

    /**
     * Decrypt a message using ECDH.
     * 
     * @param string $ciphertext Serialized base64 encrypted packet
     * @param string $privateKey Recipient's private key (PKCS8/PEM format)
     * @return string Decrypted message
     */
    public static function decrypt($ciphertext, $privateKey)
    {
        $data = json_decode(base64_decode($ciphertext), true);
        if (!$data || !isset($data['ephemeral_public'], $data['iv'], $data['tag'], $data['ciphertext'])) {
            throw new \Exception("Invalid ECDH ciphertext format.");
        }

        $ephemeralPublicKey = EC::loadPublicKey($data['ephemeral_public']);
        $recipientPrivateKey = EC::loadPrivateKey($privateKey);
        
        // Compute shared secret
        $sharedSecret = DH::computeSecret($recipientPrivateKey, $ephemeralPublicKey);
        
        // Derive key using HKDF
        $aesKey = hash_hkdf('sha256', $sharedSecret, 32, 'ecdh-encryption', '');
        
        // Decrypt using AES-256-GCM
        $decrypted = openssl_decrypt(
            base64_decode($data['ciphertext']),
            'aes-256-gcm',
            $aesKey,
            OPENSSL_RAW_DATA,
            base64_decode($data['iv']),
            base64_decode($data['tag'])
        );

        if ($decrypted === false) {
            throw new \Exception("AES-256-GCM decryption failed.");
        }

        return $decrypted;
    }
}
