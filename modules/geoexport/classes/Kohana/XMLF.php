<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Php class for build generic XML filed
 * Based on SimpleXMLElement PHP class
 *
 * @package    Gis3W
 * @category   XML
 * @author     Walter Lorenzetti
 * @copyright  (c) 2015 Gis3W
 */

class Kohana_XMLF
{
    protected $_xml;

    protected function _addSimpleNode($parent,$data,$namespace = NULL)
    {
        $parent->addChild($data[0],$data[1],$namespace);
    }

    public function render()
    {
        return $this->_xml->asXML();
    }

    public function sxml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}