<?php
namespace KeyManagement;

use Encryption\RSAEncryptor;
use Encryption\ElGamalEncryptor;
use Encryption\EdDSAEncryptor;
use Signer\RSASigner;
use Signer\ElGamalSigner;
use Signer\EdDSASigner;
// use KeyManagement\RSAGenerator;

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
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function sign($algorithm, $message, $privateKey)
    {

       
        switch ($algorithm) {
            case 'RSA':
                return RSASigner::sign($message, $privateKey);
            case 'ElGamal':
                return ElGamalSigner::sign($message, $privateKey);
            case 'EdDSA':
                return EdDSASigner::sign($message, $privateKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    public static function verify($algorithm, $message, $signature, $publicKey)
    {
        switch ($algorithm) {
            case 'RSA':
                return RSASigner::verify($message, $signature, $publicKey);
            case 'ElGamal':
                return ElGamalSigner::verify($message, $signature, $publicKey);
            case 'EdDSA':
                return EdDSASigner::verify($message, $signature, $publicKey);
            default:
                throw new \Exception("Unknown algorithm: $algorithm");
        }
    }

    /**
     * Armor a message with base64 encoding and headers/footers (RSA, ElGamal, EdDSA)
     */
    public static function enarmor($data, $marker = 'MESSAGE', array $headers = [])
    {
        // Create the header
        $header = self::header($marker, $headers);
        
        // Base64 encode the message data
        $encodedData = chunk_split(base64_encode($data), 64, "\n");

        // Calculate CRC24 checksum
        $crc24 = self::crc24($data);
        $encodedCRC24 = strtoupper(base64_encode(substr(pack('N', $crc24), 1))); // Base64 CRC24

        // Create the footer
        $footer = self::footer($marker, $encodedCRC24);

        // Combine the components into the final armored message
        return $header . $encodedData . $footer;
    }

    /**
     * Unarmor a message, verifying headers/footers and decoding base64
     */
    public static function unarmor($text, $marker = 'MESSAGE')
    {
        // Extract and verify header/footer
        $header = self::header($marker);
        $footer = self::footer($marker);

        if (strpos($text, $header) === false || strpos($text, $footer) === false) {
            throw new Exception("Invalid PGP armor: Missing header or footer.");
        }

        // Extract the base64-encoded data
        $startPos = strpos($text, $header) + strlen($header);
        $endPos = strpos($text, $footer);
        $encodedData = substr($text, $startPos, $endPos - $startPos);

        // Decode the base64-encoded data
        $decodedData = base64_decode(trim($encodedData));

        // Verify the CRC24 checksum
        $expectedCRC24 = substr(pack('N', self::crc24($decodedData)), 1);
        $actualCRC24 = substr(base64_decode(substr($text, -8)), 1); // Extract and compare CRC

        if ($expectedCRC24 !== $actualCRC24) {
            throw new Exception("CRC24 checksum mismatch.");
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
        return $header;
    }

    /**
     * Generate the footer for PGP armor
     */
    public static function footer($marker, $crc24)
    {
        return "-----END " . strtoupper($marker) . "-----\n" . $crc24 . "\n";
    }

    /**
     * Calculate CRC24 checksum for message integrity
     */
    public static function crc24($data)
    {
        $crc = 0x000000; // Initial CRC value
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($data[$i]) << 16);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x800000) {
                    $crc = ($crc << 1) ^ 0x864CFB; // Polynomial for CRC24
                } else {
                    $crc <<= 1;
                }
            }
            $crc &= 0xFFFFFF; // Keep CRC value within 24 bits
        }

        return $crc;
    }
}


