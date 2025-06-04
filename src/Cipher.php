<?php
declare(strict_types=1);

namespace peterurk\Cipher;

use InvalidArgumentException;

use peterurk\Cipher\Exception\CipherException;
use peterurk\Cipher\Exception\InvalidCipherMethodException;
use peterurk\Cipher\Exception\HmacVerificationException;

/**
 * Cipher Class
 *
 * @author Peter Post <peterurk@gmail.com>
 */
final class Cipher implements CipherInterface
{
    public const DEFAULT_METHOD = 'AES-256-CBC';
    public const DEFAULT_SALT = 'cipher_salt';
    public const DEFAULT_TAG_LENGTH = 16;
    public const HMAC_LENGTH = 32;

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

        $salt = getenv('CIPHER_SALT') ?: self::DEFAULT_SALT;
        $this->encryptionKey = sodium_crypto_pwhash(
            32,
            $personalKey,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
        );

        $this->method = $method ?? getenv('CIPHER_METHOD') ?: self::DEFAULT_METHOD;
        if (!in_array($this->method, openssl_get_cipher_methods(), true)) {
            throw new InvalidCipherMethodException('Unsupported cipher method');
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
        $iv = random_bytes($ivLength);
        if ($this->method === 'AES-256-GCM') {
            $cipher = openssl_encrypt($input, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);
            if ($cipher === false) {
                throw new CipherException('Encryption failed');
            }
            return base64_encode($iv . $tag . $cipher);
        }

        $cipher = openssl_encrypt($input, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        if ($cipher === false) {
            throw new CipherException('Encryption failed');
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
            $tagLength = self::DEFAULT_TAG_LENGTH;
            $tag = substr($raw, $offset, $tagLength);
            $offset += $tagLength;
            $ciphertext = substr($raw, $offset);
            $plain = openssl_decrypt($ciphertext, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv, $tag);
        } else {
            if ($this->useHmac) {
                $hmacLength = self::HMAC_LENGTH;
                $hmac = substr($raw, $offset, $hmacLength);
                $offset += $hmacLength;
                $ciphertext = substr($raw, $offset);
                $calculated = hash_hmac('sha256', $iv . $ciphertext, $this->encryptionKey, true);
                if (!hash_equals($hmac, $calculated)) {
                    throw new HmacVerificationException('HMAC verification failed');
                }
            } else {
                $ciphertext = substr($raw, $offset);
            }
            $plain = openssl_decrypt($ciphertext, $this->method, $this->encryptionKey, OPENSSL_RAW_DATA, $iv);
        }

        if ($plain === false) {
            throw new CipherException('Decryption failed');
        }

        return $plain;
    }

    public function encryptFile(string $inputPath, string $outputPath): void
    {
        $in = fopen($inputPath, 'rb');
        if ($in === false) {
            throw new CipherException('Unable to read input file');
        }
        $out = fopen($outputPath, 'wb');
        if ($out === false) {
            fclose($in);
            throw new CipherException('Unable to write output file');
        }

        while (!feof($in)) {
            $chunk = fread($in, 1048576);
            if ($chunk === false) {
                fclose($in);
                fclose($out);
                throw new CipherException('Unable to read input file');
            }
            if ($chunk === '') {
                continue;
            }
            $encrypted = $this->encrypt($chunk);
            if (fwrite($out, $encrypted . PHP_EOL) === false) {
                fclose($in);
                fclose($out);
                throw new CipherException('Unable to write output file');
            }
        }

        fclose($in);
        fclose($out);
    }

    public function decryptFile(string $inputPath, string $outputPath): void
    {
        $in = fopen($inputPath, 'rb');
        if ($in === false) {
            throw new CipherException('Unable to read input file');
        }
        $out = fopen($outputPath, 'wb');
        if ($out === false) {
            fclose($in);
            throw new CipherException('Unable to write output file');
        }

        while (($line = fgets($in)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $plain = $this->decrypt($line);
            if (fwrite($out, $plain) === false) {
                fclose($in);
                fclose($out);
                throw new CipherException('Unable to write output file');
            }
        }

        if (!feof($in)) {
            fclose($in);
            fclose($out);
            throw new CipherException('Unable to read input file');
        }

        fclose($in);
        fclose($out);
    }
}

