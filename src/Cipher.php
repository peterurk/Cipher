<?php
declare(strict_types=1);

namespace peterurk\Cipher;

use InvalidArgumentException;
use RuntimeException;

/**
 * Cipher Class
 *
 * @author Peter Post <peterurk@gmail.com>
 */
final class Cipher implements CipherInterface
{

    /**
     * SHA256 Encrypted key
     */
    private string $encryptionKey;
    private string $method;
    private bool $useHmac;

    /**
     * Constructor
     *
     * @param string $personalKey Holds the personal key to use in encryption
     *
     * @throws InvalidArgumentException When the provided key is empty
     */
    public function __construct(?string $personalKey = null, ?string $method = null, bool $useHmac = false)
    {
        $personalKey = $personalKey ?? getenv('CIPHER_KEY');
        if ($personalKey === false || $personalKey === '') {
            throw new InvalidArgumentException('A personal key is required for encryption/decryption');
        }

        $this->encryptionKey = hash('sha256', $personalKey, true);

        $this->method = $method ?? getenv('CIPHER_METHOD') ?: 'AES-256-CBC';
        if (!in_array($this->method, openssl_get_cipher_methods(), true)) {
            throw new InvalidArgumentException('Unsupported cipher method');
        }

        $this->useHmac = $useHmac;
    }

    /**
     * Encrypt a string
     *
     * @param string $input Data to encrypt
     * @return string Base64 encoded IV and ciphertext
     */
    public function encrypt(string $input): string
    {
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        if ($this->method === 'AES-256-GCM') {
            $cipher = openssl_encrypt($input, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);
            if ($cipher === false) {
                throw new RuntimeException('Encryption failed');
            }
            return base64_encode($iv . $tag . $cipher);
        }

        $cipher = openssl_encrypt($input, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            throw new RuntimeException('Encryption failed');
        }

        if ($this->useHmac) {
            $hmac = hash_hmac('sha256', $iv . $cipher, $this->encryptionKey, true);
            return base64_encode($iv . $hmac . $cipher);
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
            throw new InvalidArgumentException('Input is not valid base64');
        }

        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($raw, 0, $ivLength);
        $offset = $ivLength;
        if ($this->method === 'AES-256-GCM') {
            $tagLength = 16;
            $tag = substr($raw, $offset, $tagLength);
            $offset += $tagLength;
            $ciphertext = substr($raw, $offset);
            $plain = openssl_decrypt($ciphertext, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);
        } else {
            if ($this->useHmac) {
                $hmacLength = 32;
                $hmac = substr($raw, $offset, $hmacLength);
                $offset += $hmacLength;
                $ciphertext = substr($raw, $offset);
                $calculated = hash_hmac('sha256', $iv . $ciphertext, $this->encryptionKey, true);
                if (!hash_equals($hmac, $calculated)) {
                    throw new RuntimeException('HMAC verification failed');
                }
            } else {
                $ciphertext = substr($raw, $offset);
            }
            $plain = openssl_decrypt($ciphertext, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        }

        if ($plain === false) {
            throw new RuntimeException('Decryption failed');
        }

        return $plain;
    }

    public function encryptFile(string $inputPath, string $outputPath): void
    {
        $data = file_get_contents($inputPath);
        if ($data === false) {
            throw new RuntimeException('Unable to read input file');
        }
        $encrypted = $this->encrypt($data);
        if (file_put_contents($outputPath, $encrypted) === false) {
            throw new RuntimeException('Unable to write output file');
        }
    }

    public function decryptFile(string $inputPath, string $outputPath): void
    {
        $data = file_get_contents($inputPath);
        if ($data === false) {
            throw new RuntimeException('Unable to read input file');
        }
        $plain = $this->decrypt($data);
        if (file_put_contents($outputPath, $plain) === false) {
            throw new RuntimeException('Unable to write output file');
        }
    }
}

