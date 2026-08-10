<?php

/**
 * CI3-compatible Encryption wrapper over the CI4 Encrypter.
 */
#[AllowDynamicProperties]
class CI_Encryption
{
    /** @var \CodeIgniter\Encryption\Encrypter */
    protected $encrypter;

    public function __construct($params = [])
    {
        $this->encrypter = service('encrypter');
    }

    public function encrypt($data)
    {
        return base64_encode($this->encrypter->encrypt($data));
    }

    public function decrypt($data)
    {
        try {
            return $this->encrypter->decrypt(base64_decode((string) $data));
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function encrypt_b64($data)
    {
        return $this->encrypt($data);
    }

    public function decrypt_b64($data)
    {
        return $this->decrypt($data);
    }
}
