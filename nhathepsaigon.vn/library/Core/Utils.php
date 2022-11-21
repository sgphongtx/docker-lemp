<?php

/**
 * Class Core_Utils
 */
class Core_Utils
{
    public static $arrImgDone = array();
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


    /**
     * @param $number
     * @return int|string
     */
    public static function numberFormat($number)
    {
        return $number > 0 ? number_format($number, 0, '.', ',') : 0;
    }

    /**
     * HTML entities
     * @param string $string
     * @return string
     */
    public static function htmlEntities($string)
    {
        return str_replace(array("<", ">", '"', '\''), array("&lt;", "&gt;", "&quot;", "&#039;"), $string);
    }

    /**
     * HTML entities decode
     * @param string $string
     * @return string
     */
    public static function htmlEntitiesDecode($string)
    {
        return str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&#039;"), array('&', "<", ">", '"', '\''), $string);
    }

    public static function convertDateToDisplay($date, $format = "d/m/Y")
    {
        return date($format, strtotime($date));
    }

    /**
     * @param int $isDate
     * @param int $isFromDate
     * @param int $numDays
     * @param int $showTime
     * @param string $unit
     * @param string $format
     * @return string
     */
    public static function getDate($isDate = 0, $isFromDate = 1, $numDays = 0, $showTime = 1, $unit = 'days', $format = 'Y-m-d')
    {
        if (is_null($isDate)) {
            return null;
        }

        $now = date($format);
        if ($numDays == 0) {
            return (!empty($isDate) ? $isDate : $now) . ($showTime == 1 ? $isFromDate == 1 ? ' 00:00:00' : ' 23:59:59' : null);
        }
        $date = new DateTime(!empty($isDate) ? $isDate : $now);
        $date->add(DateInterval::createFromDateString($numDays . ' ' . $unit));
        return $date->format($format) . ($showTime == 1 ? $isFromDate == 1 ? ' 00:00:00' : ' 23:59:59' : null);
    }

    /**
     * Generate SEO string
     * @param <type> $str
     * @return <type>
     */
    public static function createSeoStr($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/[^a-zA-Z0-9-]+/", '-', $str);
        $str = preg_replace("/[-]+/", '-', $str);
        $str = preg_replace("/^-+|-+$/", '', $str);
        return $str;
    }

