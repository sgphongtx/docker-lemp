<?php

/**
 *
 * @author PhongTX
 */
class Core_Crawler_VmodeVn extends Core_Crawler_Abstract
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
        $titleNode = $dom->query('h1.entry-title');

        foreach ($titleNode->current()->childNodes as $child)
        {
            $response['data']['title'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['title'] = Core_Filter::purify($response['data']['title']);

        /**
         * Get lead
         */
        $leadNode = $dom->query('#content p strong');

        foreach ($leadNode->current()->childNodes as $child)
        {
            $response['data']['lead'] .= $child->ownerDocument->saveXML($child);
        }

        $response['data']['lead'] = Core_Filter::purify($response['data']['lead']);

        /**
         * Get content 
         */                
        
        $contentNode = $dom->query('#content .entry');

        foreach ($contentNode->current()->childNodes as $child)
        {
            $response['data']['content'] .= $child->ownerDocument->saveXML($child);
        }
        //Loai bo chu thich html
        $response['data']['content'] = preg_replace('<!--(.*?)-->','',$response['data']['content']);
        //Loai bo styel html
        $response['data']['content'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<div class="post_tools">([\\S\\s]*?)<\/div>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<span class="entrydate pull-left">([\\S\\s]*?)<\/span>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<span class="entryby metaitem pull-right">([\\S\\s]*?)<\/span>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<span class="entrytag metaitem pull-right">([\\S\\s]*?)<\/span>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<span class="post_topmeta">([\\S\\s]*?)<\/span>/','',$response['data']['content']);
        $response['data']['content'] = preg_replace('/<h1 class="entry-title">([\\S\\s]*?)<\/h1>/','',$response['data']['content']);

        $response['data']['content'] = Core_Filter::purify($response['data']['content']);

        $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
        $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
            return Core_Utils::setImageContent($match,'http://vmode.vn','vmode.vn','www.vmode.vn');
        }, $response['data']['content']);
        //Remove href
        $response['data']['content'] = preg_replace('#</a>#siU', '', preg_replace('#<a.*>#siU', '', $response['data']['content']));
        $response['data']['content'] .= '<p style="text-align: right;">Nguồn vmode.vn</p>';
        return $response;        
    }

}
