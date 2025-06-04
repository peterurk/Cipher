<?php

declare(strict_types=1);

use peterurk\Cipher\Cipher;
use PHPUnit\Framework\TestCase;

final class CipherTest extends TestCase
{
    public function testEncryptAndDecrypt(): void
    {
        $cipher = new Cipher('secret');
        $encrypted = $cipher->encrypt('hello');
        $this->assertNotSame('hello', $encrypted);
        $this->assertSame('hello', $cipher->decrypt($encrypted));
    }

    public function testInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Cipher('');
    }

    public function testInvalidBase64(): void
    {
        $cipher = new Cipher('key');
        $this->expectException(InvalidArgumentException::class);
        $cipher->decrypt('@@@not base64@@@');
    }
}
