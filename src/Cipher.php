<?php
declare(strict_types=1);

namespace peterurk\Cipher;

/**
 * Cipher Class
 *
 * @author Peter Post <peterurk@gmail.com>
 */
class Cipher
{

	/**
	 * SHA256 Encrypted Key
	 * @var string
	 */
    private string $encryptionKey;

    /**
     * Constructor
     *
     * @param string $personalKey Holds the personal key to use in encryption
     *
     * @throws Exception When the provided key is empty
     */
    public function __construct(string $personalKey)
    {
        if ($personalKey === '') {
            throw new Exception('A personal key is required for encryption/decryption', 1);
        }

        $this->encryptionKey = hash('sha256', $personalKey, true);
    }

    /**
     * Encrypt a string
     *
     * @param string $input Data to encrypt
     * @return string Base64 encoded IV and ciphertext
     */
    public function encrypt(string $input): string
    {
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $cipher = openssl_encrypt($input, 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            throw new Exception('Encryption failed');
        }

        return base64_encode($iv . $cipher);
    }

    /**
     * Decrypt string
     *
     * @param string $input Base64 encoded IV and ciphertext
     * @return string Decrypted output
     */
    public function decrypt(string $input): string
    {
        $raw = base64_decode($input, true);
        if ($raw === false) {
            throw new Exception('Input is not valid base64');
        }

        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($raw, 0, $ivLength);
        $ciphertext = substr($raw, $ivLength);

        $plain = openssl_decrypt($ciphertext, 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        if ($plain === false) {
            throw new Exception('Decryption failed');
        }

        return $plain;
    }
}

