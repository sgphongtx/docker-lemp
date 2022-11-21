<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Crypt_Adapter_Secret
 * @version     :   211012
 * @copyright   :   Fpt Online
 * @todo        :   Using to crypt
 */
class Core_Crypt_Adapter_Secret extends Core_Crypt_Adapter_Abstract
{
        
    /**
     * Generate character
     * @return <string> 
     */
    private function getRndIv()
    {
        $iv_len = 16;
        $iv = '';
        
        //Loop data
        while($iv_len-- > 0)
        {
            $iv .= chr(mt_rand() & 0xff);
        }
        
        //Return data
        return $iv;
    }
    
    /**
     * encrypt string
     * @param <string> $plain_text
     * @param <string> $keyphrase
     * @return <string>
     */
    public function encode($plain_text)
    {       
        //Generate character
        $plain_text .= "\x13";
        $n = strlen($plain_text);
        $plain_text .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;
        $enc_text = $this->getRndIv();
        $iv = substr($keyphrase ^ $enc_text, 0, 512);
        
        //Loop data
        while($i < $n)
        {
            $block = substr($plain_text, $i, 16) ^ pack('H*', sha1($iv));
            $enc_text .= $block;
            $iv = substr($block . $iv, 0, 512) ^ $keyphrase;
            $i += 16;
        }
        
        //Return base64 string
        return base64_encode($enc_text);
    }

    /**
     * decrypt string
     * @param <string> $enc_text
     * @param <string> $keyphrase
     * @return <string>
     */
    public function decode($enc_text)
    {        
        //Generate character
        $enc_text = base64_decode($enc_text);
        $n = strlen($enc_text);
        $i = 16;
        $plain_text = '';
        $iv = substr($keyphrase ^ substr($enc_text, 0, 16), 0, 512);
        
        //Loop data
        while($i < $n)
        {
            $block = substr($enc_text, $i, 16);
            $plain_text .= $block ^ pack('H*', sha1($iv));
            $iv = substr($block . $iv, 0, 512) ^ $keyphrase;
            $i += 16;
        }
        
        //Return string
        return stripslashes(preg_replace('/\\x13\\x00*$/', '', $plain_text));
    }
}