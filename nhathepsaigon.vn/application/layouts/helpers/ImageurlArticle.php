<?php

/**
 * @todo helper generate url image with size
 * @param <string> $strPath img original path
 * @return <string> url image with size
 * @author PhongTX
 */
class Zend_View_Helper_ImageurlArticle extends Zend_View_Helper_Abstract
{
    protected $config;

    public function __construct()
    {
        $config       = Core_Global::getApplicationIni();
        $this->config = $config['images'];
    }

    public function ImageurlArticle($article, $strSize = 'size1')
    {
        //Set $strPath
        $strPath    = isset($article['thumb_url'])&&$article['thumb_url']!=''?$article['thumb_url']:(isset($article['strThumbUrl'])&&$article['strThumbUrl']!=''?$article['strThumbUrl']:'no-image.png');
        //Check config ini
        if ( array_key_exists($strSize, $this->config) )
        {
            $size   = $this->config[$strSize];
        }
        else
        {
            $size   = '60x60';
        }//end if
        //Return
        return STATIC_URL. '/uploads/thumbnails/thumbs/' . preg_replace('/\.[^.]+$/', (empty($size) ? '' : '_' . $size) . '$0', $strPath);
    }
}