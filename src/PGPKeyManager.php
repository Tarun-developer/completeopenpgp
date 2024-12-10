<?php

namespace CompleteOpenPGP;

class PGPKeyManager
{
    protected $gnupg;

    public function __construct()
    {
        $this->gnupg = new \gnupg();
    }

    // Generate a key pair (RSA)
    public function generateKeyPair($name, $email, $passphrase)
    {
        $keyInfo = [
            'name' => $name,
            'email' => $email,
            'passphrase' => $passphrase,
            'key_type' => 'RSA',
            'key_length' => 2048
        ];

        // Generate the key pair
        $key = $this->gnupg->genkey($keyInfo);

        return $key;
    }

    // Export a public key
    public function exportPublicKey($key)
    {
        return $this->gnupg->export($key);
    }

    // Export a private key
    public function exportPrivateKey($key, $passphrase)
    {
        return $this->gnupg->exportprivatekey($key, $passphrase);
    }

    // Import a key (public or private)
    public function importKey($keyData)
    {
        return $this->gnupg->import($keyData);
    }
}

