# Cipher

A simple PHP library to encrypt and decrypt strings. The library supports `AES-256-CBC` and `AES-256-GCM` and requires PHP 8.1 or higher.

## Installation

Install the package through [Composer](https://getcomposer.org/):

```bash
composer require peterurk/cipher
```

## Usage

```php
use peterurk\Cipher\Cipher;

$cipher = new Cipher('YourSecretKey');

$encrypted = $cipher->encrypt('message');
$decrypted = $cipher->decrypt($encrypted);
```

### Using environment variables

You can provide the key and cipher method through environment variables:

```bash
export CIPHER_KEY=mysecret
export CIPHER_METHOD=AES-256-GCM
```

```php
$cipher = new Cipher();
```

### CLI

This repository ships with a small CLI tool. Encrypt a string:

```bash
bin/cipher --encrypt "hello" --key mysecret
```

Decrypt a string:

```bash
bin/cipher --decrypt "<ciphertext>" --key mysecret
```

### File encryption

```php
$cipher->encryptFile('input.txt', 'output.enc');
$cipher->decryptFile('output.enc', 'plain.txt');
```

## Running Tests

```bash
composer install
composer test
```

## License

Released under the [MIT License](LICENSE).
