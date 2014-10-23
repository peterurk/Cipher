<?php
/**
 * Cipher Class
 *
 * @author Peter Post <peter@itechnics.nl>
 */
class Cipher
{

	/**
	 * SHA256 Encrypted Key
	 * @var string
	 */
    private $encryptedKey;

    /**
     * Initial vector
     *
     * Used to seed the encryption string
     *
     * @var string
     */
    private $initVector;

    /**
     * Constructor
     * @param boolean|string $personalKey Holds the personal key to use in encryption
     */
    public function __construct($personalKey = false)
    {
    	if (false === $personalKey) {
    		throw new Exception("A personal key is required for encryption/decryption", 1);
    	}

        $this->encryptionKey = hash('sha256', $personalKey, true);
	    $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
	    $this->initVector = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
    }

    /**
     * Encrypt a string
     * @param  mixed $input  Data to encrypt
     * @return string        Encrypted data
     */
    public function encrypt($input)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->encryptionKey, $input, MCRYPT_MODE_ECB, $this->initVector));
    }

    /**
     * Decrypt string
     * @param  string $input Encrypted string we are going to decrypt
     * @return string        Decrypted output
     */
    public function decrypt($input)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->encryptionKey, base64_decode($input), MCRYPT_MODE_ECB, $this->initVector));
    }
}
