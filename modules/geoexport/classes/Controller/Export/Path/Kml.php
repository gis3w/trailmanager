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
        $this->filename = __('Path').'_'.Inflector::underscore($this->_path->nome).'_'.date('Ymd-Hi',time()).'.kml';
        $this->kml = KMLF::factory();

        $document = $this->kml->addDocument([
            ['name',$this->_path->nome],
            ['description',strip_tags($this->_path->descriz)]
        ]);

        $style = $this->kml->addStyle($document,'pathStyle');
        $this->kml->addLineStyle($style,[
            ['color',KMLF::fromRGB2KMLColor(substr($this->_path->color,1))],
            ['width',$this->_path->width]
        ]);

        $placemark = $this->kml->addPlaceMark($document,[
            ['name',$this->_path->nome],
            ['description',strip_tags($this->_path->descriz)],
            ['styleUrl','#pathStyle']
        ]);

        $this->kml->addKMLString($placemark,$this->_path->getKML());



    }



}