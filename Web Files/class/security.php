<?php
class security
{
    private $_db = "";
    function __construct($db)
    {
        $this->_db = $db;
    }

    public function encrypt($plaintext)
    {
        $plaintext = $this->generateRandomString(5) . $plaintext . $this->generateRandomString(48);
        $password = E_PASSWORD;
        $password = substr(hash('sha256', $password, true), 0, 32);
        return base64_encode(openssl_encrypt($plaintext, E_METHOD, $password, OPENSSL_RAW_DATA, E_IV));
    }

    public function generateRandomString($length = 4)
    {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= E_RNDM_STR[rand(0, strlen(E_RNDM_STR) - 1)];
        }
        return $randomString;
    }

    public function generateUserID($length = 16)
    {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= "0123456789"[rand(0, strlen("0123456789") - 1)];
        }
        return $randomString;
    }

    public function exportEcho($message)
    {
        return $this->encryptRJ256($this->generateRandomString(10) . '|' . $message . '|' . date('Y-m-d H:i:s'));
    }

    public function encryptRJ256($string_to_encrypt)
    {
        $password = substr(hash('sha256', E_ENKEY, true), 0, 32);
        $encrypted = openssl_encrypt($string_to_encrypt, E_METHOD, $password, OPENSSL_RAW_DATA, E_IV);
        $rtn = base64_encode($encrypted);
        return ($rtn);
    }

    public function clear_input($input)
    {
        $input = ltrim($input);
        $input = rtrim($input);
        $input = trim($input);
        $input = $this->decryptRJ256($input);
        $input = filter_var($input, FILTER_SANITIZE_STRING);
        $input = mysqli_real_escape_string($this->_db, $input);
        return $input;
    }

    public function decryptRJ256($string_to_decrypt)
    {
        $password = substr(hash('sha256', E_ENKEY, true), 0, 32);
        $decrypted = openssl_decrypt(base64_decode($string_to_decrypt), E_METHOD, $password, OPENSSL_RAW_DATA, E_IV);
        return $decrypted;
    }

    public function clear_input_email($input)
    {
        $input = ltrim($input);
        $input = rtrim($input);
        $input = trim($input);
        $input = filter_var($input, FILTER_SANITIZE_STRING);
        $input = filter_var($input, FILTER_SANITIZE_EMAIL);
        if (!filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            return $input;
        } else {
            return false;
        }
    }

    public function clear_input_name($input)
    {
        $input = ltrim($input);
        $input = rtrim($input);
        $input = trim($input);
        $input = filter_var($input, FILTER_SANITIZE_STRING);
        if (!ctype_alnum($input) || strlen($input) < 4 || strlen($input) > 15) {
            return false;
        } else {
            return $input;
        }
    }
}