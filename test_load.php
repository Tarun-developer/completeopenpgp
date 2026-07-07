<?php
/**
 * CompleteOpenPGP Autoload & Verification Integration Test Script
 * This script simulates loading the package via Composer autoload and runs
 * a sanity check across all five cryptosystems (RSA, ElGamal, EdDSA, ECDSA, ECDH).
 */

require_once __DIR__ . '/vendor/autoload.php';

use KeyManagement\PGPKeyManager;

echo "===========================================\n";
echo "🧪 CompleteOpenPGP Autoload & Sanity Test\n";
echo "===========================================\n\n";

$testMessage = "Hello OpenPGP! Verification Successful.";
$allPassed = true;

// 1. RSA Verification
try {
    echo "🔑 Testing RSA... ";
    $keys = PGPKeyManager::generateKey('RSA');
    $encrypted = PGPKeyManager::encrypt('RSA', $testMessage, $keys['public']);
    $decrypted = PGPKeyManager::decrypt('RSA', $encrypted, $keys['private']);
    $sig = PGPKeyManager::sign('RSA', $testMessage, $keys['private']);
    $verified = PGPKeyManager::verify('RSA', $testMessage, $sig, $keys['public']);
    
    if ($decrypted === $testMessage && $verified) {
        echo "✅ RSA Passed\n";
    } else {
        throw new Exception("RSA decrypted or signature verification mismatch.");
    }
} catch (Exception $e) {
    echo "❌ RSA Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 2. ElGamal Verification
try {
    echo "🔑 Testing ElGamal... ";
    $keys = PGPKeyManager::generateKey('ElGamal');
    $encrypted = PGPKeyManager::encrypt('ElGamal', $testMessage, $keys['public']);
    $decrypted = PGPKeyManager::decrypt('ElGamal', $encrypted, $keys['private']);
    
    if ($decrypted === $testMessage) {
        echo "✅ ElGamal Passed\n";
    } else {
        throw new Exception("ElGamal decrypted mismatch.");
    }
} catch (Exception $e) {
    echo "❌ ElGamal Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 3. EdDSA Verification
try {
    echo "🔑 Testing EdDSA (Ed25519)... ";
    $keys = PGPKeyManager::generateKey('EdDSA');
    $encrypted = PGPKeyManager::encrypt('EdDSA', $testMessage, $keys['public']);
    $decrypted = PGPKeyManager::decrypt('EdDSA', $encrypted, $keys['private']);
    $sig = PGPKeyManager::sign('EdDSA', $testMessage, $keys['private']);
    $verified = PGPKeyManager::verify('EdDSA', $testMessage, $sig, $keys['public']);
    
    if ($decrypted === $testMessage && $verified) {
        echo "✅ EdDSA Passed\n";
    } else {
        throw new Exception("EdDSA decrypted or signature verification mismatch.");
    }
} catch (Exception $e) {
    echo "❌ EdDSA Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 4. ECDSA Verification
try {
    echo "🔑 Testing ECDSA... ";
    $keys = PGPKeyManager::generateKey('ECDSA');
    $sig = PGPKeyManager::sign('ECDSA', $testMessage, $keys['private']);
    $verified = PGPKeyManager::verify('ECDSA', $testMessage, $sig, $keys['public']);
    
    if ($verified) {
        echo "✅ ECDSA Passed\n";
    } else {
        throw new Exception("ECDSA signature verification mismatch.");
    }
} catch (Exception $e) {
    echo "❌ ECDSA Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 5. ECDH Verification
try {
    echo "🔑 Testing ECDH (ECIES/AES-256-GCM)... ";
    $keys = PGPKeyManager::generateKey('ECDH');
    $encrypted = PGPKeyManager::encrypt('ECDH', $testMessage, $keys['public']);
    $decrypted = PGPKeyManager::decrypt('ECDH', $encrypted, $keys['private']);
    
    if ($decrypted === $testMessage) {
        echo "✅ ECDH Passed\n";
    } else {
        throw new Exception("ECDH decrypted mismatch.");
    }
} catch (Exception $e) {
    echo "❌ ECDH Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

// 6. PGP Armor Verification
try {
    echo "📦 Testing PGP ASCII Armor... ";
    $armored = PGPKeyManager::enarmor($testMessage, 'MESSAGE', ['Version' => 'CompleteOpenPGP Test']);
    $unarmored = PGPKeyManager::unarmor($armored, 'MESSAGE');
    
    if ($unarmored === $testMessage) {
        echo "✅ PGP Armor Passed\n";
    } else {
        throw new Exception("PGP Armor unarmor mismatch.");
    }
} catch (Exception $e) {
    echo "❌ PGP Armor Failed: " . $e->getMessage() . "\n";
    $allPassed = false;
}

echo "\n===========================================\n";
if ($allPassed) {
    echo "🎉 SUCCESS: CompleteOpenPGP is fully loaded and working!\n";
} else {
    echo "⚠️ FAILURE: Some cryptographic algorithms failed.\n";
    exit(1);
}
echo "===========================================\n";
