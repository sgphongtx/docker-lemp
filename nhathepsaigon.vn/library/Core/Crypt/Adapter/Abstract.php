<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Crypt_Adapter_Abstract
 * @version     :   211012
 * @copyright   :   Fpt Online
 * @todo        :   Using to crypt
 */
abstract class Core_Crypt_Adapter_Abstract
{
    protected $_keyphrase =null;

    public function __construct($strKeyPhrase){
        $this->_keyphrase = $strKeyPhrase;
    }
    /**
     * Gen key private
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function keyED($string);

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function encode($string);

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function decode($string);
}

