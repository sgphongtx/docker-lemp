<?php

/**
 * @author   : PhongTX
 * @name : CropWord
 * @copyright   : FPT Online
 * @todo    : Helper CropWord
 */
class Zend_View_Helper_CropWord extends Zend_View_Helper_Abstract
{

    //	Public	function
    public function CropWord($text = '', $numWord = 0, $link = '')
    {
        //Dem tu
        //$wordCount = str_word_count(($text));
        $wordCount = str_word_count(Core_Global::vnToAscii($text), 0, '0123456789`~!@#$%^&*()-_=+\\|[{]};:\'",<.>/?');
        if ($wordCount <= $numWord)
        {
            return $text;
        }
        else
        {
            $arrWord = explode(' ', $text);
            foreach ($arrWord as $word)
            {
                if ($word === '0' || $word)
                {
                    $tmp[] = $word;
                }
            }
            $str = implode(' ', array_slice($tmp, 0, $numWord));
            $str = $str . '...';
            return $str;
        }
    }

}