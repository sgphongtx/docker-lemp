<?php

class HTMLPurifier_AttrTransform_SafeEmbed extends HTMLPurifier_AttrTransform
{
    public $name = "SafeEmbed";

    public function transform($attr, $config, $context) {
        $attr['allowScriptAccess'] = 'always';       
        $attr['allowFullScreen'] = 'true';        
        //$attr['allownetworking'] = 'external';
        $attr['type'] = 'application/x-shockwave-flash';
        return $attr;
    }
}

// vim: et sw=4 sts=4
