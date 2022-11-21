<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Crypt_Adapter_Base64
 * @version     :   211012
 * @copyright   :   Fpt Online
 * @todo        :   Using to crypt
 */
class Core_Crypt_Adapter_Base64 extends Core_Crypt_Adapter_Abstract
{

    /**
     * Gen key private
     * @param <string> $string
     * @return <string>
     */
    public function keyED($string)
    {
        $string = (string)$string;
        $keyphrase = md5($this->_keyphrase);
        $ctr=0;
        $tmp = '';
        $stringLength = strlen($string);
        $keyphraseLength = strlen($keyphrase);
        for($i=0;$i<$stringLength;$i++)
        {
            if($ctr==$keyphraseLength)
            {
                $ctr=0;
            }
            $tmp .= substr($string,$i,1) ^ substr($keyphrase,$ctr,1);
            $ctr++;
        }
        return $tmp;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @return <string>
     */
    public function encode($string)
    {
        srand((double)microtime()*1000000);
        $encrypt_key = md5(rand(0,32000));
        $ctr=0;
        $tmp = '';
        $stringLength = strlen($string);
        $encryptLength = strlen($encrypt_key);
        for($i=0;$i<$stringLength;$i++)
        {
            if($ctr==$encryptLength)
            {
                $ctr=0;
            }
            $tmp.= substr($encrypt_key,$ctr,1) . (substr($string,$i,1) ^ substr($encrypt_key,$ctr,1));
            $ctr++;
        }
        return base64_encode($this->keyED($tmp));
    }

    /**
     * decrypt string
     * @param <string> $string
     * @return <string>
     */
    public function decode($string)
    {
        $string = $this->keyED(base64_decode($string));
        $tmp = '';
        $stringLength = strlen($string);
        for($i=0;$i<$stringLength;$i++)
        {
            $md5 = substr($string,$i,1);
            $i++;
            $tmp.= (substr($string,$i,1) ^ $md5);
        }
        return $tmp;
    }
}

