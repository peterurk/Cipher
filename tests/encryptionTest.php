<?php
/**
 * Encryption test
 */
require 'Cipher.php';

// First init the class by calling the constructor
// Pass your personal key to the constructor as a
// parameter.
$cipher = new Cipher('AvErrySeCretPasSw0rd!1!2!3!');

$text = 'This is the piece of text we are going to encrypt.';

// Now we encrypt the above text
// The returned value will be a base64encoded form of your encrypted 
// string.
$encrypted = $cipher->encrypt($text);

