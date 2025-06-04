# Cipher

A simple PHP library to encrypt and decrypt strings using AES-256-CBC. The library provides a lightweight wrapper around `openssl` and requires PHP 8.1 or higher.

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

## Running Tests

```bash
composer install
composer test
```

## License

Released under the [MIT License](LICENSE).
