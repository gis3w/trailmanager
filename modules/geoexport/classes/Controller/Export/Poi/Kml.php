<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Poi_Kml extends Controller_Export_Base_Kml{

    protected $_poi;

    public function action_index()
    {

        #get the sepcific path
        $this->_poi = ORMGIS::factory('Poi', $this->request->param('id'));
        if(!$this->_poi->publish)
            throw new HTTP_Exception_500('Sorry Poi id not availabled');
        #star GPXF Obeject
        //set the filename
        $this->filename = 'Poi_'.Inflector::underscore($this->_poi->title).'_'.time().'.kml';
        $this->kml = KMLF::factory();

        $document = $this->kml->addDocument([
            ['name',$this->_poi->title],
            ['description',strip_tags($this->_poi->description)]
        ]);
        

        $placemark = $this->kml->addPlaceMark($document,[
            ['name',$this->_poi->title],
            ['description',strip_tags($this->_poi->description)],
        ]);

        $this->kml->addKMLString($placemark,$this->_poi->getKML());



    }



}