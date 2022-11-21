<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 27/04/2015
 * Time: 10:35 SA
 */
if (empty($GLOBALS['HTMLPurifier_ROOT']))
{
    $GLOBALS['HTMLPurifier_ROOT'] = realpath(realpath(dirname(__FILE__)) . '/Filter/HTMLPurifier');
}

require_once $GLOBALS['HTMLPurifier_ROOT'] . '/HTMLPurifier.includes.php';
require_once $GLOBALS['HTMLPurifier_ROOT'] . '/HTMLPurifier.autoload.php';
class Core_Filter
{
    /**
     * HTMLPurifier instances
     * @var HTMLPurifier
     */
    private static $_htmlPurifier = array();
    /**
     * List Vietnamese lower
     * @var <array>
     */
    public static $arrLower = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "đ", "é", "è", "ẽ", "ẻ", "ẹ", "ê", "ễ", "ề", "ế", "ệ", "ể", "ú", "ù", "ũ", "ụ", "ủ", "ư", "ừ", "ứ", "ữ", "ử", "ự", "ó", "ò", "ỏ", "õ", "ọ", "ơ", "ờ", "ớ", "ở", "ỡ", "ợ", "ô", "ố", "ồ", "ổ", "ỗ", "ộ", "á", "à", "ả", "ã", "ạ", "â", "ẩ", "ẫ", "ậ", "ấ", "ầ", "ă", "ắ", "ằ", "ẵ", "ẳ", "ặ", "í", "ì", "ỉ", "ĩ", "ị", "ý", "ỳ", "ỷ", "ỹ", "ỵ");

    /**
     * List Vietnamese upper
     * @var <array>
     */
    public static $arrUpper = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "Đ", "É", "È", "Ẽ", "Ẻ", "Ẹ", "Ê", "Ễ", "Ề", "Ế", "Ệ", "Ể", "Ú", "Ù", "Ũ", "Ụ", "Ủ", "Ư", "Ừ", "Ứ", "Ữ", "Ử", "Ự", "Ó", "Ò", "Ỏ", "Õ", "Ọ", "Ơ", "Ờ", "Ớ", "Ở", "Ỡ", "Ợ", "Ô", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Á", "À", "Ả", "Ã", "Ạ", "Â", "Ẩ", "Ẫ", "Ậ", "Ấ", "Ầ", "Ă", "Ắ", "Ằ", "Ẵ", "Ẳ", "Ặ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ");

    public static function setSeoLink($str, $separate = '-')
    {
        $arrReg = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#siu',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#siu',
            '#(ì|í|ị|ỉ|ĩ)#siu',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#siu',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#siu',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#siu',
            '#(đ)#siu',
            '#([^a-zA-Z0-9]+)#i'
        );
        $arrFind = array(
            'a',
            'e',
            'i',
            'o',
            'u',
            'y',
            'd',
            $separate
        );
        return strtolower(trim(preg_replace($arrReg, $arrFind, $str), $separate));
    }

    /**
     * cut UTF8 string return full string
     * @param <string> $string
     * @param <int> $start
     * @param <int> $len
     * @param <string> $charlim
     * @return <string>
     */
    public static function subFullString($string, $start = 0, $len = 20, $charlim = '...')
    {
        //Strip tags html
        $string = strip_tags($string);
        if (mb_strlen($string, 'UTF-8') <= $len)
            return $string;
        $string = mb_substr($string, 0, $len, 'UTF-8');
        preg_match('/\s[^\s]+$/u', $string, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches)) {
            $string = mb_substr($string, 0, $matches[0][1]) . ' ';
        }
        return $string . $charlim;
    }

    /**
     * Convert string to upper
     * @param <string> $string
     * @return <string>
     */
    public static function lowerToUpper($string)
    {
        return str_replace(self::$arrLower, self::$arrUpper, $string);
    }

    /**
     * Convert string to lower
     * @param <string> $string
     * @return <string>
     */
    public static function upperTolower($string)
    {
        return str_replace(self::$arrUpper, self::$arrLower, $string);
    }

    /**
     * Remove xss by HTMLPurifier
     * @param <string> $config
     * @return <string>
     */
    static public function purify($string, $config = array(),$type = 'content') // <p></p> <p> </p> <p>&nbsp;</p>
    {
        $string = trim($string);
        if(empty($string)) return null;
        $matches = NULL;
        if (preg_match_all('#<p.*>(.*)</p>#U', $string, $matches))
        {
            foreach ($matches[1] as $idx => $value)
            {
                $value = trim($value);
                if (empty($value) || preg_match('/^(&nbsp;)+$/', $value))
                {
                    $string    = str_replace($matches[0][$idx], '', $string);
                }
            }
        }
        // remove img[height] img[style:height]
        /*$string = preg_replace_callback(array('/(height)=(\'|\")\d*(\'|\")\s/esiU','/(height):(.*)\d(.*)px/esiU'), function () {
            return '';
        }, $string);*/
        // remove img[height] img[style:height]
        /*if (preg_match('/<img[^>]+>/ims', $string, $matches)) {
            foreach ($matches as $match) {
                // Replace all occurences of width/height
                $clean = preg_replace(array('/(height)=(\'|\")\d*(\'|\")\s/esiU','/(height):(.*)\d(.*)px/esiU'), "", $match);
                $string = str_replace($match, $clean, $string);
            }
        }*/

        //$string = self::replaceCaption($string);

        $config = count($config) ? HTMLPurifier_Config::create($config) : HTMLPurifier_Config::createDefault();
        $config->set('Filter.YouTube', true);
        $config->set('HTML.SafeObject', true);
        $config->set('HTML.SafeIframe', true);
        $config->set('HTML.SafeEmbed', true);
        $config->set('HTML.FlashAllowFullScreen', true);
        $config->set('CSS.ForbiddenProperties', array('font-size' => true,
            'font-family' => true,
            'font-style' => true,
            'font-variant' => true,
            'font-weight' => true,
            'font' => true
        ));
        $config->set('HTML.ForbiddenElements', array('font'));

        $def = $config->getHTMLDefinition(true);

        $def->addAttribute('a', 'data-checked', 'CDATA');
        $def->addAttribute('a', 'data-component', 'CDATA');
        $def->addAttribute('a', 'data-component-value', 'CDATA');
        $def->addAttribute('a', 'data-component-type', 'CDATA');
        $def->addAttribute('a', 'data-component-width', 'CDATA');
        $def->addAttribute('a', 'data-component-height', 'CDATA');
        $def->addAttribute('a', 'target', 'CDATA');
        $def->addAttribute('a', 'rel', 'CDATA');
        $def->addAttribute('a', 'onclick', 'CDATA');

        $def->addAttribute('img', 'data-component', 'CDATA');
        $def->addAttribute('img', 'data-component-value', 'CDATA');
        $def->addAttribute('img', 'data-component-type', 'CDATA');
        $def->addAttribute('img', 'data-component-width', 'CDATA');
        $def->addAttribute('img', 'data-component-height', 'CDATA');
        $def->addAttribute('img', 'data-natural-width', 'CDATA');
        $def->addAttribute('img', 'data-component-typevideo', 'CDATA');
        $def->addAttribute('img', 'data-component-siteid', 'CDATA');
        $def->addAttribute('img', 'data-component-price', 'CDATA');
        $def->addAttribute('img', 'data-component-layout', 'CDATA');
        $def->addAttribute('img', 'data-zoom', 'CDATA');
        $def->addAttribute('img', 'data-cke-realelement', 'CDATA');
        $def->addAttribute('img', 'data-title', 'CDATA');

        $def->addAttribute('div', 'id', 'CDATA');
        $def->addAttribute('div', 'data-component', 'CDATA');
        $def->addAttribute('div', 'data-component-value', 'CDATA');
        $def->addAttribute('div', 'data-component-type', 'CDATA');
        $def->addAttribute('div', 'data-component-typevideo', 'CDATA');
        $def->addAttribute('div', 'data-component-siteid', 'CDATA');
        $def->addAttribute('div', 'data-component-realdata', 'CDATA');
        $def->addAttribute('div', 'data-component-layout', 'CDATA');
        $def->addAttribute('div', 'data-title', 'CDATA');
        $def->addAttribute('div', 'data-href', 'CDATA');
        $def->addAttribute('div', 'data-width', 'CDATA');
        $def->addAttribute('div', 'contenteditable', 'CDATA');
        $def->addAttribute('div', 'data-id', 'CDATA');

        $def->addAttribute('table', 'data-template-display', 'CDATA');

        $def->addAttribute('blockquote', 'width', 'CDATA');
        $def->addAttribute('li', 'data-id', 'CDATA');

        $def->addAttribute('span', 'id', 'CDATA');

        $def->addAttribute('iframe', 'allowfullscreen', 'CDATA');
        $def->addAttribute('iframe', 'src', 'CDATA');

        $def->addAttribute('embed', 'src', 'CDATA');
        $def->addAttribute('embed', 'pluginspage', 'CDATA');

        $def->addAttribute('object', 'data', 'CDATA');
        $def->addAttribute('object', 'codebase', 'CDATA');
        $def->addAttribute('object', 'classid', 'CDATA');
        $def->addAttribute('object', 'data-component', 'CDATA');

        $def->addAttribute('td', 'style', 'CDATA');
        $def->addElement('figure', 'Block', 'Inline', 'Common');

        $purify = new HTMLPurifier($config);

        return $purify->purify($string);
    }

}

?>