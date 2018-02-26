<?php
class Custom_Controller_Plugin_DesCrypt{
    /**
     * 3DES加密解密  
     * Created on 2010-11-02 by Tianhm
     *  
     */
    private $key; //APIkey
    private $deviceid;
    private $user;
    private $lsh;
    private $cipherText;
    private $HcipherText;
    private $decrypted_data;

    public function __construct($key = 'v*&$@#!%') {
        $this->key = $key; //APIkey
        $this->deviceid = '';
        $this->user = '';
        $this->lsh = '';
        $this->cipherText = '';
        $this->HcipherText = '';
        $this->decrypted_data = '';
    }

    //加密
    public function en($str) {
        $cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        if (mcrypt_generic_init($cipher, substr($this->key, 0, 8), $iv) != -1) {
            $this->cipherText = mcrypt_generic($cipher, $this->pad($str));
            mcrypt_generic_deinit($cipher);
            // 以十六进制字符显示加密后的字符
            $this->HcipherText = bin2hex($this->cipherText);
            //printf("<p>3DES encrypted:\n%s</p>",$this->cipherText);
            //printf("<p>3DES HexEncrypted:\n%s</p>",$this->HcipherText);
        }
        mcrypt_module_close($cipher);
        return $this->HcipherText;
    }

    //解密
    public function de($str) {
        $str = pack('H*', $str);
        $cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB), MCRYPT_RAND);
        if (mcrypt_generic_init($cipher, substr($this->key, 0, 8), $iv) != -1) {
            $this->decrypted_data = mdecrypt_generic($cipher, $str);
            mcrypt_generic_deinit($cipher);
        }
        mcrypt_module_close($cipher);
        return $this->unpad($this->decrypted_data);
    }

    private function pad($data) {
        $data = str_replace("\n", "", $data);
        $data = str_replace("\t", "", $data);
        $data = str_replace("\r", "", $data);

        $text_add = strlen($data) % 8;
        for ($i = $text_add; $i < 8; $i++) {
            $data .= chr(8 - $text_add);
        }
        return $data;
    }

    private function unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, - 1 * $pad);
    }

    public function __destruct() {
        unset($this->key);
        unset($this->deviceid);
        unset($this->user);
        unset($this->lsh);
        unset($this->cipherText);
        unset($this->HcipherText);
        unset($this->decrypted_data);
    }

}
