Sure! Here’s a comprehensive **README.md** for your **CompleteOpenPGP** package, which can be used by users to understand how to install and use the package in a Laravel application. This README includes installation instructions, configuration details, and usage examples.

---

# CompleteOpenPGP Laravel Package

**CompleteOpenPGP** is a complete OpenPGP package for Laravel, supporting encryption, decryption, signing, and verification using the **OpenPGP** standard. It supports the **ElGamal cryptosystem** (with elliptic curve support) and integrates easily into Laravel applications.

## Features

- Full **OpenPGP** support for encryption, decryption, signing, and signature verification.
- **ElGamal cryptosystem** with elliptic curve support.
- Laravel-friendly integration with easy-to-use APIs.
- Supports common elliptic curve algorithms such as **NIST**, **brainpool**, and **ed25519**.

## Installation

To install the package, you can use Composer.

### Step 1: Install the Package via Composer

Run the following command in your terminal to install **CompleteOpenPGP**:

```bash
composer require botdigit/completeopenpgp
```

### Step 2: (Optional) Publish the Configuration File

You can publish the configuration file to customize the package according to your needs:

```bash
php artisan vendor:publish --provider="CompleteOpenPGP\CompleteOpenPGPServiceProvider"
```

This will create a configuration file at `config/completeopenpgp.php`.

## Configuration

Once installed, you can configure the **CompleteOpenPGP** package by editing the published configuration file `config/completeopenpgp.php`.

### Example configuration:

```php
return [
    'key_storage' => storage_path('keys'), // Path where keys will be stored
    'default_curve' => 'ed25519', // Default elliptic curve for signing
    'signing_key' => env('PGP_SIGNING_KEY'), // PGP signing key (can be set in .env)
    'encryption_key' => env('PGP_ENCRYPTION_KEY'), // PGP encryption key (can be set in .env)
    'passphrase' => env('PGP_PASSPHRASE'), // Passphrase for private keys (if necessary)
];
```

Make sure to set the environment variables (`PGP_SIGNING_KEY`, `PGP_ENCRYPTION_KEY`, `PGP_PASSPHRASE`) in your `.env` file.

## Usage

### Encrypting a Message

To encrypt a message using a public key:

```php
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$publicKey = file_get_contents(storage_path('keys/public_key.asc'));
$message = "Hello, this is a test message!";

$encryptedMessage = CompleteOpenPGP::encrypt($message, $publicKey);
```

### Decrypting a Message

To decrypt a message using a private key and passphrase:

```php
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$privateKey = file_get_contents(storage_path('keys/private_key.asc'));
$passphrase = env('PGP_PASSPHRASE');
$encryptedMessage = '...'; // Encrypted message

$decryptedMessage = CompleteOpenPGP::decrypt($encryptedMessage, $privateKey, $passphrase);
```

### Signing a Message

To sign a message with your private key:

```php
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$privateKey = file_get_contents(storage_path('keys/private_key.asc'));
$passphrase = env('PGP_PASSPHRASE');
$message = "This is a message I want to sign.";

$signedMessage = CompleteOpenPGP::sign($message, $privateKey, $passphrase);
```

### Verifying a Signed Message

To verify a signed message using the public key:

```php
use CompleteOpenPGP\Facades\CompleteOpenPGP;

$publicKey = file_get_contents(storage_path('keys/public_key.asc'));
$message = "This is a message I want to verify.";
$signature = '...'; // Signature from the signed message

$isValid = CompleteOpenPGP::verify($message, $signature, $publicKey);

if ($isValid) {
    echo "The signature is valid!";
} else {
    echo "The signature is invalid!";
}
```

## Command-Line Usage

You can also use artisan commands to perform encryption, decryption, signing, and verification.

### Example Command to Encrypt a File:

```bash
php artisan openpgp:encrypt --input "path/to/message.txt" --output "path/to/encrypted_message.asc" --public-key "path/to/public_key.asc"
```

### Example Command to Decrypt a File:

```bash
php artisan openpgp:decrypt --input "path/to/encrypted_message.asc" --output "path/to/decrypted_message.txt" --private-key "path/to/private_key.asc" --passphrase "your-passphrase"
```

## Testing

To run tests for the **CompleteOpenPGP** package, you can use Laravel’s built-in test suite:

```bash
php artisan test
```

### Unit Tests

Unit tests are provided in the `tests` directory. You can extend the tests based on your requirements.

## Security

- **Keys**: Make sure your private keys are kept **secure**. The package uses `gnupg` or `phpseclib` to handle key management, but you should always ensure that private keys are stored in a safe, non-public location.
- **Passphrase**: Use a **strong passphrase** for your private keys to protect them from unauthorized access.

## Contributing

If you'd like to contribute to **CompleteOpenPGP**, feel free to fork the repository, make your changes, and submit a pull request.

### Steps to Contribute:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/your-feature-name`)
3. Make your changes
4. Commit your changes (`git commit -am 'Add new feature'`)
5. Push to the branch (`git push origin feature/your-feature-name`)
6. Submit a pull request

## License

This package is open-source and available under the [MIT License](LICENSE).

---

### Notes:
- **Make sure to replace paths and environment variables** with your own values.
- Include **clear documentation** for end users, like example inputs/outputs, where to get keys, and how to configure things in Laravel.

Let me know if you need additional changes or improvements!
