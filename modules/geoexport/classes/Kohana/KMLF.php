<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Php class for build KML file
 *
 * @package    Gis3W
 * @category   GIS
 * @author     Walter Lorenzetti
 * @copyright  (c) 2015 Gis3W
 */

class Kohana_KMLF
{
    protected $_kml;
    public function __construct()
    {
        $this->_init();
    }

    protected function _init()
    {
        $this->_kml = '<?xml version="1.0" encoding="UTF-8"?>
                       <kml xmlns="http://www.opengis.net/kml/2.2">';

        $this->_kml .= "</kml>";
        $this->_kml= simplexml_load_string($this->_kml);
    }

    public function render()
    {
        return $this->_kml->asXML();
    }
}