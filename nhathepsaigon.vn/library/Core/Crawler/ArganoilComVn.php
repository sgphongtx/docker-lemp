<?php

/**
 *
 * @author PhongTX
 */
class Core_Crawler_ArganoilComVn extends Core_Crawler_Abstract
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

        //get DOM
        $dom = new Zend_Dom_Query($html);
        
        /**
         * Get title 
         */
        $titleNode = $dom->query('.product_name');

        foreach ($titleNode->current()->childNodes as $child)
        {
            $response['data']['title'] .= $child->ownerDocument->saveXML($child);
        }                
        
        $response['data']['title'] = Core_Filter::purify($response['data']['title']);
        
        /**
         * Get lead 
         */
        /*$leadNode = $dom->query('.short_intro');
        
        foreach ($leadNode->current()->childNodes as $child)
        {
            $response['data']['lead'] .= $child->ownerDocument->saveXML($child);
        }
        
        $response['data']['lead'] = Core_Filter::purify($response['data']['lead']);
        /**
         * Get content 
         */
        $response['data']['lead'] = '';
        
        $contentNode = $dom->query('div.rte');

        foreach ($contentNode->current()->childNodes as $child)
        {
            $response['data']['content'] .= $child->ownerDocument->saveXML($child);
        }

        /*$response['data']['content'] = preg_replace(array(
            '#<h1[^>]*>(.*)</h1>#esiU',
            '#<h2[^>]*>(.*)</h2>#esiU'
        ), '', $response['data']['content']);*/
        
        $response['data']['content'] = Core_Filter::purify($response['data']['content']);
        //Loai bo chu thich html
        $response['data']['content'] = preg_replace('<!--(.*?)-->','',$response['data']['content']);
        //Loai bo styel html
        $response['data']['content'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1',$response['data']['content']);
        $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
        $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
            return Core_Utils::setImageContent($match,'http://arganoil.com.vn','arganoil.com.vn','www.giaitri.vnexpress.net');
        }, $response['data']['content']);
        //Remove href
        $response['data']['content'] = preg_replace('#</a>#siU', '', preg_replace('#<a.*>#siU', '', $response['data']['content']));
        $response['data']['content'] .= '<p style="text-align: right;">Nguồn arganoil.com.vn</p>';
        return $response;
    }

}
