<?php

namespace KeyManagement;

class ElGamal
{
    private $p; // Large prime number
    private $g; // Generator (primitive root mod p)
    private $x; // Private key
    private $y; // Public key

    // Constructor to initialize p, g, and generate keys
    public function __construct($p, $g)
    {
        $this->p = $p;
        $this->g = $g;
        // Generate a random private key x
        $this->x = rand(2, $this->p - 2); // private key
        // Calculate public key y = g^x % p
        $this->y = bcpowmod($this->g, $this->x, $this->p); // public key
    }

    // Generate keys (public and private)
    public function generateKeys()
    {
        return [
            'private' => $this->x,
            'public' => $this->y
        ];
    }

    // Encrypt the message (m)
    public function encrypt($m)
    {
        // Choose a random k
        $k = rand(2, $this->p - 2);
        
        // Calculate c1 = g^k % p
        $c1 = bcpowmod($this->g, $k, $this->p);
        
        // Calculate c2 = m * y^k % p
        $c2 = bcmul($m, bcpowmod($this->y, $k, $this->p));
        $c2 = bcmod($c2, $this->p);

        return ['c1' => $c1, 'c2' => $c2];
    }

    // Decrypt the ciphertext (c1, c2)
    public function decrypt($c1, $c2)
    {
        // Calculate s = c1^x % p
        $s = bcpowmod($c1, $this->x, $this->p);
        
        // Calculate modular inverse of s (s_inv)
        $s_inv = $this->modInverse($s, $this->p);
        
        // Decrypt m = c2 * s_inv % p
        $m = bcmul($c2, $s_inv);
        $m = bcmod($m, $this->p);

        return $m;
    }

    // Helper function to compute modular inverse using the Extended Euclidean Algorithm
    private function modInverse($a, $m)
    {
        $m0 = $m;
        $x0 = 0;
        $x1 = 1;

        if ($m == 1) {
            return 0;
        }

        while ($a > 1) {
            $q = bcdiv($a, $m, 0);
            $m = bcmod($a, $m);
            $a = $q;

            $t = $x0;
            $x0 = bcsub($x1, bcmul($q, $x0));
            $x1 = $t;
        }

        if ($x1 < 0) {
            $x1 = bcadd($x1, $m0);
        }

        return $x1;
    }

    // Getter for the public key
    public function getPublicKey()
    {
        return $this->y;
    }

    // Getter for the private key
    public function getPrivateKey()
    {
        return $this->x;
    }
}
