<?php

namespace CompleteOpenPGP;

use phpseclib3\Crypt\GPG;

class PGPHandler
{
    protected $gnupg;

    public function __construct()
    {
        // Initialize the GPG handler (you need GnuPG installed on the server)
        $this->gnupg = new \gnupg();
    }

    // Encrypt a message
    public function encrypt($message, $publicKey)
    {
        $this->gnupg->addencryptkey($publicKey);
        return $this->gnupg->encrypt($message);
    }

    // Decrypt a message
    public function decrypt($encryptedMessage, $privateKey, $passphrase)
    {
        $this->gnupg->adddecryptkey($privateKey, $passphrase);
        return $this->gnupg->decrypt($encryptedMessage);
    }

    // Sign a message
    public function sign($message, $privateKey, $passphrase)
    {
        $this->gnupg->addsignkey($privateKey, $passphrase);
        return $this->gnupg->sign($message);
    }

    // Verify a signed message
    public function verify($message, $signature, $publicKey)
    {
        $this->gnupg->addverifykey($publicKey);
        return $this->gnupg->verify($message, $signature);
    }
}

