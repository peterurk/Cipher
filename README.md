# Cipher PHP Class

This is a class I wrote to encrypt and decrypt everything you can possibly imagine. In the tests folder you will find an encryption and a decryption example. Examples rely on Composer's autoloader. After cloning the repository run `composer dump-autoload` so the generated autoload files are available.

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

The library uses the OpenSSL extension so make sure it is available.

## Contributing guidelines

We’d love you to help us improve this project. To help us keep this collection high quality, we request that contributions adhere to the following guidelines.

- **Provide a link to the application or project’s homepage**. Unless it’s
  extremely popular, there’s a chance the maintainers don’t know about or use
  the language, framework, editor, app, or project your change applies to.
- **Provide links to documentation** supporting the change you’re making.
  Current, canonical documentation mentioning the files being ignored is best.
  If documentation isn’t available to support your change, do the best you can
  to explain what the files being ignored are for.
- **Explain why you’re making a change**. Even if it seems self-evident, please
  take a sentence or two to tell us why your change or addition should happen.
  It’s especially helpful to articulate why this change applies to *everyone*
  who works with the applicable technology, rather than just you or your team.
- **Please consider the scope of your change**. If your change specific to a
  certain language or framework, then make sure the change is made to the
  template for that language or framework, rather than to the template for an
  editor, tool, or operating system.
- **Please only modify *one template* per pull request**. This helps keep pull
  requests and feedback focused on a specific project or technology.

In general, the more you can do to help us understand the change you’re making,
the more likely we’ll be to accept your contribution quickly.

Please also understand that we can’t list every tool that ever existed.
Our aim is to curate a collection of the *most common and helpful* templates,
not to make sure we cover every project possible. If we choose not to
include your language, tool, or project, it’s not because it’s not awesome.

## Contributing workflow

Here’s how we suggest you go about proposing a change to this project:

1. [Fork this project][fork] to your account.
2. [Create a branch][branch] for the change you intend to make.
3. Make your changes to your fork.
4. [Send a pull request][pr] from your fork’s branch to our `master` branch.

Using the web-based interface to make changes is fine too, and will help you
by automatically forking the project and prompting to send a pull request too.

[fork]: http://help.github.com/forking/
[branch]: https://help.github.com/articles/creating-and-deleting-branches-within-your-repository
[pr]: http://help.github.com/pull-requests/

## License

[MIT](./LICENSE).
