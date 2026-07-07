<?php
namespace Tests\KeyManagement;

use PHPUnit\Framework\TestCase;
use KeyManagement\PGPKeyManager;

class PGPKeyManagerTest extends TestCase
{
    // Test RSA Key Generation
    public function testGenerateRSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
        $this->assertNotEmpty($keyPair['public']);
        $this->assertNotEmpty($keyPair['private']);
    }

    // Test RSA Encryption & Decryption
    public function testRSAEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $message = 'Hello, RSA!';

        $encrypted = PGPKeyManager::encrypt('RSA', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('RSA', $encrypted, $keyPair['private']);

        $this->assertEquals($message, $decrypted);
    }

    // Test RSA Signing & Verification
    public function testRSASignVerify()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $message = 'Hello, RSA Signing!';

        $signature = PGPKeyManager::sign('RSA', $message, $keyPair['private']);
        $verified = PGPKeyManager::verify('RSA', $message, $signature, $keyPair['public']);

        $this->assertTrue($verified);
    }

    // Test ElGamal Key Generation
    public function testGenerateElGamalKey()
    {
        $keyPair = PGPKeyManager::generateKey('ElGamal');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
        $this->assertNotEmpty($keyPair['public']);
        $this->assertNotEmpty($keyPair['private']);
    }

    // Test ElGamal Encryption & Decryption
    public function testElGamalEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('ElGamal');
        $message = 'Hello, ElGamal!';

        $encrypted = PGPKeyManager::encrypt('ElGamal', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('ElGamal', $encrypted, $keyPair['private']);

        $this->assertEquals($message, $decrypted);
    }

    // Test EdDSA Key Generation
    public function testGenerateEdDSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
        $this->assertNotEmpty($keyPair['public']);
        $this->assertNotEmpty($keyPair['private']);
    }

    // Test EdDSA Signing & Verification
    public function testEdDSASignVerify()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $message = 'Hello, EdDSA Signing!';

        $signature = PGPKeyManager::sign('EdDSA', $message, $keyPair['private']);
        $verified = PGPKeyManager::verify('EdDSA', $message, $signature, $keyPair['public']);

        $this->assertTrue($verified);
    }

    // Test EdDSA Encryption & Decryption (via Curve25519)
    public function testEdDSAEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $message = 'Hello, EdDSA Encryption!';

        $encrypted = PGPKeyManager::encrypt('EdDSA', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('EdDSA', $encrypted, $keyPair['private']);

        $this->assertEquals($message, $decrypted);
    }

    // Test ECDSA Key Generation
    public function testGenerateECDSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('ECDSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
        $this->assertNotEmpty($keyPair['public']);
        $this->assertNotEmpty($keyPair['private']);
    }

    // Test ECDSA Signing & Verification
    public function testECDSASignVerify()
    {
        $keyPair = PGPKeyManager::generateKey('ECDSA');
        $message = 'Hello, ECDSA Signing!';

        $signature = PGPKeyManager::sign('ECDSA', $message, $keyPair['private']);
        $verified = PGPKeyManager::verify('ECDSA', $message, $signature, $keyPair['public']);

        $this->assertTrue($verified);
    }

    // Test ECDH Key Generation
    public function testGenerateECDHKey()
    {
        $keyPair = PGPKeyManager::generateKey('ECDH');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
        $this->assertNotEmpty($keyPair['public']);
        $this->assertNotEmpty($keyPair['private']);
    }

    // Test ECDH Encryption & Decryption
    public function testECDHEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('ECDH');
        $message = 'Hello, ECDH ECIES!';

        $encrypted = PGPKeyManager::encrypt('ECDH', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('ECDH', $encrypted, $keyPair['private']);

        $this->assertEquals($message, $decrypted);
    }

    // Test PGP Armor / Unarmor utilities
    public function testPGPArmor()
    {
        $data = "Confidential PGP Payload";
        $armored = PGPKeyManager::enarmor($data, 'MESSAGE', ['Version' => 'CompleteOpenPGP v1.1.0']);
        
        $this->assertStringContainsString('-----BEGIN MESSAGE-----', $armored);
        $this->assertStringContainsString('-----END MESSAGE-----', $armored);
        $this->assertStringContainsString('Version: CompleteOpenPGP v1.1.0', $armored);

        $unarmored = PGPKeyManager::unarmor($armored, 'MESSAGE');
        $this->assertEquals($data, $unarmored);
    }
}
