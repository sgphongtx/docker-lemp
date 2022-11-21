<?php

/**
 * 
 * @author PhongTX
 */
class Core_Crawler_PhongCachSaoCom extends Core_Crawler_Abstract
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
        $titleNode = $dom->query('h3.post-title a');

        foreach ($titleNode->current()->childNodes as $child)
        {
            $response['data']['title'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['title'] = Core_Filter::purify($response['data']['title']);

        /**
         * Get lead
         */
        $leadNode = $dom->query('div[dir="ltr"] b');

        foreach ($leadNode->current()->childNodes as $child)
        {
            $response['data']['lead'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['lead'] = str_ireplace('PHONGCACHSAO - ','',Core_Filter::purify($response['data']['lead']));
        /**
         * Get content
         */

        $contentNode = $dom->query('div[dir="ltr"]');

        foreach ($contentNode->current()->childNodes as $child)
        {
            $response['data']['content'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['content'] = str_ireplace('PHONGCACHSAO - ','',str_ireplace($response['data']['lead'],'',Core_Filter::purify($response['data']['content'])));
        //Loai bo chu thich html
        $response['data']['content'] = preg_replace('<!--(.*?)-->','',$response['data']['content']);
        //Loai bo styel html
        $response['data']['content'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="clear: both;"><\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="width:100%;background:#f5f5f5;border:1px solid #ddd;text-align:center;padding:10px 0;">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="float:right;padding:10px 0;">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="width:100%;background:#f5f5f5;border:1px solid #ddd;text-align:center;padding:10px 0;">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="width:700px;margin:0 0 10px 0;padding:5px 0;">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div style="margin-top:10px" id="related-posts">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
        $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
            return Core_Utils::setImageContent($match,'http://phongcachsao.com','phongcachsao.com','www.phongcachsao.com');
        }, $response['data']['content']);
        //Remove href
        $response['data']['content'] = preg_replace('#</a>#siU', '', preg_replace('#<a.*>#siU', '', $response['data']['content']));
        $response['data']['content'] .= '<p style="text-align: right;">Nguồn phongcachsao.com</p>';
        return $response;
    }

}
