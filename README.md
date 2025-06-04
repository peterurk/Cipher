# Cipher PHP Class

A minimal class for encrypting and decrypting strings with PHP.

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require peterurk/cipher
```

## Usage

```php
<?php
require 'vendor/autoload.php';

use peterurk\Cipher\Cipher;

$cipher = new Cipher('your-secret-passphrase');

$encrypted = $cipher->encrypt('Hello world!');
$decrypted = $cipher->decrypt($encrypted);
```

The library uses `mcrypt` under the hood so make sure the extension is available.

## License

[MIT](./LICENSE).
