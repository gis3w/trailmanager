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

    /**
     * From RGB string color retunr KMl collor string
     * @param $rgbColor string rgb
     * @param string $opacity km color
     */
    public static function fromRGB2KMLColor($rgbColor,$opacity = 'ff')
    {
        $R = substr($rgbColor,0,2);
        $G = substr($rgbColor,2,2);
        $B = substr($rgbColor,4,2);

        return strtolower($opacity.$B.$G.$R);

    }

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

    public function addStyle($parent,$idStyle)
    {
        $styleNode = $parent->addChild('Style');
        $styleNode->addAttribute('id',$idStyle);
        return $styleNode;
    }

    public function addLineStyle($parent,$params)
    {
        $lineStyleNode = $parent->addChild('LineStyle');
        foreach($params as $param)
            $this->_addSimpleNode($lineStyleNode,$param);
        return $lineStyleNode;
    }

    public function addIconStyle($parent,$params)
    {
        $iconStyleNode = $parent->addChild('IconStyle');
        foreach($params as $param)
            $this->_addSimpleNode($iconStyleNode,$param);
        return $iconStyleNode;
    }

    public function addIcon($parent,$params)
    {
        $iconNode = $parent->addChild('Icon');
        foreach($params as $param)
            $this->_addSimpleNode($iconNode,$param);
        return $iconNode;
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