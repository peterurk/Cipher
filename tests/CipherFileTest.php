<?php

declare(strict_types=1);

use peterurk\Cipher\Cipher;
use PHPUnit\Framework\TestCase;

final class CipherFileTest extends TestCase
{
    public function testEncryptAndDecryptFile(): void
    {
        $cipher = new Cipher('secret');
        $input = tempnam(sys_get_temp_dir(), 'plain');
        $enc = tempnam(sys_get_temp_dir(), 'enc');
        $out = tempnam(sys_get_temp_dir(), 'out');
        file_put_contents($input, 'filecontent');
        $cipher->encryptFile($input, $enc);
        $cipher->decryptFile($enc, $out);
        $this->assertSame('filecontent', file_get_contents($out));
        unlink($input);
        unlink($enc);
        unlink($out);
    }
}
