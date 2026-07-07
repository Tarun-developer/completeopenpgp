<?php

namespace KeyManagement;

use phpseclib3\Math\BigInteger;

class ElGamal
{
    private $p; // BigInteger prime
    private $g; // BigInteger generator
    private $x; // BigInteger private key
    private $y; // BigInteger public key

    public function __construct($p = null, $g = null, $x = null, $y = null)
    {
        if ($p === null) {
            // Standard MODP Group 14 (2048-bit prime)
            $p_hex = 'FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD1' .
                     '29024E088A67CC74020BBEA63B139B22514A08798E3404DD' .
                     'EF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245' .
                     'E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7ED' .
                     'EE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3D' .
                     'C2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F' .
                     '83655D23DCA3AD961C62F356208552BB9ED529077096966D' .
                     '670C354E4ABC9804F1746C08CA18217C32905E462E36CE3B' .
                     'E39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9' .
                     'DE2BCBF6955817183995497CEA956AE515D2261898FA0510' .
                     '15728E5A8AACAA68FFFFFFFFFFFFFFFF';
            $this->p = new BigInteger($p_hex, 16);
            $this->g = new BigInteger(2);
        } else {
            $this->p = $p instanceof BigInteger ? $p : new BigInteger($p);
            $this->g = $g instanceof BigInteger ? $g : new BigInteger($g);
        }

        if ($x !== null) {
            $this->x = $x instanceof BigInteger ? $x : new BigInteger($x);
        }
        if ($y !== null) {
            $this->y = $y instanceof BigInteger ? $y : new BigInteger($y);
        }
    }

    public function generateKeys()
    {
        // Generate private key x: 1 < x < p-1
        $two = new BigInteger(2);
        $limit = $this->p->subtract($two);
        $this->x = BigInteger::randomRange($two, $limit);

        // Public key y = g^x mod p
        $this->y = $this->g->powMod($this->x, $this->p);

        return [
            'private' => $this->x->toString(),
            'public' => $this->y->toString()
        ];
    }

    public function encrypt($message)
    {
        // Convert binary message to BigInteger safely
        $m = new BigInteger($message, 256);
        if ($m->compare($this->p) >= 0) {
            throw new \Exception("Message is too large for the prime p.");
        }

        // Choose random k in [2, p-2]
        $two = new BigInteger(2);
        $limit = $this->p->subtract($two);
        $k = BigInteger::randomRange($two, $limit);

        // c1 = g^k mod p
        $c1 = $this->g->powMod($k, $this->p);

        // c2 = m * y^k mod p
        $yk = $this->y->powMod($k, $this->p);
        list($q, $c2) = $m->multiply($yk)->divide($this->p);

        return [
            'c1' => $c1->toString(),
            'c2' => $c2->toString()
        ];
    }

    public function decrypt($c1, $c2)
    {
        $c1 = new BigInteger($c1);
        $c2 = new BigInteger($c2);

        // s = c1^x mod p
        $s = $c1->powMod($this->x, $this->p);

        // s_inv = s^-1 mod p
        $s_inv = $s->modInverse($this->p);
        if ($s_inv === false) {
            throw new \Exception("Modular inverse does not exist.");
        }

        // m = c2 * s_inv mod p
        list($q, $m) = $c2->multiply($s_inv)->divide($this->p);

        return $m->toBytes();
    }

    public function getPublicKey()
    {
        return $this->y ? $this->y->toString() : null;
    }

    public function getPrivateKey()
    {
        return $this->x ? $this->x->toString() : null;
    }
}
