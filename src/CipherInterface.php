<?php
namespace peterurk\Cipher;

interface CipherInterface
{
    public function encrypt(string $input): string;
    public function decrypt(string $input): string;
}
