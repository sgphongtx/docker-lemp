<?php

/**
 * 
 * @author PhongTX
 */
class Core_Crawler_EvaVn extends Core_Crawler_Abstract
{
    /**
     * Parse HTML to article parts
     * @param string $html
     * @return array 
     */
    public static function parse($html)
    {
        /**
         * Response format
         */
        $response = array(
            'status' => 1,
            'data' => array(
                'title' => '',
                'lead' => '',
                'content' => ''
            )
        );
        if($html == null){
            $response['data']['title'] = "Can't load content now";
            return $response;
        }

        /*$doc = new DOMDocument();
        // load the HTML string we want to strip
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        //get DOM
        $dom = new Zend_Dom_Query(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));*/

        //get DOM
        $dom = new Zend_Dom_Query($html);

        /**
         * Get title
         */
        $titleNode = $dom->query('h1.baiviet-title');

        foreach ($titleNode->current()->childNodes as $child)
        {
            $response['data']['title'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['title'] = Core_Filter::purify($response['data']['title']);

        /**
         * Get lead
         */
        $leadNode = $dom->query('p.baiviet-sapo');

        foreach ($leadNode->current()->childNodes as $child)
        {
            $response['data']['lead'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['lead'] = Core_Filter::purify($response['data']['lead']);
        /**
         * Get content
         */

        $contentNode = $dom->query('#baiviet-container');

        foreach ($contentNode->current()->childNodes as $child)
        {
            $response['data']['content'] .= $child->ownerDocument->saveXML($child);
        }
        //Loai bo chu thich html
        $response['data']['content'] = preg_replace('<!--(.*?)-->','',$response['data']['content']);
        //Loai bo styel html
        $response['data']['content'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1',$response['data']['content']);
        //Remove bai viet lien quan top
        $response['data']['content'] = preg_replace('/<div class="baiviet-bailienquan pink-box-bg-light">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        //Remove bai viet lien quan bottom
        $response['data']['content'] = preg_replace('/<div class="baiviet-bailienquan baiviet-bailienquan-bottom">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div class="bailienquan-trangtrong">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('#<img[^>]+src="http://eva-static.24hstatic.com/images/2014/share-fb.gif" (.*)(\\1).*>#siU','',$response['data']['content']);
        $response['data']['content'] = preg_replace('#<img[^>]+src="http://eva-static.24hstatic.com/images/2014/share-gg.gif" (.*)(\\1).*>#siU','',$response['data']['content']);
        $response['data']['content'] = Core_Filter::purify($response['data']['content']);
        $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
        $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
            return Core_Utils::setImageContent($match,'http://eva.vn','eva.vn','www.eva.vn');
        }, $response['data']['content']);
        //Remove href
        $response['data']['content'] = preg_replace('#</a>#siU', '', preg_replace('#<a.*>#siU', '', $response['data']['content']));
        $response['data']['content'] .= '<p style="text-align: right;">Nguồn eva.vn</p>';
        return $response;
    }

}
