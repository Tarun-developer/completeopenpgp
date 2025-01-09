<?php
namespace Tests\KeyManagement;

use PHPUnit\Framework\TestCase;
use KeyManagement\PGPKeyManager;
use KeyManagement\RSAGenerator;
use KeyManagement\ElGamalGenerator;
use KeyManagement\EdDSAGenerator;
use Encryption\RSAEncryptor;
use Encryption\ElGamalEncryptor;
use Encryption\EdDSAEncryptor;
use Signer\RSASigner;
use Signer\ElGamalSigner;
use Signer\EdDSASigner;

class PGPKeyManagerTest extends TestCase
{
    // Test RSA key generation
    public function testGenerateRSAKey()
    {


        $keyPair = PGPKeyManager::generateKey('RSA');

        
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
    }

    // Test ElGamal key generation
    // public function testGenerateElGamalKey()
    // {
    //     $keyPair = PGPKeyManager::generateKey('ElGamal');
    //     $this->assertArrayHasKey('public', $keyPair);
    //     $this->assertArrayHasKey('private', $keyPair);
    // }

    // Test EdDSA key generation
    public function testGenerateEdDSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
    }

    // // Test RSA encryption and decryption
    public function testRSAEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $message = 'Hello, RSA!';

        $encrypted = PGPKeyManager::encrypt('RSA', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('RSA', $encrypted, $keyPair['private']);

        $this->assertEquals($message, $decrypted);
    }

    // // Test ElGamal encryption and decryption
    // // public function testElGamalEncryptDecrypt()
    // // {
    // //     $keyPair = PGPKeyManager::generateKey('ElGamal');
    // //     $message = 'Hello, ElGamal!';

    // //     $encrypted = PGPKeyManager::encrypt('ElGamal', $message, $keyPair['public']);
    // //     $decrypted = PGPKeyManager::decrypt('ElGamal', $encrypted, $keyPair['private']);

    // //     $this->assertEquals($message, $decrypted);
    // // }

    // // Test EdDSA encryption and decryption (Note: EdDSA is typically for signing, not encryption)
   public function testEdDSAEncryptDecrypt()
{
    // Generate EdDSA key pair
    $keyPair = PGPKeyManager::generateKey('EdDSA');
    
    // Print the raw public and private keys
    print_r("Public Key (Base64 Encoded): ");
    print_r(base64_encode($keyPair['public']));  // Base64 encode the public key for readability
    print_r("\n");

    print_r("Private Key (Base64 Encoded): ");
    print_r(base64_encode($keyPair['private']));  // Base64 encode the private key for readability
    print_r("\n");

    // Message to sign
    $message = 'Hello, EdDSA!';

    // Signing the message using the private key
    $signature = PGPKeyManager::sign('EdDSA', $message, $keyPair['private']);
    
    // Print the base64-encoded signature for readability
    print_r("Signature (Base64 Encoded): ");
    print_r(base64_encode($signature));  // Base64 encode the signature for readability
    print_r("\n");

    // Make sure the signature is correctly passed as a binary string to the verification method
    $signatureBinary = base64_decode(base64_encode($signature));  // Ensure binary data for verification

    // Verifying the signature using the raw public key (not base64 encoded)
    // The public key should be the raw 32-byte key, not base64 encoded
    $publicKeyRaw = $keyPair['public']; // No encoding, just raw key
    
    // Verifying the signature using the raw public key
    $verified = PGPKeyManager::verify('EdDSA', $message, $signatureBinary, $publicKeyRaw);
    
    // Print the verification result (true or false)
    print_r("********\n");
    print_r($verified ? 'Signature Verified' : 'Signature Not Verified');
    
    // Stop execution after printing the verification status
    die;

    // Assert that the signature is valid
    $this->assertTrue($verified);
}



    // // Test RSA signing and verification
    public function testRSASignVerify()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $message = 'Hello, RSA Signing!';

        $signature = PGPKeyManager::sign('RSA', $message, $keyPair['private']);
        $verified = PGPKeyManager::verify('RSA', $message, $signature, $keyPair['public']);

        $this->assertTrue($verified);
    }

    // // Test ElGamal signing and verification
    // // public function testElGamalSignVerify()
    // // {
    // //     $keyPair = PGPKeyManager::generateKey('ElGamal');
    // //     $message = 'Hello, ElGamal Signing!';

    // //     $signature = PGPKeyManager::sign('ElGamal', $message, $keyPair['private']);
    // //     $verified = PGPKeyManager::verify('ElGamal', $message, $signature, $keyPair['public']);

    // //     $this->assertTrue($verified);
    // // }

    // // Test EdDSA signing and verification
    // public function testEdDSASignVerify()
    // {
    //     $keyPair = PGPKeyManager::generateKey('EdDSA');
    //     $message = 'Hello, EdDSA Signing!';

    //     $signature = PGPKeyManager::sign('EdDSA', $message, $keyPair['private']);
    //     $verified = PGPKeyManager::verify('EdDSA', $message, $signature, $keyPair['public']);

    //     $this->assertTrue($verified);
    // }
}

