<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Php class for build KML file
 *
 * @package    Gis3W
 * @category   GIS
 * @author     Walter Lorenzetti
 * @copyright  (c) 2015 Gis3W
 */

class Kohana_KMLF extends Kohana_XMLF
{
    protected function __construct()
    {
        $this->_init();
    }

    protected function _init()
    {
        $this->_xml = '<?xml version="1.0" encoding="UTF-8"?>
                       <kml xmlns="http://www.opengis.net/kml/2.2">';

        $this->_xml .= "</kml>";
        $this->_xml= simplexml_load_string($this->_xml);
    }

    public function addDocument($params)
    {
        $documentNode = $this->_xml->addChild('Document');
        foreach($params as $param)
            $this->_addSimpleNode($documentNode,$param);

        return $documentNode;
    }

    public function addPlaceMark($parent,$params)
    {
        $placemarkNode = $parent->addChild('Placemark');
        foreach($params as $param)
            $this->_addSimpleNode($placemarkNode,$param);
        return $placemarkNode;
    }

    public function addKMLString($parent,$kmlString)
    {
        if(is_string($kmlString))
            $this->sxml_append($parent, new SimpleXMLElement($kmlString));
    }

    public function render()
    {
        return $this->_xml->asXML();
    }

    public static function factory()
    {
        return new self();
    }
}