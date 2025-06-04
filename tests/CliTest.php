<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CliTest extends TestCase
{
    private string $binary;

    protected function setUp(): void
    {
        $this->binary = __DIR__ . '/../bin/cipher';
    }

    public function testEncryptAndDecryptViaCli(): void
    {
        $output = shell_exec("{$this->binary} --encrypt hello --key secret");
        $this->assertNotFalse($output);
        $encrypted = trim($output);
        $decrypted = shell_exec("{$this->binary} --decrypt {$encrypted} --key secret");
        $this->assertSame("hello\n", $decrypted);
    }
}
