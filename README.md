# CompleteOpenPGP Laravel Package

**CompleteOpenPGP** is a Laravel package that integrates OpenPGP support for encryption, decryption, signing, and signature verification. It supports the RSA, ElGamal, and EdDSA cryptosystems, allowing you to use various cryptographic algorithms for securing your messages.

This package provides an easy-to-use API to perform OpenPGP encryption and signing operations within your Laravel application.

Visit [botdigit.com](https://botdigit.com) for more information and premium digital products.

---

## Features

- RSA, ElGamal, and EdDSA cryptosystem support for key generation, encryption, signing, and verification.
- Seamlessly integrates with Laravel via service providers and facades.
- Supports elliptic curve signing for EdDSA.
- Includes unit tests to ensure the correctness of encryption, decryption, signing, and key management.

---

## Installation

### Step 1: Install the Package via Composer
Run the following command in your terminal:
```bash
composer require botdigit/completeopenpgp
Step 2: (Optional) Publish the Configuration File
You can publish the configuration file to customize the package settings:

bash
Copy code
php artisan vendor:publish --provider="CompleteOpenPGP\CompleteOpenPGPServiceProvider"
This will create a configuration file at config/completeopenpgp.php.

Step 3: Set Up Environment Variables
In your .env file, define the PGP keys and passphrases that will be used for encryption, decryption, signing, and verification:

dotenv
Copy code
PGP_SIGNING_KEY="your-signing-key-here"
PGP_ENCRYPTION_KEY="your-encryption-key-here"
PGP_PASSPHRASE="your-passphrase-here"
Configuration
The configuration file (config/completeopenpgp.php) contains the following settings:

php
Copy code
return [
    'key_storage' => storage_path('keys'), // Directory for storing keys
    'default_curve' => 'ed25519', // Default curve for EdDSA signing
    'signing_key' => env('PGP_SIGNING_KEY'),
    'encryption_key' => env('PGP_ENCRYPTION_KEY'),
    'passphrase' => env('PGP_PASSPHRASE'),
];
Usage
Encrypting a Message
Encrypt a message using the recipient's public key:

php
Copy code
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$publicKey = file_get_contents(storage_path('keys/public_key.asc'));
$message = "This is a secret message!";

$encryptedMessage = CompleteOpenPGP::encrypt($message, $publicKey);
Decrypting a Message
Decrypt a message using your private key and passphrase:

php
Copy code
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$privateKey = file_get_contents(storage_path('keys/private_key.asc'));
$passphrase = env('PGP_PASSPHRASE');
$encryptedMessage = '...'; // The encrypted message

$decryptedMessage = CompleteOpenPGP::decrypt($encryptedMessage, $privateKey, $passphrase);
Signing a Message
Sign a message with your private key:

php
Copy code
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$privateKey = file_get_contents(storage_path('keys/private_key.asc'));
$passphrase = env('PGP_PASSPHRASE');
$message = "This message is signed.";

$signedMessage = CompleteOpenPGP::sign($message, $privateKey, $passphrase);
Verifying a Signed Message
Verify a signed message using the public key:

php
Copy code
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$publicKey = file_get_contents(storage_path('keys/public_key.asc'));
$message = "This message is signed.";
$signature = '...'; // The signature from the signed message

$isValid = CompleteOpenPGP::verify($message, $signature, $publicKey);

if ($isValid) {
    echo "The signature is valid!";
} else {
    echo "The signature is invalid!";
}
Artisan Commands
Encrypt a File
bash
Copy code
php artisan openpgp:encrypt --input "path/to/message.txt" --output "path/to/encrypted_message.asc" --public-key "path/to/public_key.asc"
Decrypt a File
bash
Copy code
php artisan openpgp:decrypt --input "path/to/encrypted_message.asc" --output "path/to/decrypted_message.txt" --private-key "path/to/private_key.asc" --passphrase "your-passphrase"
Testing
The package includes unit tests to ensure key generation, encryption, decryption, signing, and verification are working as expected.

Run the tests using Laravel’s built-in test suite:

bash
Copy code
php artisan test
Or use PHPUnit:

bash
Copy code
vendor/bin/phpunit --testdox
Test Coverage
The following functionality is covered in the tests:

Key Generation for RSA, ElGamal, and EdDSA
RSA encryption and decryption
EdDSA signing and verification
Signature verification for RSA and EdDSA
RSA signing and verification
Example Test File
Here’s an example test case for key generation and encryption:

php
Copy code
namespace Tests\KeyManagement;

use PHPUnit\Framework\TestCase;
use KeyManagement\PGPKeyManager;

class PGPKeyManagerTest extends TestCase
{
    public function testGenerateRSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
    }

    public function testGenerateEdDSAKey()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $this->assertArrayHasKey('public', $keyPair);
        $this->assertArrayHasKey('private', $keyPair);
    }

    public function testRSAEncryptDecrypt()
    {
        $keyPair = PGPKeyManager::generateKey('RSA');
        $message = 'Hello, RSA!';
        $encrypted = PGPKeyManager::encrypt('RSA', $message, $keyPair['public']);
        $decrypted = PGPKeyManager::decrypt('RSA', $encrypted, $keyPair['private']);
        $this->assertEquals($message, $decrypted);
    }

    public function testEdDSASignVerify()
    {
        $keyPair = PGPKeyManager::generateKey('EdDSA');
        $message = 'Hello, EdDSA Signing!';
        $signature = PGPKeyManager::sign('EdDSA', $message, $keyPair['private']);
        $verified = PGPKeyManager::verify('EdDSA', $message, $signature, $keyPair['public']);
        $this->assertTrue($verified);
    }
}
Security
Private Keys: Always store private keys securely, preferably outside the public directory.
Passphrases: Use strong passphrases to protect private keys from unauthorized access.
Contributing
We welcome contributions! Follow these steps to contribute:

Fork the repository.
Create a new branch:
bash
Copy code
git checkout -b feature/your-feature-name
Make your changes.
Commit your changes:
bash
Copy code
git commit -am 'Add new feature'
Push to the branch:
bash
Copy code
git push origin feature/your-feature-name
Submit a pull request.
License
This package is open-source and available under the MIT License.

Visit botdigit.com for more information!
