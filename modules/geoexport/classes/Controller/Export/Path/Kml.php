<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Path_Kml extends Controller_Export_Base_Kml{

    protected $_path;

    public function action_index()
    {

        #get the sepcific path
        $this->_path = ORMGIS::factory('Path', $this->request->param('id'));
        if(!$this->_path->publish)
            throw new HTTP_Exception_500('Sorry Path id not availabled');
        #star GPXF Obeject
        //set the filename
        $this->filename = 'Path_'.Inflector::underscore($this->_path->title).'_'.time().'.kml';
        $this->kml = KMLF::factory();

        $document = $this->kml->addDocument([
            ['name',$this->_path->title],
            ['description',$this->_path->description]
        ]);

        $placemark = $this->kml->addPlaceMark($document,[
            ['name',$this->_path->title],
            ['description',$this->_path->description]
        ]);

        $this->kml->addKMLString($placemark,$this->_path->getKML());



    }



}