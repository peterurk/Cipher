<?php
/**
 * Encryption test
 */
require 'Cipher.php';

// First init the class by calling the constructor
// Pass your personal key to the constructor as a
// parameter.
$cipher = new Cipher('AvErrySeCretPasSw0rd!1!2!3!');

// Your previously encrypted string
$encrypted = '';

// Now we are going to decrypt it.
// $decrypted = $cipher->decrypt($encrypted);
