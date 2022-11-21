<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Crypt_Adapter_Xor
 * @version     :   211012
 * @copyright   :   Fpt Online
 * @todo        :   Using to crypt
 */
class Core_Crypt_Adapter_Xor extends Core_Crypt_Adapter_Abstract
{

    /**
     * Gen key private
     * @param <string> $string
     * @return <string>
     */
    public function keyED($string)
    {
        $string = (string)$string;
        $keyphraseLength = strlen($this->_keyphrase);
        $stringLength = strlen($string);
        for($i = 0; $i < $stringLength; $i++)
        {
            $rPos = $i % $keyphraseLength;
            $r = ord($string[$i]) ^ ord($this->_keyphrase[$rPos]);
            $string[$i] = chr($r);
        }
        return $string;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @return <string>
     */
    public function encode($string)
    {        
        $string = $this->keyED($string);
        $string = base64_encode($string);
        return $string;
    }

    /**
     * decrypt string
     * @param <string> $string
     * @return <string>
     */
    public function decode($string)
    {        
        $string = base64_decode($string);
        $string = $this->keyED($string);
        return $string;
    }
}

