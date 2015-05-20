<?php namespace TopBetta\Helpers;

/**
 * Coded by Oliver Shanahan
 * File creation date: 13/03/15
 * File creation time: 11:56
 * Project: tb4
 */

use SimpleXMLElement;

class LibSimpleXMLElement extends SimpleXMLElement{

    function addChild($name, $value = null, $namespace = null) {
        $tmpNode = parent::addChild($name,null,$namespace);
        $tmpNode->{0} = $value;
        return $tmpNode;
    }
}