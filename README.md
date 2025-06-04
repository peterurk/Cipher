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
export CIPHER_SALT=mysalt
```

```php
$cipher = new Cipher();
```

The defaults for the cipher method, tag length and salt are configurable via constants in `Cipher`.

### Security considerations

`AES-256-CBC` is vulnerable to tampering if you do not verify integrity. Enable the `--hmac` option or pass `true` as the third constructor argument to automatically append and validate an HMAC. `AES-256-GCM` already provides authentication and does not need HMAC.

### CLI

This repository ships with a small CLI tool. Show the help message:

```bash
bin/cipher --help
```

Encrypt a string:

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

Encrypt a file from the CLI:

```bash
bin/cipher --encrypt-file input.txt --key mysecret > output.enc
```

Decrypt a file from the CLI:

```bash
bin/cipher --decrypt-file output.enc --key mysecret > plain.txt
```

Large files are processed in chunks so they won't consume excessive memory.

## Running Tests

```bash
composer install
composer test
```

## License

Released under the [MIT License](LICENSE).
