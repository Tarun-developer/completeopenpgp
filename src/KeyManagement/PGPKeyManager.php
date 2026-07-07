<?php
namespace KeyManagement;

use Encryption\RSAEncryptor;
use Encryption\ElGamalEncryptor;
use Encryption\EdDSAEncryptor;
use Encryption\ECDHEncryptor;
use Signer\RSASigner;
use Signer\ElGamalSigner;
use Signer\EdDSASigner;
use Signer\ECDSASigner;

class PGPKeyManager
{
    public static function generateKey($algorithm='RSA', $keySize = 2048)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSAGenerator::generateKey($keySize);
            case 'ElGamal':
                return ElGamalGenerator::generateKey($keySize);
            case 'EdDSA':
                return EdDSAGenerator::generateKey();
            case 'ECDSA':
                return ECDSAGenerator::generateKey();
            case 'ECDH':
                return ECDHGenerator::generateKey();
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function encrypt($algorithm, $message, $publicKey)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSAEncryptor::encrypt($message, $publicKey);
            case 'ElGamal':
                return ElGamalEncryptor::encrypt($message, $publicKey);
            case 'EdDSA':
                return EdDSAEncryptor::encrypt($message, $publicKey);
            case 'ECDH':
                return ECDHEncryptor::encrypt($message, $publicKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function decrypt($algorithm, $ciphertext, $privateKey)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSAEncryptor::decrypt($ciphertext, $privateKey);
            case 'ElGamal':
                return ElGamalEncryptor::decrypt($ciphertext, $privateKey);
            case 'EdDSA':
                return EdDSAEncryptor::decrypt($ciphertext, $privateKey);
            case 'ECDH':
                return ECDHEncryptor::decrypt($ciphertext, $privateKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function sign($algorithm, $message, $privateKey)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSASigner::sign($message, $privateKey);
            case 'EdDSA':
                return EdDSASigner::sign($message, $privateKey);
            case 'ECDSA':
                return ECDSASigner::sign($message, $privateKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function verify($algorithm, $message, $signature, $publicKey)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSASigner::verify($message, $signature, $publicKey);
            case 'EdDSA':
                return EdDSASigner::verify($message, $signature, $publicKey);
            case 'ECDSA':
                return ECDSASigner::verify($message, $signature, $publicKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    /**
     * Armor a message with base64 encoding and headers/footers (RSA, ElGamal, EdDSA)
     */
    public static function enarmor($data, $marker = 'MESSAGE', array $headers = [])
    {
        $header = self::header($marker, $headers);
        $encodedData = chunk_split(base64_encode($data), 64, "\n");
        
        $crc = self::crc24($data);
        $crc_bytes = pack('N', $crc);
        $encodedCRC = '=' . base64_encode(substr($crc_bytes, 1)) . "\n";
        
        $footer = self::footer($marker);
        return $header . $encodedData . $encodedCRC . $footer;
    }

    /**
     * Unarmor a message, verifying headers/footers and decoding base64
     */
    public static function unarmor($text, $marker = 'MESSAGE')
    {
        $header = trim(self::header($marker));
        $footer = trim(self::footer($marker));
        
        $text = trim($text);
        if (strpos($text, $header) === false || strpos($text, $footer) === false) {
            throw new \Exception("Invalid PGP armor: Missing header or footer.");
        }
        
        // Extract data between header and footer
        $start = strpos($text, $header) + strlen($header);
        $end = strpos($text, $footer);
        $body = trim(substr($text, $start, $end - $start));
        
        // Split body into headers and base64 body (separated by blank line)
        $parts = explode("\n\n", $body, 2);
        if (count($parts) === 2) {
            $base64Part = $parts[1];
        } else {
            // Check if there are header lines (containing ':')
            if (strpos($parts[0], ':') !== false) {
                $lines = explode("\n", $parts[0]);
                $base64Lines = [];
                foreach ($lines as $line) {
                    if (strpos($line, ':') === false) {
                        $base64Lines[] = $line;
                    }
                }
                $base64Part = implode("\n", $base64Lines);
            } else {
                $base64Part = $parts[0];
            }
        }
        
        $lines = explode("\n", trim($base64Part));
        $checksumLine = trim(array_pop($lines));
        $base64Data = implode("", $lines);
        
        if (strpos($checksumLine, '=') !== 0) {
            $base64Data = trim($base64Part);
            $actualCRC = null;
        } else {
            $actualCRC = base64_decode(substr($checksumLine, 1));
        }
        
        $decodedData = base64_decode($base64Data);
        
        if ($actualCRC !== null) {
            $expectedCRC = substr(pack('N', self::crc24($decodedData)), 1);
            if ($expectedCRC !== $actualCRC) {
                throw new \Exception("CRC24 checksum mismatch.");
            }
        }
        
        return $decodedData;
    }

    /**
     * Generate the header for PGP armor
     */
    public static function header($marker, array $headers = [])
    {
        $header = "-----BEGIN " . strtoupper($marker) . "-----\n";
        foreach ($headers as $key => $value) {
            $header .= $key . ': ' . $value . "\n";
        }
        $header .= "\n"; // Blank line separating headers from data
        return $header;
    }

    /**
     * Generate the footer for PGP armor
     */
    public static function footer($marker, $crc24 = null)
    {
        $footer = "-----END " . strtoupper($marker) . "-----\n";
        if ($crc24 !== null) {
            $footer = "=" . $crc24 . "\n" . $footer;
        }
        return $footer;
    }

    /**
     * Calculate CRC24 checksum for message integrity
     */
    public static function crc24($data)
    {
        $crc = 0xb704ce; // Standard OpenPGP CRC24 preset (0xB704CE)
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($data[$i]) << 16);
            for ($j = 0; $j < 8; $j++) {
                $crc <<= 1;
                if ($crc & 0x1000000) {
                    $crc ^= 0x1864cfb; // OpenPGP CRC24 polynomial
                }
            }
        }

        return $crc & 0xffffff;
    }
}
