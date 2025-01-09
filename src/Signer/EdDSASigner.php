<?php
namespace Signer;

use SodiumException;

class EdDSASigner
{
    public static function sign($message, $privateKey)
    {
 try {
            // Decode the private key from Base64
            $decodedPrivateKey = base64_decode($privateKey);

            // Sign the message
            $signedMessage = sodium_crypto_sign($message, $decodedPrivateKey);

            return $signedMessage;
        } catch (SodiumException $e) {
            throw new \Exception("Error signing message: " . $e->getMessage());
        }
    }

    /**
     * Verifies a signed message using the EdDSA public key.
     * 
     * @param string $signedMessage The signed message to verify.
     * @param string $publicKey Base64-encoded public key.
     * 
     * @return bool True if the signature is valid, false otherwise.
     * @throws \Exception If verification fails.
     */
  public static function verify($signedMessage, $publicKey)
{
    try {
        // Decode the public key from Base64
        $decodedPublicKey = base64_decode($publicKey);
       
        // Ensure the public key is of the correct length
        // if (strlen($decodedPublicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
        //     throw new \Exception("Invalid public key length.");
        // }

        // Verify the signed message
        // sodium_crypto_sign_open expects the signed message (signature + original message)
        $message = sodium_crypto_sign_open($signedMessage, $decodedPublicKey);

        // If the message is valid, sodium_crypto_sign_open returns the original message
        return $message !== false;
    } catch (SodiumException $e) {

        throw new \Exception("Error verifying message: " . $e->getMessage());
    }
}

}

