<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Poi_Gpx extends Controller_Export_Base_Gpx{

    protected $_poi;

    public function action_index()
    {

        #get the sepcific path
        $this->_poi = ORMGIS::factory('Poi', $this->request->param('id'));
        if(!$this->_poi->publish)
            throw new HTTP_Exception_500('Sorry Poi is not disponibled');
        #star GPXF Obeject
        //set the filename
        $this->filename = 'Poi_'.Inflector::underscore($this->_poi->title).'_'.time().'.gpx';
        $this->gpx = GPXF::factory(GPXF::MODE_FILE,$this->filename);

        $properties = [
            ['name',$this->_poi->title],
            ['desc',$this->_cleanText($this->_poi->description)],
        ];
        $this->_poi->getLonLat(4326);
        $wpt = $this->gpx->addWpt($this->_poi->lat,$this->_poi->lon,$properties);
        $this->gpx->addToWptFile($wpt);

        $this->_update_global_bounds($this->_poi->lat,$this->_poi->lon);

        if(!empty($this->global_bounds))
            $this->gpx->addBounds(NULL,$this->global_bounds);
        $this->file = $this->gpx->render();


    }



}