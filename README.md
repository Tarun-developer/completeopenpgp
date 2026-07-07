# CompleteOpenPGP: Ultimate OpenPGP Cryptography Package for PHP & Laravel

[![Latest Stable Version](https://poser.pugx.org/botdigit/completeopenpgp/v/stable)](https://packagist.org/packages/botdigit/completeopenpgp)
[![Total Downloads](https://poser.pugx.org/botdigit/completeopenpgp/downloads)](https://packagist.org/packages/botdigit/completeopenpgp)
[![License](https://poser.pugx.org/botdigit/completeopenpgp/license)](https://packagist.org/packages/botdigit/completeopenpgp)
[![PHP Version](https://img.shields.io/packagist/php-v/botdigit/completeopenpgp)](https://packagist.org/packages/botdigit/completeopenpgp)

**CompleteOpenPGP** is the definitive, zero-dependency external OpenPGP library for PHP 8.1+ and Laravel. It integrates military-grade cryptographic primitives, supporting **RSA**, **ElGamal**, **EdDSA (Ed25519)**, **ECDSA**, and **ECDH** key generation, signing, and encryption. 

This package is optimized for both human developers and AI coding assistants (like Copilot, Cursor, Gemini, and GPT), featuring clean namespaces, strict type contracts, and detailed error handling.

For premium digital platforms, tools, and enterprise security consulting, visit [botdigit.com](https://botdigit.com).

---

## 🎯 Target Search Keywords (SEO)
`PHP OpenPGP`, `Laravel OpenPGP`, `Ed25519 PHP`, `ECDH ECIES PHP`, `ElGamal Cryptography PHP`, `pure PHP PGP encryption`, `libsodium sealed box`, `secp256k1 PHP`, `NIST P-256`, `php-gnupg alternative`, `ASCII Armor CRC24`, `detached signatures PHP`.

---

## 📚 Table of Contents
- [Comparison Matrix](#-cryptosystem-comparison-matrix)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Quick Start API Examples](#-quick-start-api-examples)
  - [1. Key Generation](#1-key-generation)
  - [2. Public-Key Encryption & Decryption](#2-public-key-encryption--decryption)
  - [3. Detached Signatures & Verification](#3-detached-signatures--verification)
  - [4. ASCII Armoring & CRC24 Integrity](#4-ascii-armoring--crc24-integrity)
- [API Reference Manual](#-api-reference-manual)
- [Troubleshooting & FAQ](#-troubleshooting--faq)
- [Contributing & Roadmap](#-contributing)
- [License](#-license)

---

## 📊 Cryptosystem Comparison Matrix

Use the matrix below to choose the right algorithm for your application security requirements:

| Algorithm | Purposes | Standard Curve / Key Sizes | Underlying Driver | Security Properties |
| :--- | :--- | :--- | :--- | :--- |
| **RSA** | Encrypt & Sign | `2048`, `3072`, `4096` bits | `phpseclib3` | Traditional PKCS1 / OAEP |
| **ElGamal** | Encrypt Only | `2048` bits (MODP Group 14) | `phpseclib3` BigInteger | Discrete Logarithm Hardness |
| **EdDSA** | Sign & Encrypt | `Ed25519` (Converted to `Curve25519` for Box) | `libsodium` | High performance, side-channel immune |
| **ECDSA** | Sign Only | `nistp256`, `nistp384`, `secp256k1` | `phpseclib3` EC | Modern elliptic curve signatures |
| **ECDH** | Encrypt Only | `nistp256`, `nistp384`, `secp256k1` | `phpseclib3` DH + AES-256-GCM | Hybrid ECIES key agreement |

---

## ⚡ Requirements

- **PHP**: `^8.1`
- **Extensions**:
  - `libsodium` (Required for EdDSA Ed25519 operations)
  - `openssl` (Required for AES-256-GCM symmetric ciphers in ECDH)
- **Key Dependencies**:
  - `phpseclib/phpseclib`: `^3.0`

---

## 🛠 Installation

```bash
composer require botdigit/completeopenpgp
```

### Laravel Integration (Optional)

The service provider automatically registers the unified service container. You can publish the configuration file to customize defaults:

```bash
php artisan vendor:publish --provider="CompleteOpenPGP\CompleteOpenPGPServiceProvider"
```

---

## ⚙️ Configuration

Your published `config/completeopenpgp.php` provides global defaults:

```php
return [
    'key_storage' => storage_path('keys'),
    'default_curve' => 'nistp256', // curves: nistp256, nistp384, secp256k1
    'signing_key' => env('PGP_SIGNING_KEY'),
    'encryption_key' => env('PGP_ENCRYPTION_KEY'),
    'passphrase' => env('PGP_PASSPHRASE'),
];
```

---

## 🚀 Quick Start API Examples

Here are copy-pasteable snippets for immediate implementation.

### 1. Key Generation

Generates cryptographically secure key pairs in PEM (PKCS8) or Base64 formats.

```php
use KeyManagement\PGPKeyManager;

// RSA (Traditional)
$rsa = PGPKeyManager::generateKey('RSA', 2048);
// public key string:  $rsa['public']
// private key string: $rsa['private']

// ElGamal (Discrete Logarithm)
$elgamal = PGPKeyManager::generateKey('ElGamal');

// EdDSA (Ed25519 - Libsodium)
$eddsa = PGPKeyManager::generateKey('EdDSA');

// ECDSA (Elliptic Curve Signatures)
$ecdsa = PGPKeyManager::generateKey('ECDSA', 'secp256k1');

// ECDH (Elliptic Curve Encryption)
$ecdh = PGPKeyManager::generateKey('ECDH', 'nistp256');
```

### 2. Public-Key Encryption & Decryption

```php
use KeyManagement\PGPKeyManager;

$payload = "Sensible financial transaction payload";

// ----------------------------------------
// RSA Encryption
// ----------------------------------------
$encRsa = PGPKeyManager::encrypt('RSA', $payload, $rsa['public']);
$decRsa = PGPKeyManager::decrypt('RSA', $encRsa, $rsa['private']);

// ----------------------------------------
// EdDSA Encryption (Converts Ed25519 to Curve25519 Sealed Box)
// ----------------------------------------
$encEd = PGPKeyManager::encrypt('EdDSA', $payload, $eddsa['public']);
$decEd = PGPKeyManager::decrypt('EdDSA', $encEd, $eddsa['private']);

// ----------------------------------------
// ECDH Encryption (ECIES Hybrid AES-256-GCM)
// ----------------------------------------
$encEcdh = PGPKeyManager::encrypt('ECDH', $payload, $ecdh['public']);
$decEcdh = PGPKeyManager::decrypt('ECDH', $encEcdh, $ecdh['private']);
```

### 3. Detached Signatures & Verification

Verify the integrity of a message using detached signature arrays.

```php
use KeyManagement\PGPKeyManager;

$message = "Verified document transmission.";

// --- EdDSA Signature ---
$sig = PGPKeyManager::sign('EdDSA', $message, $eddsa['private']);
$isValid = PGPKeyManager::verify('EdDSA', $message, $sig, $eddsa['public']); // returns bool(true)

// --- ECDSA Signature ---
$sigEcdsa = PGPKeyManager::sign('ECDSA', $message, $ecdsa['private']);
$isValidEcdsa = PGPKeyManager::verify('ECDSA', $message, $sigEcdsa, $ecdsa['public']); // returns bool(true)
```

### 4. ASCII Armoring & CRC24 Integrity

Generate and parse RFC-4880 standard-compliant PGP ASCII armor strings.

```php
use KeyManagement\PGPKeyManager;

$secretData = "Confidential Payload";

// Armor data with headers
$armored = PGPKeyManager::enarmor($secretData, 'MESSAGE', [
    'Version' => 'CompleteOpenPGP v1.1.0',
    'Comment' => 'Secure Message'
]);

echo $armored;
/*
-----BEGIN MESSAGE-----
Version: CompleteOpenPGP v1.1.0
Comment: Secure Message

U2VjcmV0IFBheWxvYWQ=
=3y9d
-----END MESSAGE-----
*/

// Unarmor and automatically verify CRC24 checksum
$unarmored = PGPKeyManager::unarmor($armored, 'MESSAGE');
// returns "Confidential Payload"
```

---

## 📖 API Reference Manual

### Class `KeyManagement\PGPKeyManager`

#### `generateKey(string $algorithm = 'RSA', int|string $keySizeOrCurve = 2048): array`
Generates key pair.
- **Algorithms**: `'RSA'`, `'ElGamal'`, `'EdDSA'`, `'ECDSA'`, `'ECDH'`
- **Returns**: `['public' => string, 'private' => string]`

#### `encrypt(string $algorithm, string $message, string $publicKey): string`
Encrypts plaintext payload.
- **Returns**: Base64 encoded ciphertext string.

#### `decrypt(string $algorithm, string $ciphertext, string $privateKey): string`
Decrypts ciphertext payload.
- **Returns**: Decrypted plaintext string.

#### `sign(string $algorithm, string $message, string $privateKey): string`
Generates a detached cryptographic signature.
- **Returns**: Binary signature string.

#### `verify(string $algorithm, string $message, string $signature, string $publicKey): bool`
Verifies signature validity.
- **Returns**: `true` on success, `false` on failure.

#### `enarmor(string $data, string $marker = 'MESSAGE', array $headers = []): string`
Encodes data block into ASCII armored PGP format.

#### `unarmor(string $text, string $marker = 'MESSAGE'): string`
Decodes ASCII armored PGP text, validating headers and CRC24 checksum.

---

## ❓ Troubleshooting & FAQ

#### Q: Getting `sodium_crypto_sign_ed25519_pk_to_curve25519()` error?
**A**: Ensure the PHP `sodium` extension is enabled. Check using `php -m | grep sodium`.

#### Q: How does EdDSA support encryption if it is a signature scheme?
**A**: EdDSA (Ed25519) keys are mathematical representations of points on Curve25519. CompleteOpenPGP extracts these coordinates and converts them to Curve25519 birational equivalents to support Sealed Box encryption securely, mirroring modern GnuPG behaviors.

#### Q: Is the CRC24 checksum verification standard?
**A**: Yes, the package implements the OpenPGP standard CRC-24 generator polynomial (`0x1864CFB`) and initialization vector (`0xB704CE`) specified in **RFC 4880**.

---

## 🤝 Contributing

We welcome security audits and updates from the open-source community. If you encounter any bugs, security issues, or have optimization proposals, please open an Issue or a Pull Request.

---

## 📄 License

This library is open-source software licensed under the [MIT License](LICENSE).