    /**
     * @param $str
     * @param string $separate
     * @return string
     */
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
     * @param $max_width
     * @param $max_height
     * @param $source_file
     * @param $dst_dir
     * @param int $quality
     */
    //resize and crop image by center
    public static function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 100)
    {
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 9;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                //$quality = 100;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        } else {
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if ($dst_img) imagedestroy($dst_img);
        if ($src_img) imagedestroy($src_img);
    }

    /**
     * @name        :   cropImage
     * @author      :   PhongTX
     * @copyright   :   Fpt Online
     */
    /* Library of scale crop image */
    public static function scaleCropImage($options, $max_width, $max_height, $source_file, $dst_dir, $quality = 100)
    {
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch ($mime) {
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 9;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                //$quality = 100;
                break;

            default:
                return false;
                break;
        }

        /*scale*/
        $new_width = $max_width_scale = $img_width = $width;
        $new_height = $max_height_scale = $img_height = $height;
        $crop = !empty($options['crop']);
        $x = !empty($options['crop_x']) ? floatval($options['crop_x']) : 0;
        $y = !empty($options['crop_y']) ? floatval($options['crop_y']) : 0;
        if (isset($options['scale'])) {
            $scale = floatval($options['scale']);
            $x = $x / $scale;
            $y = $y / $scale;
            $max_width_scale = $new_width / $scale;
            $max_height_scale = $new_height / $scale;
        }
        $success = true;
        if ($crop) {
            if (!isset($options['crop_x']) || !isset($options['crop_y'])) {
                $x = 0;
                $y = 0;
                if (($img_width / $img_height) >= ($max_width_scale / $max_height_scale)) {
                    //$new_width = 0; // Enables proportional scaling based on max_height
                    $max_width_scale = $max_width_scale * $img_height / $max_height_scale;
                    $max_height_scale = $img_height;
                    $x = ($img_width / ($img_height / $max_height_scale) - $max_width_scale) / 2;
                } else {
                    //$new_height = 0; // Enables proportional scaling based on max_width
                    $max_height_scale = $img_width * $max_height_scale / $max_width_scale;
                    $max_width_scale = $img_width;
                    $y = 0;
                }

            }
        }
        /*End scale*/

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if ($width_new > $width) {
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $y, $max_width, $max_height, $width, $height_new);
        } else {
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $x, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if ($dst_img) imagedestroy($dst_img);
        if ($src_img) imagedestroy($src_img);
    }

    /**
     * @param $str
     * @return mixed
     */
    public static function vn_str_filter($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
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
     * @param $match
     * @param $domain
     * @param $domain1
     * @param $domain2
     * @return string
     */
    public static function setImageContent($match, $domain, $domain1, $domain2)
    {
        $src = trim($match[2]);
        $arrSrc = explode('/', $src);
        if ($domain == 'http://pose.com.vn' || $domain == 'http://dep.com.vn' || $domain == 'http://nuathegioi.com' || $domain == 'http://baophunuonline.net') {
            if (!in_array($domain1, $arrSrc) && !in_array($domain2, $arrSrc)) {
                $src = $domain . $src;
            }
        }
        //return "<p style=\"text-align: center;\"><img class='lazy' style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . $src . "\" data-original=\"" . $src . "\"/></p>";
        return "<img class='lazy' style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . $src . "\" data-original=\"" . $src . "\"/>";
    }

    /**
     * @param $match
     * @return string
     */
    public static function getimage($match, $module = 'backend')
    {
        $src = trim($match[2]);
        $md5 = md5($src);
        if (!isset(self::$arrImgDone[$md5])) {
            if (strpos($src, 'http') === 0) {
                if (strpos($src, SITE_URL) !== 0) {
                    $arrInfo = pathinfo($src);
                    $ext = in_array($arrInfo['extension'], array('jpg', 'png')) ? $arrInfo['extension'] : 'jpg';
                    $filename = $arrInfo['filename'] . '_' . mt_rand(1, 1000) . time() . '.' . $ext;
                    if ($module == 'backend') {
                        $url = STATIC_URL . '/uploads/images/' . date('Y/m') . '/' . $filename;
                        $up_source_dir = IMAGE_UPLOAD_DIR . '/images/originals/';
                        $path = IMAGE_UPLOAD_DIR . '/images/';
                    }
                    if ($module == 'fontend') {
                        $url = STATIC_URL . '/uploads/users/images/' . date('Y/m') . '/' . $filename;
                        $up_source_dir = IMAGE_UPLOAD_DIR . '/users/images/originals/';
                        $path = IMAGE_UPLOAD_DIR . '/users/images/';
                    }
                    $year = array(date('Y'), date('m'));
                    if (!is_dir($up_source_dir)) {
                        mkdir($up_source_dir, 0755, true);
                    }
                    foreach ($year as $row) {
                        $up_source_dir .= $row;
                        if (!is_dir($up_source_dir)) {
                            mkdir($up_source_dir, 0755, true);
                        }
                        $up_source_dir .= DIRECTORY_SEPARATOR;
                    }
                    $up_source_dir .= '/' . $filename;
                    foreach ($year as $row) {
                        $path .= $row;
                        if (!is_dir($path)) {
                            $ok = mkdir($path, 0755, true);
                        }
                        $path .= DIRECTORY_SEPARATOR;
                    }
                    $path .= '/' . $filename;
                    try {
                        $content = file_get_contents($src);
                        file_put_contents($up_source_dir, $content);
                        self::$arrImgDone[$md5] = $up_source_dir;
                        $imgsize = getimagesize($up_source_dir);
                        $width = $imgsize[0];
                        $height = $imgsize[1];
                        if ($width > 663) {
                            $height = ceil(663 * $height / $width);
                            $width = 663;
                        }
                        self::resize_crop_image($width, $height, $up_source_dir, $path);
                    } catch (Zend_Exception $e) {
                        return '';
                    }
                    return "<img style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . $url . "\"/>";
                } else {
                    return $match[0];
                }
            } else {
                return '';
            }
        } else {
            return "<img style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . self::$arrImgDone[$md5] . "\"/>";
        }
    }

    /**
     * cleanFileByTime
     */
    public static function cleanFileByTime($path,$time)
    {
        $seconds_old = $time;
        $directory = $path;
        if( !$dirhandle = @opendir($directory) )
            return;
        while( false !== ($filename = readdir($dirhandle)) ) {
            if( $filename != "." && $filename != "..") {
                $filename = $directory. "/". $filename;
                if( @filemtime($filename) < (time()-$seconds_old)){
                    @unlink($filename);
                }
            }
        }
    }

    /**
     * cleanFileCaptchaByTime
     */
    public static function cleanFileCaptchaByTime() {
        $seconds_old = 60;
        $directory = IMAGE_CAPTCHA_DIR;
        if( !$dirhandle = @opendir($directory) )
            return;
        while( false !== ($filename = readdir($dirhandle)) ) {
            if( $filename != "." && $filename != "..") {
                $filename = $directory. "/". $filename;
                if( @filemtime($filename) < (time()-$seconds_old)){
                    @unlink($filename);
                }
            }
        }
    }

    /**
     * @param $length
     * @param $nums
     * @return string
     */
    public static function genString($length, $nums)
    {
        $lowLet = "abcdefghijklmnopqrstuvwxyz";
        $highLet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $numbers = "123456789";
        $pass = "";
        $i = 1;
        While ($i <= $length) {
            $type = rand(0, 1);
            if ($type == 0) {
                if (($length - $i + 1) > $nums) {
                    $type2 = rand(0, 1);
                    if ($type2 == 0) {
                        $ran = rand(0, 25);
                        $pass .= $lowLet[$ran];
                    } else {
                        $ran = rand(0, 25);
                        $pass .= $highLet[$ran];
                    }
                } else {
                    $ran = rand(0, 8);
                    $pass .= $numbers[$ran];
                    $nums--;
                }
            } else {
                if ($nums > 0) {
                    $ran = rand(0, 8);
                    $pass .= $numbers[$ran];
                    $nums--;
                } else {
                    $type2 = rand(0, 1);
                    if ($type2 == 0) {
                        $ran = rand(0, 25);
                        $pass .= $lowLet[$ran];
                    } else {
                        $ran = rand(0, 25);
                        $pass .= $highLet[$ran];
                    }
                }
            }
            $i++;
        }
        return $pass;
    }

    /**
     * @param $string
     * @param $keyphrase
     * @return string
     */
    public static function keyED($string, $keyphrase)
    {
        $string = (string)$string;
        $keyphraseLength = strlen($keyphrase);
        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; $i++) {
            $rPos = $i % $keyphraseLength;
            $r = ord($string[$i]) ^ ord($keyphrase[$rPos]);
            $string[$i] = chr($r);
        }
        return $string;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public static function encode($string, $keyphrase)
    {
        $string = self::keyED($string, $keyphrase);
        $string = base64_encode($string);
        return $string;
    }

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public static function decode($string, $keyphrase)
    {
        $string = base64_decode($string);
        $string = self::keyED($string, $keyphrase);
        return $string;
    }

}
